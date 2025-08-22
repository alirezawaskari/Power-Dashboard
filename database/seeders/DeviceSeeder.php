<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('email', 'owner@example.com')->first();
        $secretPlain = 'device-secret-123';

        Device::query()->firstOrCreate(
            ['uuid' => (string) Str::uuid()],
            [
                'user_id' => $owner->id,
                'name' => 'Main Meter',
                'slug' => 'main-meter',
                'status' => 'offline',
                'secret_hash' => Hash::make($secretPlain),
                'firmware' => '1.0.0',
                'model' => 'PM-230',
                'location' => 'HQ',
                'tags' => ['hq', 'prod'],
            ]
        );
    }
}