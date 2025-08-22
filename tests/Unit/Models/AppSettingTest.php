<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\{AppSetting, User, Device};
use App\Enums\SettingScope;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppSettingTest extends TestCase
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
    public function it_can_resolve_global_setting()
    {
        AppSetting::set('theme', 'dark', SettingScope::Global);

        $value = AppSetting::resolve('theme');
        $this->assertEquals('dark', $value);
    }

    /** @test */
    public function it_can_resolve_user_setting()
    {
        AppSetting::set('theme', 'dark', SettingScope::Global);
        AppSetting::set('theme', 'light', SettingScope::User, $this->user->id);

        $value = AppSetting::resolve('theme', null, $this->user->id);
        $this->assertEquals('light', $value);
    }

    /** @test */
    public function it_can_resolve_device_setting()
    {
        AppSetting::set('theme', 'dark', SettingScope::Global);
        AppSetting::set('theme', 'light', SettingScope::User, $this->user->id);
        AppSetting::set('theme', 'blue', SettingScope::Device, $this->device->id);

        $value = AppSetting::resolve('theme', $this->device->id, $this->user->id);
        $this->assertEquals('blue', $value);
    }

    /** @test */
    public function it_follows_precedence_device_user_global()
    {
        // Set all three levels
        AppSetting::set('threshold', 100, SettingScope::Global);
        AppSetting::set('threshold', 200, SettingScope::User, $this->user->id);
        AppSetting::set('threshold', 300, SettingScope::Device, $this->device->id);

        // Should return device setting (highest precedence)
        $value = AppSetting::resolve('threshold', $this->device->id, $this->user->id);
        $this->assertEquals(300, $value);

        // Should return user setting when no device setting
        $value = AppSetting::resolve('threshold', null, $this->user->id);
        $this->assertEquals(200, $value);

        // Should return global setting when no user setting
        $value = AppSetting::resolve('threshold');
        $this->assertEquals(100, $value);
    }

    /** @test */
    public function it_returns_null_for_nonexistent_setting()
    {
        $value = AppSetting::resolve('nonexistent');
        $this->assertNull($value);
    }

    /** @test */
    public function it_can_set_global_setting()
    {
        $setting = AppSetting::set('timezone', 'UTC', SettingScope::Global);

        $this->assertDatabaseHas('app_settings', [
            'key' => 'timezone',
            'scope_type' => SettingScope::Global,
            'scope_id' => null,
        ]);
        
        $dbSetting = AppSetting::where('key', 'timezone')->first();
        $this->assertEquals('UTC', $dbSetting->value);
    }

    /** @test */
    public function it_can_set_user_setting()
    {
        $setting = AppSetting::set('timezone', 'EST', SettingScope::User, $this->user->id);

        $this->assertDatabaseHas('app_settings', [
            'key' => 'timezone',
            'scope_type' => SettingScope::User,
            'scope_id' => $this->user->id,
        ]);
        
        $dbSetting = AppSetting::where('key', 'timezone')->first();
        $this->assertEquals('EST', $dbSetting->value);
    }

    /** @test */
    public function it_can_set_device_setting()
    {
        $setting = AppSetting::set('sampling_rate', 1000, SettingScope::Device, $this->device->id);

        $this->assertDatabaseHas('app_settings', [
            'key' => 'sampling_rate',
            'scope_type' => SettingScope::Device,
            'scope_id' => $this->device->id,
        ]);
        
        $dbSetting = AppSetting::where('key', 'sampling_rate')->first();
        $this->assertEquals(1000, $dbSetting->value);
    }

    /** @test */
    public function it_can_update_existing_setting()
    {
        AppSetting::set('theme', 'dark', SettingScope::Global);
        AppSetting::set('theme', 'light', SettingScope::Global);

        $this->assertDatabaseHas('app_settings', [
            'key' => 'theme',
            'scope_type' => SettingScope::Global,
        ]);
        
        $dbSetting = AppSetting::where('key', 'theme')->first();
        $this->assertEquals('light', $dbSetting->value);
        $this->assertDatabaseCount('app_settings', 1); // Should not create duplicate
    }

    /** @test */
    public function it_can_scope_for_device()
    {
        AppSetting::set('setting1', 'value1', SettingScope::Device, $this->device->id);
        AppSetting::set('setting2', 'value2', SettingScope::Device, $this->device->id);

        $deviceSettings = AppSetting::forDevice($this->device->id)->get();
        $this->assertEquals(2, $deviceSettings->count());
    }

    /** @test */
    public function it_can_scope_for_user()
    {
        AppSetting::set('setting1', 'value1', SettingScope::User, $this->user->id);
        AppSetting::set('setting2', 'value2', SettingScope::User, $this->user->id);

        $userSettings = AppSetting::forUser($this->user->id)->get();
        $this->assertEquals(2, $userSettings->count());
    }

    /** @test */
    public function it_can_scope_global()
    {
        AppSetting::set('setting1', 'value1', SettingScope::Global);
        AppSetting::set('setting2', 'value2', SettingScope::Global);

        $globalSettings = AppSetting::global()->get();
        $this->assertEquals(2, $globalSettings->count());
    }

    /** @test */
    public function it_can_store_complex_values()
    {
        $complexValue = [
            'thresholds' => ['min' => 10, 'max' => 100],
            'alerts' => ['email' => true, 'sms' => false],
        ];

        AppSetting::set('config', $complexValue, SettingScope::Global);

        $value = AppSetting::resolve('config');
        $this->assertEquals($complexValue, $value);
    }
}
