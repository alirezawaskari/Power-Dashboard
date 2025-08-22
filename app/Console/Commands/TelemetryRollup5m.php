<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TelemetryRollup5m extends Command
{
    protected $signature = 'telemetry:rollup-5m {--since=} {--until=}';
    protected $description = 'Aggregate power_records into 5-minute buckets';

    public function handle(): int
    {
        // Simple DB-level rollup (portable). For large data consider window functions or TimescaleDB.
        $since = $this->option('since') ?: now()->subHour()->startOfMinute()->toIso8601String();
        $until = $this->option('until') ?: now()->toIso8601String();

        DB::statement(<<<SQL
        INSERT INTO telemetry_rollups_5m (device_id, bucket_start_ts, min_power, avg_power, max_power, count, min_current, avg_current, max_current, min_voltage, avg_voltage, max_voltage, created_at, updated_at)
        SELECT
          device_id,
          DATE_TRUNC('minute', ts) - (EXTRACT(MINUTE FROM ts)::int % 5) * INTERVAL '1 minute' AS bucket_start,
          MIN(power) as min_power,
          AVG(power) as avg_power,
          MAX(power) as max_power,
          COUNT(*) as cnt,
          MIN(current) as min_current,
          AVG(current) as avg_current,
          MAX(current) as max_current,
          MIN(voltage) as min_voltage,
          AVG(voltage) as avg_voltage,
          MAX(voltage) as max_voltage,
          NOW(), NOW()
        FROM power_records
        WHERE ts >= :since AND ts < :until
        GROUP BY device_id, bucket_start
        ON CONFLICT (device_id, bucket_start_ts) DO UPDATE SET
          min_power = EXCLUDED.min_power,
          avg_power = EXCLUDED.avg_power,
          max_power = EXCLUDED.max_power,
          count = EXCLUDED.count,
          min_current = EXCLUDED.min_current,
          avg_current = EXCLUDED.avg_current,
          max_current = EXCLUDED.max_current,
          min_voltage = EXCLUDED.min_voltage,
          avg_voltage = EXCLUDED.avg_voltage,
          max_voltage = EXCLUDED.max_voltage,
          updated_at = NOW();
        SQL, [
            'since' => $since,
            'until' => $until,
        ]);

        $this->info('Rollups generated.');
        return self::SUCCESS;
    }
}