<?php

namespace Database\Factories;

use App\Models\{PowerRecord, Device, User};
use Illuminate\Database\Eloquent\Factories\Factory;

class PowerRecordFactory extends Factory
{
    protected $model = PowerRecord::class;

    public function definition(): array
    {
        $current = $this->faker->randomFloat(2, 0.1, 10.0);
        $voltage = $this->faker->randomFloat(1, 110.0, 240.0);
        $power = $current * $voltage;

        return [
            'device_id' => Device::factory(),
            'user_id' => User::factory(),
            'ts' => $this->faker->dateTimeBetween('-1 day', 'now'),
            'current' => $current,
            'voltage' => $voltage,
            'power' => $power,
            'sampling_ms' => $this->faker->numberBetween(100, 5000),
            'phase' => $this->faker->randomElement(['A', 'B', 'C']),
            'flags' => $this->faker->randomElements(['valid', 'calibrated', 'verified'], $this->faker->numberBetween(0, 3)),
        ];
    }

    public function highPower(): static
    {
        return $this->state(fn (array $attributes) => [
            'current' => $this->faker->randomFloat(2, 5.0, 10.0),
            'voltage' => $this->faker->randomFloat(1, 220.0, 240.0),
        ]);
    }

    public function lowPower(): static
    {
        return $this->state(fn (array $attributes) => [
            'current' => $this->faker->randomFloat(2, 0.1, 2.0),
            'voltage' => $this->faker->randomFloat(1, 110.0, 120.0),
        ]);
    }

    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'ts' => $this->faker->dateTimeBetween('-1 hour', 'now'),
        ]);
    }

    public function old(): static
    {
        return $this->state(fn (array $attributes) => [
            'ts' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
}
