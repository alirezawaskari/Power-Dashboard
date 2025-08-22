<?php

namespace Tests\Feature\Unit\Models;

use Tests\TestCase;
use App\Models\{ApiKey, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ApiKeyTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_generate_api_key()
    {
        $key = ApiKey::generateKey();
        
        $this->assertStringStartsWith('pk_', $key);
        $this->assertEquals(35, strlen($key)); // pk_ + 32 chars
    }

    /** @test */
    public function it_can_create_api_key_for_user()
    {
        $result = ApiKey::createForUser(
            user: $this->user,
            name: 'Test API Key',
            scopes: ['devices:read', 'tickets:write'],
            expiresAt: now()->addDays(30)->toISOString()
        );

        $this->assertArrayHasKey('key', $result);
        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('Test API Key', $result['name']);
        $this->assertEquals(['devices:read', 'tickets:write'], $result['scopes']);

        $apiKey = ApiKey::find($result['id']);
        $this->assertNotNull($apiKey);
        $this->assertTrue(Hash::check($result['key'], $apiKey->key_hash));
    }

    /** @test */
    public function it_can_check_scope_permissions()
    {
        $apiKey = ApiKey::factory()->create([
            'user_id' => $this->user->id,
            'scopes' => ['devices:read', 'tickets:write'],
        ]);

        $this->assertTrue($apiKey->hasScope('devices:read'));
        $this->assertTrue($apiKey->hasScope('tickets:write'));
        $this->assertFalse($apiKey->hasScope('settings:write'));
    }

    /** @test */
    public function it_can_check_multiple_scopes()
    {
        $apiKey = ApiKey::factory()->create([
            'user_id' => $this->user->id,
            'scopes' => ['devices:read', 'tickets:write'],
        ]);

        $this->assertTrue($apiKey->hasAnyScope(['devices:read', 'settings:write']));
        $this->assertFalse($apiKey->hasAnyScope(['settings:write', 'users:read']));
    }

    /** @test */
    public function it_can_mark_as_used()
    {
        $apiKey = ApiKey::factory()->create([
            'user_id' => $this->user->id,
            'last_used_at' => null,
        ]);

        $apiKey->markAsUsed();

        $this->assertNotNull($apiKey->fresh()->last_used_at);
    }

    /** @test */
    public function it_can_revoke_api_key()
    {
        $apiKey = ApiKey::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        $apiKey->revoke();

        $this->assertFalse($apiKey->fresh()->is_active);
    }

    /** @test */
    public function it_can_check_expiration()
    {
        $apiKey = ApiKey::factory()->create([
            'user_id' => $this->user->id,
            'expires_at' => now()->addDays(1),
        ]);

        $this->assertFalse($apiKey->isExpired());

        $apiKey->update(['expires_at' => now()->subDays(1)]);
        $this->assertTrue($apiKey->fresh()->isExpired());
    }

    /** @test */
    public function it_can_check_validity()
    {
        $apiKey = ApiKey::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
            'expires_at' => now()->addDays(1),
        ]);

        $this->assertTrue($apiKey->isValid());

        // Test expired
        $apiKey->update(['expires_at' => now()->subDays(1)]);
        $this->assertFalse($apiKey->fresh()->isValid());

        // Test revoked
        $apiKey->update(['is_active' => false, 'expires_at' => now()->addDays(1)]);
        $this->assertFalse($apiKey->fresh()->isValid());
    }

    /** @test */
    public function it_can_scope_active_keys()
    {
        ApiKey::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
            'expires_at' => now()->addDays(1),
        ]);

        ApiKey::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => false,
        ]);

        ApiKey::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
            'expires_at' => now()->subDays(1),
        ]);

        $activeKeys = ApiKey::active()->get();
        $this->assertEquals(1, $activeKeys->count());
    }

    /** @test */
    public function it_can_scope_by_user()
    {
        $otherUser = User::factory()->create();

        ApiKey::factory()->create(['user_id' => $this->user->id]);
        ApiKey::factory()->create(['user_id' => $otherUser->id]);

        $userKeys = ApiKey::byUser($this->user->id)->get();
        $this->assertEquals(1, $userKeys->count());
        $this->assertEquals($this->user->id, $userKeys->first()->user_id);
    }

    /** @test */
    public function it_hides_key_hash_from_serialization()
    {
        $apiKey = ApiKey::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $array = $apiKey->toArray();
        
        $this->assertArrayNotHasKey('key_hash', $array);
    }
}
