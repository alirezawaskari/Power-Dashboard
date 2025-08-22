<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TelemetryPruneRaw extends Command
{
    protected $signature = 'telemetry:prune-raw {--days=30} {--device=}';
    protected $description = 'Chunked prune of power_records older than N days (optional per device)';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);
        $device = $this->option('device');
        $total = 0;
        do {
            $q = DB::table('power_records')->where('ts', '<', $cutoff)->orderBy('id')->limit(10000);
            if ($device)
                $q->where('device_id', $device);
            $ids = $q->pluck('id')->all();
            if (!$ids)
                break;
            $total += DB::table('power_records')->whereIn('id', $ids)->delete();
        } while (true);
        $this->info("Deleted {$total}");
        return self::SUCCESS;
    }
}