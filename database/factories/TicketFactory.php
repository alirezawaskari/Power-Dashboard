<?php

namespace Database\Factories;

use App\Models\{Ticket, User, Device};
use App\Enums\{TicketStatus, TicketPriority, ThreadMode};
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'title' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(TicketStatus::cases()),
            'priority' => $this->faker->randomElement(TicketPriority::cases()),
            'thread_mode' => ThreadMode::SnapshotJson,
            'snapshot_json' => json_encode([
                'messages' => [
                    [
                        'id' => Str::uuid(),
                        'user_id' => null,
                        'message' => $this->faker->paragraph(),
                        'timestamp' => now()->subHours(1)->toISOString(),
                        'type' => 'system',
                    ],
                ],
                'version' => 1,
            ]),
            'snapshot_version' => 1,
            'last_activity_at' => $this->faker->dateTimeBetween('-1 day', 'now'),
            'meta' => json_encode([
                'source' => 'web',
                'ip_address' => $this->faker->ipv4(),
            ]),
            'user_id' => User::factory(),
            'device_id' => Device::factory(),
            'assignee_id' => $this->faker->optional()->randomElement([User::factory()]),
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatus::Open,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatus::Pending,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatus::Closed,
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TicketPriority::Urgent,
        ]);
    }

    public function high(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TicketPriority::High,
        ]);
    }

    public function normal(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TicketPriority::Normal,
        ]);
    }

    public function low(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TicketPriority::Low,
        ]);
    }

    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'assignee_id' => User::factory(),
        ]);
    }

    public function unassigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'assignee_id' => null,
        ]);
    }

    public function withMessages(int $count = 3): static
    {
        $messages = [];
        for ($i = 0; $i < $count; $i++) {
            $messages[] = [
                'id' => Str::uuid(),
                'user_id' => $i % 2 === 0 ? null : User::factory(),
                'message' => $this->faker->paragraph(),
                'timestamp' => now()->subHours($count - $i)->toISOString(),
                'type' => $i % 2 === 0 ? 'system' : 'user',
            ];
        }

        return $this->state(fn (array $attributes) => [
            'snapshot_json' => [
                'messages' => $messages,
                'version' => $count,
            ],
        ]);
    }
}
