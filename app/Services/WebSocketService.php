<?php declare(strict_types=1);

namespace App\Services;

use App\Models\{Device, PowerRecord, Alert, Notification, User};
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class WebSocketService
{
    private Pusher $pusher;

    public function __construct()
    {
        $this->pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );
    }

    public function broadcastDeviceUpdate(Device $device): void
    {
        $data = [
            'device_id' => $device->id,
            'status' => $device->status->value,
            'last_seen_at' => $device->last_seen_at?->toISOString(),
            'updated_at' => $device->updated_at->toISOString(),
        ];

        $this->pusher->trigger(
            "user.{$device->user_id}",
            'device.updated',
            $data
        );

        Log::info('WebSocket: Device update broadcasted', [
            'device_id' => $device->id,
            'user_id' => $device->user_id,
        ]);
    }

    public function broadcastPowerData(PowerRecord $record): void
    {
        $data = [
            'device_id' => $record->device_id,
            'timestamp' => $record->ts->toISOString(),
            'current' => $record->current,
            'voltage' => $record->voltage,
            'power' => $record->power,
            'phase' => $record->phase,
        ];

        $this->pusher->trigger(
            "device.{$record->device_id}",
            'power.data',
            $data
        );

        Log::info('WebSocket: Power data broadcasted', [
            'device_id' => $record->device_id,
            'power' => $record->power,
        ]);
    }

    public function broadcastAlert(Alert $alert): void
    {
        $data = [
            'alert_id' => $alert->id,
            'type' => $alert->type->value,
            'title' => $alert->title,
            'message' => $alert->message,
            'status' => $alert->status->value,
            'device_id' => $alert->device_id,
            'triggered_at' => $alert->triggered_at->toISOString(),
            'escalation_level' => $alert->escalation_level,
        ];

        $this->pusher->trigger(
            "user.{$alert->user_id}",
            'alert.triggered',
            $data
        );

        Log::info('WebSocket: Alert broadcasted', [
            'alert_id' => $alert->id,
            'user_id' => $alert->user_id,
        ]);
    }

    public function broadcastNotification(Notification $notification): void
    {
        $data = [
            'notification_id' => $notification->id,
            'type' => $notification->type->value,
            'title' => $notification->title,
            'message' => $notification->message,
            'priority' => $notification->priority,
            'channel' => $notification->channel,
            'created_at' => $notification->created_at->toISOString(),
        ];

        $this->pusher->trigger(
            "user.{$notification->user_id}",
            'notification.received',
            $data
        );

        Log::info('WebSocket: Notification broadcasted', [
            'notification_id' => $notification->id,
            'user_id' => $notification->user_id,
        ]);
    }

    public function broadcastTicketUpdate($ticket): void
    {
        $data = [
            'ticket_id' => $ticket->id,
            'status' => $ticket->status->value,
            'priority' => $ticket->priority->value,
            'title' => $ticket->title,
            'updated_at' => $ticket->updated_at->toISOString(),
        ];

        // Broadcast to ticket creator
        $this->pusher->trigger(
            "user.{$ticket->user_id}",
            'ticket.updated',
            $data
        );

        // Broadcast to ticket assignee if different
        if ($ticket->assignee_id && $ticket->assignee_id !== $ticket->user_id) {
            $this->pusher->trigger(
                "user.{$ticket->assignee_id}",
                'ticket.updated',
                $data
            );
        }

        Log::info('WebSocket: Ticket update broadcasted', [
            'ticket_id' => $ticket->id,
        ]);
    }

    public function broadcastChatMessage($message): void
    {
        $data = [
            'message_id' => $message->id,
            'ticket_id' => $message->ticket_id,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender_name,
            'content' => $message->content,
            'created_at' => $message->created_at->toISOString(),
        ];

        $this->pusher->trigger(
            "ticket.{$message->ticket_id}",
            'chat.message',
            $data
        );

        Log::info('WebSocket: Chat message broadcasted', [
            'message_id' => $message->id,
            'ticket_id' => $message->ticket_id,
        ]);
    }

    public function broadcastSystemStatus(): void
    {
        $data = [
            'timestamp' => now()->toISOString(),
            'online_devices' => Device::where('status', 'online')->count(),
            'total_devices' => Device::count(),
            'active_alerts' => Alert::where('status', 'active')->count(),
            'pending_notifications' => Notification::where('status', 'pending')->count(),
        ];

        $this->pusher->trigger(
            'system',
            'status.update',
            $data
        );

        Log::info('WebSocket: System status broadcasted', $data);
    }

    public function subscribeUserToChannels(User $user): array
    {
        return [
            "user.{$user->id}",
            "system",
        ];
    }

    public function subscribeDeviceToChannels(Device $device): array
    {
        return [
            "device.{$device->id}",
            "user.{$device->user_id}",
        ];
    }
}
