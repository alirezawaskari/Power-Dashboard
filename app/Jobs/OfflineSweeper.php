<?php declare(strict_types=1);

namespace App\Jobs;

use App\Models\Device;
use Illuminate\Support\Facades\DB;

final class OfflineSweeper
{
    public function __invoke(): void
    {
        $seconds = (int) env('OFFLINE_SLA_SECONDS', 120);

        DB::table('devices')
            ->where('status', '!=', 'decommissioned')
            ->whereNotNull('last_seen_at')
            ->whereNotNull('heartbeat_seconds')
            ->whereRaw('last_seen_at < (NOW() - (heartbeat_seconds || " seconds")::interval)')
            ->update(['status' => 'offline']);

        Device::query()
            ->where('status', '!=', 'decommissioned')
            ->whereNull('heartbeat_seconds')
            ->where('last_seen_at', '<', now()->subSeconds($seconds))
            ->update(['status' => 'offline']);
    }
}
