<?php declare(strict_types=1);

namespace App\Services;

use App\Models\{Notification, User, Device, Ticket};
use App\Enums\{NotificationType, NotificationStatus};
use Illuminate\Support\Facades\Log;

class NotificationService
{
    private RabbitMQService $rabbitMQ;

    public function __construct(RabbitMQService $rabbitMQ)
    {
        $this->rabbitMQ = $rabbitMQ;
    }

    public function createNotification(
        User $user,
        NotificationType $type,
        string $title,
        string $message,
        array $data = [],
        string $channel = 'websocket',
        int $priority = 0
    ): Notification {
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'status' => NotificationStatus::Pending,
            'channel' => $channel,
            'priority' => $priority,
            'max_retries' => 3,
        ]);

        // Publish to RabbitMQ for immediate delivery
        $this->rabbitMQ->publishNotification([
            'notification_id' => $notification->id,
            'user_id' => $user->id,
            'type' => $type->value,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'channel' => $channel,
            'priority' => $priority,
        ], "notification.{$type->value}");

        return $notification;
    }

    public function notifyDeviceOffline(Device $device): void
    {
        $user = $device->user;
        
        $this->createNotification(
            user: $user,
            type: NotificationType::DeviceOffline,
            title: 'Device Offline',
            message: "Device '{$device->name}' has gone offline",
            data: [
                'device_id' => $device->id,
                'device_name' => $device->name,
                'last_seen_at' => $device->last_seen_at?->toISOString(),
            ],
            priority: 2
        );
    }

    public function notifyDeviceOnline(Device $device): void
    {
        $user = $device->user;
        
        $this->createNotification(
            user: $user,
            type: NotificationType::DeviceOnline,
            title: 'Device Online',
            message: "Device '{$device->name}' is back online",
            data: [
                'device_id' => $device->id,
                'device_name' => $device->name,
            ],
            priority: 1
        );
    }

    public function notifyPowerThreshold(Device $device, float $power, float $threshold): void
    {
        $user = $device->user;
        
        $this->createNotification(
            user: $user,
            type: NotificationType::PowerThreshold,
            title: 'Power Threshold Exceeded',
            message: "Device '{$device->name}' power consumption ({$power}W) exceeded threshold ({$threshold}W)",
            data: [
                'device_id' => $device->id,
                'device_name' => $device->name,
                'power' => $power,
                'threshold' => $threshold,
            ],
            priority: 3
        );
    }

    public function notifyTicketAssigned(Ticket $ticket): void
    {
        if (!$ticket->assignee) {
            return;
        }

        $this->createNotification(
            user: $ticket->assignee,
            type: NotificationType::TicketAssigned,
            title: 'Ticket Assigned',
            message: "You have been assigned to ticket #{$ticket->id}",
            data: [
                'ticket_id' => $ticket->id,
                'device_id' => $ticket->device_id,
                'device_name' => $ticket->device?->name,
                'priority' => $ticket->priority->value,
            ],
            priority: 2
        );
    }

    public function notifyTicketUpdated(Ticket $ticket, string $updateMessage): void
    {
        $users = collect([$ticket->creator, $ticket->assignee])->filter();
        
        foreach ($users as $user) {
            $this->createNotification(
                user: $user,
                type: NotificationType::TicketUpdated,
                title: 'Ticket Updated',
                message: "Ticket #{$ticket->id} has been updated: {$updateMessage}",
                data: [
                    'ticket_id' => $ticket->id,
                    'device_id' => $ticket->device_id,
                    'device_name' => $ticket->device?->name,
                    'update_message' => $updateMessage,
                ],
                priority: 1
            );
        }
    }

    public function sendChatMessage(Ticket $ticket, User $sender, string $message): void
    {
        $recipients = collect([$ticket->creator, $ticket->assignee])
            ->filter(fn($user) => $user && $user->id !== $sender->id);

        foreach ($recipients as $recipient) {
            $this->rabbitMQ->publishChatMessage([
                'ticket_id' => $ticket->id,
                'sender_id' => $sender->id,
                'sender_name' => $sender->name,
                'recipient_id' => $recipient->id,
                'message' => $message,
                'timestamp' => now()->toISOString(),
            ], "chat.ticket.{$ticket->id}");
        }
    }

    public function markNotificationAsDelivered(int $notificationId): void
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->markAsDelivered();
        }
    }

    public function markNotificationAsFailed(int $notificationId): void
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->markAsFailed();
        }
    }
}
