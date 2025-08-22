<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use App\Models\{Device, User, AppSetting};
use App\Enums\{DeviceStatus, SettingScope};
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class CheckDeviceHeartbeatsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Device $onlineDevice;
    private Device $oldHeartbeatDevice;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock NotificationService to avoid RabbitMQ dependency
        $this->mock(NotificationService::class, function ($mock) {
            $mock->shouldReceive('notifyDeviceOffline')->andReturn();
        });
        
        $this->user = User::factory()->create();
        $this->onlineDevice = Device::factory()->create([
            'user_id' => $this->user->id,
            'status' => DeviceStatus::Online,
            'last_seen_at' => now(),
        ]);
        
        $this->oldHeartbeatDevice = Device::factory()->create([
            'user_id' => $this->user->id,
            'status' => DeviceStatus::Online,
            'last_seen_at' => now()->subMinutes(10), // Old heartbeat
        ]);
    }

    /** @test */
    public function it_marks_devices_offline_when_heartbeat_is_old()
    {
        // Set heartbeat threshold to 5 minutes
        AppSetting::set('device.heartbeat_threshold_minutes', 5, SettingScope::Global);

        $this->artisan('devices:check-heartbeats')
            ->expectsOutput('Checking device heartbeats...')
            ->expectsOutput("Device {$this->oldHeartbeatDevice->name} should be marked offline")
            ->expectsOutput('Marked 1 devices as offline')
            ->assertExitCode(0);

        // Check device was marked offline
        $this->oldHeartbeatDevice->refresh();
        $this->assertEquals(DeviceStatus::Offline, $this->oldHeartbeatDevice->status);

        // Check online device was not affected
        $this->onlineDevice->refresh();
        $this->assertEquals(DeviceStatus::Online, $this->onlineDevice->status);
    }

    /** @test */
    public function it_uses_device_specific_heartbeat_threshold()
    {
        // Set global threshold to 5 minutes
        AppSetting::set('device.heartbeat_threshold_minutes', 5, SettingScope::Global);
        
        // Set device-specific threshold to 15 minutes
        AppSetting::set('device.heartbeat_threshold_minutes', 15, SettingScope::Device, $this->oldHeartbeatDevice->id);

        $this->artisan('devices:check-heartbeats')
            ->expectsOutput('Checking device heartbeats...')
            ->expectsOutput('Marked 0 devices as offline')
            ->assertExitCode(0);

        // Device should still be online because device-specific threshold is higher
        $this->oldHeartbeatDevice->refresh();
        $this->assertEquals(DeviceStatus::Online, $this->oldHeartbeatDevice->status);
    }

    /** @test */
    public function it_uses_user_specific_heartbeat_threshold()
    {
        // Set global threshold to 5 minutes
        AppSetting::set('device.heartbeat_threshold_minutes', 5, SettingScope::Global);
        
        // Set user-specific threshold to 15 minutes
        AppSetting::set('device.heartbeat_threshold_minutes', 15, SettingScope::User, $this->user->id);

        $this->artisan('devices:check-heartbeats')
            ->expectsOutput('Checking device heartbeats...')
            ->expectsOutput('Marked 0 devices as offline')
            ->assertExitCode(0);

        // Device should still be online because user-specific threshold is higher
        $this->oldHeartbeatDevice->refresh();
        $this->assertEquals(DeviceStatus::Online, $this->oldHeartbeatDevice->status);
    }

    /** @test */
    public function it_does_not_check_offline_devices()
    {
        $offlineDevice = Device::factory()->create([
            'user_id' => $this->user->id,
            'status' => DeviceStatus::Offline,
            'last_seen_at' => now()->subMinutes(10),
        ]);

        $this->artisan('devices:check-heartbeats')
            ->expectsOutput('Checking device heartbeats...')
            ->assertExitCode(0);

        // Offline device should remain offline
        $offlineDevice->refresh();
        $this->assertEquals(DeviceStatus::Offline, $offlineDevice->status);
    }

    /** @test */
    public function it_does_not_check_maintenance_devices()
    {
        $maintenanceDevice = Device::factory()->create([
            'user_id' => $this->user->id,
            'status' => DeviceStatus::Maintenance,
            'last_seen_at' => now()->subMinutes(10),
        ]);

        $this->artisan('devices:check-heartbeats')
            ->expectsOutput('Checking device heartbeats...')
            ->assertExitCode(0);

        // Maintenance device should remain in maintenance
        $maintenanceDevice->refresh();
        $this->assertEquals(DeviceStatus::Maintenance, $maintenanceDevice->status);
    }

    /** @test */
    public function it_does_not_check_decommissioned_devices()
    {
        $decommissionedDevice = Device::factory()->create([
            'user_id' => $this->user->id,
            'status' => DeviceStatus::Decommissioned,
            'last_seen_at' => now()->subMinutes(10),
        ]);

        $this->artisan('devices:check-heartbeats')
            ->expectsOutput('Checking device heartbeats...')
            ->assertExitCode(0);

        // Decommissioned device should remain decommissioned
        $decommissionedDevice->refresh();
        $this->assertEquals(DeviceStatus::Decommissioned, $decommissionedDevice->status);
    }

    /** @test */
    public function it_uses_default_threshold_when_no_setting_exists()
    {
        // Don't set any heartbeat threshold setting

        $this->artisan('devices:check-heartbeats')
            ->expectsOutput('Checking device heartbeats...')
            ->expectsOutput("Device {$this->oldHeartbeatDevice->name} should be marked offline")
            ->expectsOutput('Marked 1 devices as offline')
            ->assertExitCode(0);

        // Device should be marked offline (default threshold is 5 minutes)
        $this->oldHeartbeatDevice->refresh();
        $this->assertEquals(DeviceStatus::Offline, $this->oldHeartbeatDevice->status);
    }

    /** @test */
    public function it_handles_devices_without_last_seen_at()
    {
        $deviceWithoutHeartbeat = Device::factory()->create([
            'user_id' => $this->user->id,
            'status' => DeviceStatus::Online,
            'last_seen_at' => null,
        ]);

        $this->artisan('devices:check-heartbeats')
            ->expectsOutput('Checking device heartbeats...')
            ->assertExitCode(0);

        // Device without heartbeat should remain online
        $deviceWithoutHeartbeat->refresh();
        $this->assertEquals(DeviceStatus::Online, $deviceWithoutHeartbeat->status);
    }
}
