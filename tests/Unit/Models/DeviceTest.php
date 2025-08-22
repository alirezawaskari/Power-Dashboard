<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\{Device, User, PowerRecord, EventLog};
use App\Enums\{DeviceStatus, EventType};
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeviceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Device $device;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->device = Device::factory()->create([
            'user_id' => $this->user->id,
            'status' => DeviceStatus::Offline,
        ]);
    }

    /** @test */
    public function it_can_mark_device_online()
    {
        $this->device->markOnline();

        $this->assertEquals(DeviceStatus::Online, $this->device->fresh()->status);
        $this->assertNotNull($this->device->fresh()->last_seen_at);
        
        // Check event log was created
        $this->assertDatabaseHas('event_logs', [
            'type' => EventType::DeviceOnline,
            'subject_type' => 'device',
            'subject_id' => $this->device->id,
        ]);
    }

    /** @test */
    public function it_can_mark_device_offline()
    {
        $this->device->markOnline();
        $this->device->markOffline();

        $this->assertEquals(DeviceStatus::Offline, $this->device->fresh()->status);
        
        // Check event log was created
        $this->assertDatabaseHas('event_logs', [
            'type' => EventType::DeviceOffline,
            'subject_type' => 'device',
            'subject_id' => $this->device->id,
        ]);
    }

    /** @test */
    public function it_can_set_maintenance_mode()
    {
        $this->device->setMaintenance(true);

        $this->assertEquals(DeviceStatus::Maintenance, $this->device->fresh()->status);
        
        // Check event log was created
        $this->assertDatabaseHas('event_logs', [
            'type' => EventType::DeviceMaintenanceOn,
            'subject_type' => 'device',
            'subject_id' => $this->device->id,
        ]);
    }

    /** @test */
    public function it_can_remove_maintenance_mode()
    {
        $this->device->setMaintenance(true);
        $this->device->setMaintenance(false);

        $this->assertEquals(DeviceStatus::Offline, $this->device->fresh()->status);
        
        // Check event log was created
        $this->assertDatabaseHas('event_logs', [
            'type' => EventType::DeviceMaintenanceOff,
            'subject_type' => 'device',
            'subject_id' => $this->device->id,
        ]);
    }

    /** @test */
    public function it_can_decommission_device()
    {
        $this->device->decommission();

        $this->assertEquals(DeviceStatus::Decommissioned, $this->device->fresh()->status);
        
        // Check event log was created
        $this->assertDatabaseHas('event_logs', [
            'type' => EventType::DeviceDecommissioned,
            'subject_type' => 'device',
            'subject_id' => $this->device->id,
        ]);
    }

    /** @test */
    public function it_cannot_mark_decommissioned_device_online()
    {
        $this->device->decommission();

        $this->expectException(\InvalidArgumentException::class);
        $this->device->markOnline();
    }

    /** @test */
    public function it_cannot_set_maintenance_on_decommissioned_device()
    {
        $this->device->decommission();

        $this->expectException(\InvalidArgumentException::class);
        $this->device->setMaintenance(true);
    }

    /** @test */
    public function it_has_relationships()
    {
        $this->assertInstanceOf(User::class, $this->device->user);
        $this->assertEquals($this->user->id, $this->device->user->id);
    }

    /** @test */
    public function it_can_scope_by_status()
    {
        Device::factory()->create(['status' => DeviceStatus::Online]);
        Device::factory()->create(['status' => DeviceStatus::Offline]);

        $onlineDevices = Device::status(DeviceStatus::Online)->get();
        $offlineDevices = Device::status(DeviceStatus::Offline)->get();

        $this->assertEquals(1, $onlineDevices->count());
        $this->assertEquals(2, $offlineDevices->count()); // Including the one from setUp
    }

    /** @test */
    public function it_can_check_if_should_be_offline()
    {
        // Device should not be offline if it's not online
        $this->assertFalse($this->device->shouldBeOffline());

        // Mark device online with recent heartbeat
        $this->device->markOnline();
        $this->assertFalse($this->device->shouldBeOffline());

        // Mark device online with old heartbeat
        $this->device->forceFill(['last_seen_at' => now()->subMinutes(10)])->save();
        $this->assertTrue($this->device->shouldBeOffline());
    }

    /** @test */
    public function it_has_status_helper_methods()
    {
        $this->assertFalse($this->device->isOnline());
        $this->assertTrue($this->device->isOffline());
        $this->assertFalse($this->device->isInMaintenance());
        $this->assertFalse($this->device->isDecommissioned());

        $this->device->markOnline();
        $this->assertTrue($this->device->fresh()->isOnline());
        $this->assertFalse($this->device->fresh()->isOffline());

        $this->device->setMaintenance(true);
        $this->assertTrue($this->device->fresh()->isInMaintenance());

        $this->device->decommission();
        $this->assertTrue($this->device->fresh()->isDecommissioned());
    }
}
