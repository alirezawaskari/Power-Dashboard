<?php

namespace Database\Factories;

use App\Models\{Notification, User};
use App\Enums\{NotificationType, NotificationStatus};
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(NotificationType::cases()),
            'title' => $this->faker->sentence(),
            'message' => $this->faker->paragraph(),
            'data' => [
                'device_id' => $this->faker->uuid(),
                'timestamp' => now()->toISOString(),
            ],
            'status' => $this->faker->randomElement(NotificationStatus::cases()),
            'channel' => $this->faker->randomElement(['email', 'websocket', 'sms']),
            'priority' => $this->faker->numberBetween(1, 5),
            'retry_count' => 0,
            'max_retries' => 3,
            'read_at' => $this->faker->optional()->dateTimeBetween('-1 day', 'now'),
            'delivered_at' => $this->faker->optional()->dateTimeBetween('-1 day', 'now'),
            'failed_at' => null,
            'scheduled_at' => $this->faker->optional()->dateTimeBetween('now', '+1 day'),
            'expires_at' => $this->faker->optional()->dateTimeBetween('now', '+7 days'),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => NotificationStatus::Pending,
            'read_at' => null,
            'delivered_at' => null,
            'failed_at' => null,
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => NotificationStatus::Sent,
            'delivered_at' => now(),
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => NotificationStatus::Delivered,
            'delivered_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => NotificationStatus::Failed,
            'failed_at' => now(),
            'retry_count' => $this->faker->numberBetween(1, 3),
        ]);
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => now(),
        ]);
    }

    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => null,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subHour(),
        ]);
    }
}
