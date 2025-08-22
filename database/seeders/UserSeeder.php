<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Owner One',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'support@example.com'],
            [
                'name' => 'Support User',
                'password' => Hash::make('password'),
                'role' => 'support',
            ]
        );
    }
}