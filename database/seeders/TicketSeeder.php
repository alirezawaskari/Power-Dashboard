<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Str;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::where('email', 'owner@example.com')->first();
        $assignee = User::where('email', 'support@example.com')->first();

        Ticket::query()->firstOrCreate(
            ['id' => (string) Str::uuid()],
            [
                'user_id' => $creator->id,
                'assignee_id' => $assignee->id,
                'status' => 'open',
                'priority' => 'normal',
                'thread_mode' => 'snapshot_json',
                'title' => 'Setup question',
                'last_activity_at' => now(),
                'snapshot_json' => json_encode([
                    'snapshot_version' => 1,
                    'messages' => [
                        ['role' => 'user', 'text' => 'How do I rename a device?', 'ts' => now()->toIso8601String()],
                        ['role' => 'support', 'text' => 'Go to Devices → Details → Edit.', 'ts' => now()->toIso8601String()],
                    ],
                ]),
                'snapshot_version' => 1,
            ]
        );
    }
}
