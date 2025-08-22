<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\{EventLog, User, Device};
use App\Enums\{EventType, DeviceStatus};
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventLogTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Device $device;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->device = Device::factory()->create(['user_id' => $this->user->id]);
    }

    /** @test */
    public function it_can_scope_by_type()
    {
        EventLog::factory()->create(['type' => EventType::DeviceOnline]);
        EventLog::factory()->create(['type' => EventType::DeviceOffline]);
        EventLog::factory()->create(['type' => EventType::TicketCreated]);

        $deviceEvents = EventLog::byType(EventType::DeviceOnline)->get();
        $this->assertEquals(1, $deviceEvents->count());
    }

    /** @test */
    public function it_can_scope_by_subject()
    {
        EventLog::factory()->create([
            'subject_type' => 'device',
            'subject_id' => $this->device->id,
        ]);
        EventLog::factory()->create([
            'subject_type' => 'user',
            'subject_id' => $this->user->id,
        ]);

        $deviceEvents = EventLog::bySubject('device', $this->device->id)->get();
        $this->assertEquals(1, $deviceEvents->count());
    }

    /** @test */
    public function it_can_scope_by_actor()
    {
        EventLog::factory()->create([
            'actor_type' => 'user',
            'actor_id' => $this->user->id,
        ]);
        EventLog::factory()->create([
            'actor_type' => 'device',
            'actor_id' => $this->device->id,
        ]);

        $userEvents = EventLog::byActor('user', $this->user->id)->get();
        $this->assertEquals(1, $userEvents->count());
    }

    /** @test */
    public function it_can_scope_by_time_range()
    {
        $oldEvent = EventLog::factory()->create([
            'occurred_at' => now()->subDays(5),
        ]);
        $recentEvent = EventLog::factory()->create([
            'occurred_at' => now()->subHours(2),
        ]);

        $recentEvents = EventLog::inTimeRange(now()->subDay(), now())->get();
        $this->assertEquals(1, $recentEvents->count());
        $this->assertEquals($recentEvent->id, $recentEvents->first()->id);
    }

    /** @test */
    public function it_is_append_only()
    {
        $eventLog = EventLog::factory()->create();

        // Should not allow updates
        $this->assertFalse($eventLog->update(['message' => 'Updated message']));
    }

    /** @test */
    public function it_prevents_deletion()
    {
        $eventLog = EventLog::factory()->create();

        // Should not allow deletion
        $this->assertFalse($eventLog->delete());
    }

    /** @test */
    public function it_casts_type_to_enum()
    {
        $eventLog = EventLog::factory()->create([
            'type' => EventType::DeviceOnline,
        ]);

        $this->assertInstanceOf(EventType::class, $eventLog->type);
        $this->assertEquals(EventType::DeviceOnline, $eventLog->type);
    }

    /** @test */
    public function it_casts_context_to_array()
    {
        $context = ['key' => 'value', 'nested' => ['data' => 'test']];
        
        $eventLog = EventLog::factory()->create([
            'context' => $context,
        ]);

        $this->assertIsArray($eventLog->context);
        $this->assertEquals($context, $eventLog->context);
    }

    /** @test */
    public function it_casts_occurred_at_to_datetime()
    {
        $eventLog = EventLog::factory()->create([
            'occurred_at' => now(),
        ]);

        $this->assertInstanceOf(\Carbon\CarbonImmutable::class, $eventLog->occurred_at);
    }
}
