<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Device;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class IngestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->seed();
    }

    public function test_ingest_happy_path(): void
    {
        $device = Device::first();
        $secret = 'device-secret-123'; // matches seeder
        $device->secret_hash = Hash::make($secret);
        $device->save();

        $payload = [
            'ts' => now()->toIso8601String(),
            'current' => 3.2,
            'voltage' => 230.1,
            'attributes' => ['phase' => 'A', 'sample_ms' => 1000],
        ];

        $res = $this->withHeaders([
            'X-Device-ID' => $device->uuid,
            'X-Device-Secret' => $secret,
        ])->postJson('/api/ingest', $payload);

        $res->assertStatus(202);
        $this->assertDatabaseCount('power_records', \App\Models\PowerRecord::count());

    }

    public function test_ingest_rejects_bad_secret(): void
    {
        $device = Device::first();
        $secret = 'device-secret-123'; // matches seeder
        $device->secret_hash = Hash::make($secret);
        $device->save();
        
        $payload = [
            'ts' => now()->toIso8601String(),
            'current' => 3.2,
            'voltage' => 230.1,
        ];
        $res = $this->withHeaders([
            'X-Device-ID' => $device->uuid,
            'X-Device-Secret' => 'nope',
        ])->postJson('/api/ingest', $payload);
        $res->assertStatus(401);
    }
}