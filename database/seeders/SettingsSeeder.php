<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppSetting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        AppSetting::updateOrCreate(
            ['scope_type' => 'global', 'scope_id' => null, 'key' => 'raw_window_max_days'],
            ['value' => json_encode(7)]
        );
        AppSetting::updateOrCreate(
            ['scope_type' => 'global', 'scope_id' => null, 'key' => 'theme'],
            ['value' => json_encode('dark')]
        );
    }
}