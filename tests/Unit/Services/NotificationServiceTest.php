<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\{NotificationService, RabbitMQService};
use App\Models\{User, Device, Ticket, Notification};
use App\Enums\{NotificationType, NotificationStatus};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private NotificationService $notificationService;
    /** @var RabbitMQService&\Mockery\MockInterface */
    private RabbitMQService $rabbitMQ;
    private User $user;
    private Device $device;
    private Ticket $ticket;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->rabbitMQ = Mockery::mock(RabbitMQService::class, ['publishNotification', 'publishChatMessage']);
        $this->notificationService = new NotificationService($this->rabbitMQ);
        
        $this->user = User::factory()->create();
        $this->device = Device::factory()->create(['user_id' => $this->user->id]);
        $this->ticket = Ticket::factory()->create([
            'user_id' => $this->user->id,
            'device_id' => $this->device->id,
        ]);
    }

    /** @test */
    public function it_can_create_notification()
    {
        $this->rabbitMQ->shouldReceive('publishNotification')
            ->once()
            ->withArgs(function ($data, $routingKey) {
                return $data['type'] === NotificationType::DeviceOffline->value &&
                       $routingKey === 'notification.device.offline';
            });

        $notification = $this->notificationService->createNotification(
            user: $this->user,
            type: NotificationType::DeviceOffline,
            title: 'Device Offline',
            message: 'Device has gone offline',
            data: ['device_id' => $this->device->id],
            channel: 'websocket',
            priority: 2
        );

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertEquals($this->user->id, $notification->user_id);
        $this->assertEquals(NotificationType::DeviceOffline, $notification->type);
        $this->assertEquals('Device Offline', $notification->title);
        $this->assertEquals('Device has gone offline', $notification->message);
        $this->assertEquals(['device_id' => $this->device->id], $notification->data);
        $this->assertEquals('websocket', $notification->channel);
        $this->assertEquals(2, $notification->priority);
        $this->assertEquals(NotificationStatus::Pending, $notification->status);
    }

    /** @test */
    public function it_can_notify_device_offline()
    {
        $this->rabbitMQ->shouldReceive('publishNotification')
            ->once()
            ->withArgs(function ($data, $routingKey) {
                return $data['type'] === NotificationType::DeviceOffline->value &&
                       $data['data']['device_id'] === $this->device->id &&
                       $routingKey === 'notification.device.offline';
            });

        $this->notificationService->notifyDeviceOffline($this->device);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->user->id,
            'type' => NotificationType::DeviceOffline->value,
            'title' => 'Device Offline',
            'message' => "Device '{$this->device->name}' has gone offline",
            'priority' => 2,
        ]);
    }

    /** @test */
    public function it_can_notify_device_online()
    {
        $this->rabbitMQ->shouldReceive('publishNotification')
            ->once()
            ->withArgs(function ($data, $routingKey) {
                return $data['type'] === NotificationType::DeviceOnline->value &&
                       $data['data']['device_id'] === $this->device->id &&
                       $routingKey === 'notification.device.online';
            });

        $this->notificationService->notifyDeviceOnline($this->device);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->user->id,
            'type' => NotificationType::DeviceOnline->value,
            'title' => 'Device Online',
            'message' => "Device '{$this->device->name}' is back online",
            'priority' => 1,
        ]);
    }

    /** @test */
    public function it_can_notify_power_threshold()
    {
        $this->rabbitMQ->shouldReceive('publishNotification')
            ->once()
            ->withArgs(function ($data, $routingKey) {
                return $data['type'] === NotificationType::PowerThreshold->value &&
                       $data['data']['power'] === 500.0 &&
                       $data['data']['threshold'] === 400.0 &&
                       $routingKey === 'notification.power.threshold';
            });

        $this->notificationService->notifyPowerThreshold($this->device, 500.0, 400.0);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->user->id,
            'type' => NotificationType::PowerThreshold->value,
            'title' => 'Power Threshold Exceeded',
            'message' => "Device '{$this->device->name}' power consumption (500W) exceeded threshold (400W)",
            'priority' => 3,
        ]);
    }

    /** @test */
    public function it_can_notify_ticket_assigned()
    {
        $assignee = User::factory()->create();
        $this->ticket->update(['assignee_id' => $assignee->id]);

        $this->rabbitMQ->shouldReceive('publishNotification')
            ->once()
            ->withArgs(function ($data, $routingKey) use ($assignee) {
                return $data['type'] === NotificationType::TicketAssigned->value &&
                       $data['user_id'] === $assignee->id &&
                       $routingKey === 'notification.ticket.assigned';
            });

        $this->notificationService->notifyTicketAssigned($this->ticket);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $assignee->id,
            'type' => NotificationType::TicketAssigned->value,
            'title' => 'Ticket Assigned',
            'message' => "You have been assigned to ticket #{$this->ticket->id}",
            'priority' => 2,
        ]);
    }

    /** @test */
    public function it_does_not_notify_ticket_assigned_when_no_assignee()
    {
        // Ensure ticket has no assignee
        $this->ticket->update(['assignee_id' => null]);
        
        $this->rabbitMQ->shouldNotReceive('publishNotification');

        $this->notificationService->notifyTicketAssigned($this->ticket);

        $this->assertDatabaseMissing('notifications', [
            'type' => NotificationType::TicketAssigned->value,
        ]);
    }

    /** @test */
    public function it_can_notify_ticket_updated()
    {
        $assignee = User::factory()->create();
        $this->ticket->update(['assignee_id' => $assignee->id]);

        $this->rabbitMQ->shouldReceive('publishNotification')
            ->twice() // Once for creator, once for assignee
            ->withArgs(function ($data, $routingKey) {
                return $data['type'] === NotificationType::TicketUpdated->value &&
                       $routingKey === 'notification.ticket.updated';
            });

        $this->notificationService->notifyTicketUpdated($this->ticket, 'Status changed to closed');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->user->id,
            'type' => NotificationType::TicketUpdated->value,
            'title' => 'Ticket Updated',
            'message' => "Ticket #{$this->ticket->id} has been updated: Status changed to closed",
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $assignee->id,
            'type' => NotificationType::TicketUpdated->value,
            'title' => 'Ticket Updated',
            'message' => "Ticket #{$this->ticket->id} has been updated: Status changed to closed",
        ]);
    }

    /** @test */
    public function it_can_send_chat_message()
    {
        $assignee = User::factory()->create();
        $this->ticket->update(['assignee_id' => $assignee->id]);

        $this->rabbitMQ->shouldReceive('publishChatMessage')
            ->once()
            ->withArgs(function ($data, $routingKey) use ($assignee) {
                return $data['ticket_id'] === $this->ticket->id &&
                       $data['sender_id'] === $this->user->id &&
                       $data['recipient_id'] === $assignee->id &&
                       $data['message'] === 'Hello, can you help me?' &&
                       $routingKey === "chat.ticket.{$this->ticket->id}";
            });

        $this->notificationService->sendChatMessage($this->ticket, $this->user, 'Hello, can you help me?');
        
        $this->assertTrue(true); // Test passes if no exception is thrown
    }

    /** @test */
    public function it_can_mark_notification_as_delivered()
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'status' => NotificationStatus::Pending,
        ]);

        $this->notificationService->markNotificationAsDelivered($notification->id);

        $this->assertEquals(NotificationStatus::Delivered, $notification->fresh()->status);
        $this->assertNotNull($notification->fresh()->delivered_at);
    }

    /** @test */
    public function it_can_mark_notification_as_failed()
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'status' => NotificationStatus::Pending,
        ]);

        $this->notificationService->markNotificationAsFailed($notification->id);

        $this->assertEquals(NotificationStatus::Failed, $notification->fresh()->status);
        $this->assertNotNull($notification->fresh()->failed_at);
        $this->assertEquals(1, $notification->fresh()->retry_count);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
