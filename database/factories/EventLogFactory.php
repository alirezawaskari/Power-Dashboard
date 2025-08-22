<?php

namespace Database\Factories;

use App\Models\{EventLog, User, Device};
use App\Enums\EventType;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventLogFactory extends Factory
{
    protected $model = EventLog::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(EventType::cases()),
            'message' => $this->faker->sentence(),
            'actor_type' => $this->faker->randomElement(['user', 'device', 'system']),
            'actor_id' => $this->faker->numberBetween(1, 100),
            'subject_type' => $this->faker->randomElement(['user', 'device', 'ticket', 'power_record']),
            'subject_id' => $this->faker->numberBetween(1, 100),
            'context' => [
                'ip_address' => $this->faker->ipv4(),
                'user_agent' => $this->faker->userAgent(),
                'timestamp' => now()->toISOString(),
            ],
            'occurred_at' => $this->faker->dateTimeBetween('-1 day', 'now'),
        ];
    }

    public function deviceEvent(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $this->faker->randomElement([
                EventType::DeviceOnline,
                EventType::DeviceOffline,
                EventType::DeviceMaintenanceOn,
                EventType::DeviceMaintenanceOff,
                EventType::DeviceDecommissioned,
            ]),
            'actor_type' => 'device',
            'subject_type' => 'device',
        ]);
    }

    public function ticketEvent(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $this->faker->randomElement([
                EventType::TicketCreated,
                EventType::TicketUpdated,
                EventType::TicketClosed,
            ]),
            'actor_type' => 'user',
            'subject_type' => 'ticket',
        ]);
    }

    public function ingestEvent(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $this->faker->randomElement([
                EventType::IngestAccepted,
                EventType::IngestRejectedSchema,
                EventType::IngestRejectedAuth,
            ]),
            'actor_type' => 'device',
            'subject_type' => 'device',
        ]);
    }

    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'occurred_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
        ]);
    }

    public function old(): static
    {
        return $this->state(fn (array $attributes) => [
            'occurred_at' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
}
