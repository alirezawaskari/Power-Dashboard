<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Device, PowerRecord, EventLog, User};
use App\Enums\{DeviceStatus, EventType};
use Illuminate\Foundation\Testing\RefreshDatabase;

class IngestControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Device $device;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->device = Device::factory()
            ->withSecret('test-secret')
            ->create([
                'user_id' => $this->user->id,
                'status' => DeviceStatus::Offline,
            ]);
    }

    /** @test */
    public function it_can_ingest_power_data()
    {
        $data = [
            'ts' => now()->toISOString(),
            'current' => 3.5,
            'voltage' => 230.0,
            'attributes' => [
                'phase' => 'A',
                'samplems' => 1000,
            ],
        ];

        $response = $this->postJson('/api/ingest', $data, [
            'X-Device-ID' => $this->device->uuid,
            'X-Device-Secret' => 'test-secret',
        ]);

        $response->assertStatus(202)
                ->assertJson(['ok' => true]);

        // Check power record was created
        $this->assertDatabaseHas('power_records', [
            'device_id' => $this->device->id,
            'user_id' => $this->user->id,
            'current' => 3.5,
            'voltage' => 230.0,
            'power' => 805.0, // 3.5 * 230
        ]);

        // Check device was marked online
        $this->device->refresh();
        $this->assertEquals(DeviceStatus::Online, $this->device->status);
        $this->assertNotNull($this->device->last_seen_at);

        // Check event log was created
        $this->assertDatabaseHas('event_logs', [
            'type' => EventType::IngestAccepted->value,
            'actor_type' => 'device',
            'actor_id' => $this->device->id,
            'subject_type' => 'device',
            'subject_id' => $this->device->id,
        ]);
    }

    /** @test */
    public function it_calculates_power_automatically()
    {
        $data = [
            'ts' => now()->toISOString(),
            'current' => 2.0,
            'voltage' => 120.0,
        ];

        $response = $this->postJson('/api/ingest', $data, [
            'X-Device-ID' => $this->device->uuid,
            'X-Device-Secret' => 'test-secret',
        ]);

        $response->assertStatus(202);

        $this->assertDatabaseHas('power_records', [
            'device_id' => $this->device->id,
            'power' => 240.0, // 2.0 * 120
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->postJson('/api/ingest', [], [
            'X-Device-ID' => $this->device->uuid,
            'X-Device-Secret' => 'test-secret',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['ts', 'current', 'voltage']);
    }

    /** @test */
    public function it_validates_numeric_values()
    {
        $data = [
            'ts' => now()->toISOString(),
            'current' => -1.0, // Negative current
            'voltage' => 230.0,
        ];

        $response = $this->postJson('/api/ingest', $data, [
            'X-Device-ID' => $this->device->uuid,
            'X-Device-Secret' => 'test-secret',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['current']);
    }

    /** @test */
    public function it_accepts_optional_attributes()
    {
        $data = [
            'ts' => now()->toISOString(),
            'current' => 3.5,
            'voltage' => 230.0,
            'attributes' => [
                'phase' => 'B',
                'samplems' => 500,
                'custom_field' => 'custom_value',
            ],
        ];

        $response = $this->postJson('/api/ingest', $data, [
            'X-Device-ID' => $this->device->uuid,
            'X-Device-Secret' => 'test-secret',
        ]);

        $response->assertStatus(202);

        $this->assertDatabaseHas('power_records', [
            'device_id' => $this->device->id,
            'phase' => 'B',
        ]);
    }

    /** @test */
    public function it_handles_missing_attributes()
    {
        $data = [
            'ts' => now()->toISOString(),
            'current' => 3.5,
            'voltage' => 230.0,
        ];

        $response = $this->postJson('/api/ingest', $data, [
            'X-Device-ID' => $this->device->uuid,
            'X-Device-Secret' => 'test-secret',
        ]);

        $response->assertStatus(202);

        $this->assertDatabaseHas('power_records', [
            'device_id' => $this->device->id,
            'current' => 3.5,
            'voltage' => 230.0,
        ]);
    }

    /** @test */
    public function it_prevents_ingest_from_decommissioned_device()
    {
        $this->device->decommission();

        $data = [
            'ts' => now()->toISOString(),
            'current' => 3.5,
            'voltage' => 230.0,
        ];

        $response = $this->postJson('/api/ingest', $data, [
            'X-Device-ID' => $this->device->uuid,
            'X-Device-Secret' => 'test-secret',
        ]);

        $response->assertStatus(403); // Should be rejected by device auth middleware with 403 for decommissioned
    }

    /** @test */
    public function it_creates_event_log_for_validation_failures()
    {
        $data = [
            'ts' => 'invalid-date',
            'current' => 'not-a-number',
            'voltage' => 230.0,
        ];

        $response = $this->postJson('/api/ingest', $data, [
            'X-Device-ID' => $this->device->uuid,
            'X-Device-Secret' => 'test-secret',
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseHas('event_logs', [
            'type' => EventType::IngestRejectedSchema->value,
            'actor_type' => 'device',
            'actor_id' => $this->device->id,
        ]);
    }

    /** @test */
    public function it_handles_multiple_ingest_requests()
    {
        // First ingest
        $data1 = [
            'ts' => now()->subMinute()->toISOString(),
            'current' => 3.0,
            'voltage' => 230.0,
        ];

        $response1 = $this->postJson('/api/ingest', $data1, [
            'X-Device-ID' => $this->device->uuid,
            'X-Device-Secret' => 'test-secret',
        ]);

        $response1->assertStatus(202);

        // Second ingest
        $data2 = [
            'ts' => now()->toISOString(),
            'current' => 3.5,
            'voltage' => 230.0,
        ];

        $response2 = $this->postJson('/api/ingest', $data2, [
            'X-Device-ID' => $this->device->uuid,
            'X-Device-Secret' => 'test-secret',
        ]);

        $response2->assertStatus(202);

        // Check both records were created
        $this->assertDatabaseCount('power_records', 2);
        
        // Check device is still online
        $this->device->refresh();
        $this->assertEquals(DeviceStatus::Online, $this->device->status);
    }
}
