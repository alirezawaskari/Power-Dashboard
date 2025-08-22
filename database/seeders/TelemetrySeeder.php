<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\PowerRecord;
use App\Models\User;
use Carbon\CarbonImmutable;

class TelemetrySeeder extends Seeder
{
    public function run(): void
    {
        $device = Device::query()->first();
        $user = User::where('email', 'owner@example.com')->first();
        if (!$device || !$user)
            return;

        $start = CarbonImmutable::now()->subHours(24)->minute(0)->second(0);
        for ($i = 0; $i < 24 * 60; $i++) {
            $ts = $start->addMinutes($i);
            $current = 2.0 + mt_rand(0, 100) / 100.0; // 2.0–3.0 A
            $voltage = 230.0 + mt_rand(-50, 50) / 10.0; // 225–235 V
            PowerRecord::create([
                'device_id' => $device->id,
                'user_id' => $user->id,
                'ts' => $ts,
                'current' => $current,
                'voltage' => $voltage,
                'power' => $current * $voltage,
                'sampling_ms' => 60000,
                'phase' => 'A',
            ]);
        }
    }
}
