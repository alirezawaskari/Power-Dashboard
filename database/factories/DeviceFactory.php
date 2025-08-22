<?php

namespace Database\Factories;

use App\Models\{Device, User};
use App\Enums\DeviceStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'name' => $this->faker->words(2, true),
            'slug' => $this->faker->slug(),
            'status' => $this->faker->randomElement(DeviceStatus::cases()),
            'tags' => $this->faker->randomElements(['production', 'test', 'critical', 'monitoring'], $this->faker->numberBetween(0, 3)),
            'secret_hash' => \Illuminate\Support\Facades\Hash::make('test-secret'),
            'firmware' => $this->faker->optional()->semver(),
            'model' => $this->faker->optional()->word(),
            'location' => $this->faker->optional()->city(),
            'notes' => $this->faker->optional()->sentence(),
            'last_seen_at' => $this->faker->optional()->dateTimeBetween('-1 day', 'now'),
            'heartbeat_seconds' => $this->faker->optional()->numberBetween(30, 300),
            'last_rotated_at' => $this->faker->optional()->dateTimeBetween('-30 days', 'now'),
            'user_id' => User::factory(),
        ];
    }

    public function online(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DeviceStatus::Online,
            'last_seen_at' => now(),
        ]);
    }

    public function offline(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DeviceStatus::Offline,
            'last_seen_at' => now()->subMinutes(10),
        ]);
    }

    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DeviceStatus::Maintenance,
        ]);
    }

    public function decommissioned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DeviceStatus::Decommissioned,
        ]);
    }

    public function withSecret(string $secret): static
    {
        return $this->state(fn (array $attributes) => [
            'secret_hash' => \Illuminate\Support\Facades\Hash::make($secret),
        ]);
    }
}
