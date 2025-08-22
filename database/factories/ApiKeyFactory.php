<?php

namespace Database\Factories;

use App\Models\{ApiKey, User};
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApiKey>
 */
class ApiKeyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->words(3, true),
            'key_hash' => Hash::make('test-api-key-' . $this->faker->uuid()),
            'scopes' => $this->faker->randomElements([
                'devices:read', 'devices:write', 'tickets:read', 'tickets:write',
                'settings:read', 'settings:write', 'notifications:read'
            ], $this->faker->numberBetween(1, 3)),
            'last_used_at' => $this->faker->optional()->dateTimeBetween('-30 days', 'now'),
            'expires_at' => $this->faker->optional()->dateTimeBetween('now', '+90 days'),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
        ];
    }

    /**
     * Indicate that the API key is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'expires_at' => $this->faker->dateTimeBetween('now', '+90 days'),
        ]);
    }

    /**
     * Indicate that the API key is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'expires_at' => $this->faker->dateTimeBetween('-90 days', '-1 day'),
        ]);
    }

    /**
     * Indicate that the API key is revoked.
     */
    public function revoked(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the API key has specific scopes.
     */
    public function withScopes(array $scopes): static
    {
        return $this->state(fn (array $attributes) => [
            'scopes' => $scopes,
        ]);
    }
}
