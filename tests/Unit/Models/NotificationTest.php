<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\{Notification, User};
use App\Enums\{NotificationType, NotificationStatus};
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Notification $notification;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->notification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'status' => NotificationStatus::Pending,
            'read_at' => now(), // Ensure it's read so it doesn't interfere with unread tests
            'type' => NotificationType::SystemAlert, // Use a different type to avoid conflicts
            'channel' => 'sms', // Use a different channel to avoid conflicts
        ]);
    }

    /** @test */
    public function it_has_relationships()
    {
        $this->assertInstanceOf(User::class, $this->notification->user);
        $this->assertEquals($this->user->id, $this->notification->user->id);
    }

    /** @test */
    public function it_can_mark_as_read()
    {
        $this->notification->markAsRead();

        $this->assertNotNull($this->notification->fresh()->read_at);
    }

    /** @test */
    public function it_can_mark_as_delivered()
    {
        $this->notification->markAsDelivered();

        $this->assertEquals(NotificationStatus::Delivered, $this->notification->fresh()->status);
        $this->assertNotNull($this->notification->fresh()->delivered_at);
    }

    /** @test */
    public function it_can_mark_as_failed()
    {
        $this->notification->markAsFailed();

        $this->assertEquals(NotificationStatus::Failed, $this->notification->fresh()->status);
        $this->assertNotNull($this->notification->fresh()->failed_at);
        $this->assertEquals(1, $this->notification->fresh()->retry_count);
    }

    /** @test */
    public function it_can_check_if_can_retry()
    {
        $this->assertFalse($this->notification->canRetry());

        $this->notification->markAsFailed();
        $this->assertTrue($this->notification->canRetry());

        // Set retry count to max
        $this->notification->forceFill(['retry_count' => 3])->save();
        $this->assertFalse($this->notification->canRetry());
    }

    /** @test */
    public function it_can_check_if_expired()
    {
        $this->assertFalse($this->notification->isExpired());

        $this->notification->forceFill(['expires_at' => now()->subHour()])->save();
        $this->assertTrue($this->notification->isExpired());
    }

    /** @test */
    public function it_can_scope_pending()
    {
        Notification::factory()->create(['status' => NotificationStatus::Sent]);
        Notification::factory()->create(['status' => NotificationStatus::Pending]);

        $pendingNotifications = Notification::pending()->get();
        $this->assertEquals(2, $pendingNotifications->count()); // Including the one from setUp
    }

    /** @test */
    public function it_can_scope_unread()
    {
        $readNotification = Notification::factory()->create(['read_at' => now()]);
        $unreadNotification = Notification::factory()->create(['read_at' => null]);

        $unreadNotifications = Notification::unread()->get();
        $this->assertEquals(1, $unreadNotifications->count()); // Only the one we just created with read_at = null
    }

    /** @test */
    public function it_can_scope_by_type()
    {
        Notification::factory()->create(['type' => NotificationType::DeviceOffline]);
        Notification::factory()->create(['type' => NotificationType::TicketAssigned]);

        $deviceNotifications = Notification::byType(NotificationType::DeviceOffline)->get();
        $this->assertEquals(1, $deviceNotifications->count()); // Only the one we just created
    }

    /** @test */
    public function it_can_scope_by_channel()
    {
        Notification::factory()->create(['channel' => 'email']);
        Notification::factory()->create(['channel' => 'websocket']);

        $emailNotifications = Notification::byChannel('email')->get();
        $this->assertEquals(1, $emailNotifications->count()); // Only the one we just created with email channel
    }

    /** @test */
    public function it_can_scope_expired()
    {
        Notification::factory()->create(['expires_at' => now()->subHour()]);
        Notification::factory()->create(['expires_at' => now()->addHour()]);

        $expiredNotifications = Notification::expired()->get();
        $this->assertEquals(1, $expiredNotifications->count());
    }

    /** @test */
    public function it_can_scope_retryable()
    {
        // Failed notification with retries left
        Notification::factory()->create([
            'status' => NotificationStatus::Failed,
            'retry_count' => 1,
            'max_retries' => 3,
        ]);

        // Failed notification with no retries left
        Notification::factory()->create([
            'status' => NotificationStatus::Failed,
            'retry_count' => 3,
            'max_retries' => 3,
        ]);

        $retryableNotifications = Notification::retryable()->get();
        $this->assertEquals(1, $retryableNotifications->count());
    }
}
