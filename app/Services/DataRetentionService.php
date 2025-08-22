<?php declare(strict_types=1);

namespace App\Services;

use App\Models\{PowerRecord, EventLog, Notification, Alert};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DataRetentionService
{
    private const DEFAULT_RETENTION_DAYS = [
        'power_records' => 90,      // 3 months
        'event_logs' => 365,        // 1 year
        'notifications' => 30,      // 1 month
        'alerts' => 180,            // 6 months
        'exports' => 7,             // 1 week
    ];

    public function cleanupOldPowerRecords(int $days = null): array
    {
        $days = $days ?? self::DEFAULT_RETENTION_DAYS['power_records'];
        $cutoffDate = Carbon::now()->subDays($days);

        $deletedCount = PowerRecord::where('ts', '<', $cutoffDate)->delete();

        Log::info('Data retention: Cleaned up old power records', [
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate->toISOString(),
            'retention_days' => $days,
        ]);

        return [
            'table' => 'power_records',
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate->toISOString(),
            'retention_days' => $days,
        ];
    }

    public function cleanupOldEventLogs(int $days = null): array
    {
        $days = $days ?? self::DEFAULT_RETENTION_DAYS['event_logs'];
        $cutoffDate = Carbon::now()->subDays($days);

        $deletedCount = EventLog::where('occurred_at', '<', $cutoffDate)->delete();

        Log::info('Data retention: Cleaned up old event logs', [
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate->toISOString(),
            'retention_days' => $days,
        ]);

        return [
            'table' => 'event_logs',
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate->toISOString(),
            'retention_days' => $days,
        ];
    }

    public function cleanupOldNotifications(int $days = null): array
    {
        $days = $days ?? self::DEFAULT_RETENTION_DAYS['notifications'];
        $cutoffDate = Carbon::now()->subDays($days);

        $deletedCount = Notification::where('created_at', '<', $cutoffDate)->delete();

        Log::info('Data retention: Cleaned up old notifications', [
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate->toISOString(),
            'retention_days' => $days,
        ]);

        return [
            'table' => 'notifications',
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate->toISOString(),
            'retention_days' => $days,
        ];
    }

    public function cleanupOldAlerts(int $days = null): array
    {
        $days = $days ?? self::DEFAULT_RETENTION_DAYS['alerts'];
        $cutoffDate = Carbon::now()->subDays($days);

        // Only delete resolved alerts older than retention period
        $deletedCount = Alert::where('status', 'resolved')
            ->where('resolved_at', '<', $cutoffDate)
            ->delete();

        Log::info('Data retention: Cleaned up old resolved alerts', [
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate->toISOString(),
            'retention_days' => $days,
        ]);

        return [
            'table' => 'alerts',
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate->toISOString(),
            'retention_days' => $days,
        ];
    }

    public function cleanupOldExports(int $days = null): array
    {
        $days = $days ?? self::DEFAULT_RETENTION_DAYS['exports'];
        $cutoffDate = Carbon::now()->subDays($days);

        $deletedCount = 0;
        $files = \Storage::files('exports');

        foreach ($files as $file) {
            $lastModified = Carbon::createFromTimestamp(\Storage::lastModified($file));
            if ($lastModified->lt($cutoffDate)) {
                \Storage::delete($file);
                $deletedCount++;
            }
        }

        Log::info('Data retention: Cleaned up old export files', [
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate->toISOString(),
            'retention_days' => $days,
        ]);

        return [
            'table' => 'exports',
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate->toISOString(),
            'retention_days' => $days,
        ];
    }

    public function runFullCleanup(): array
    {
        $results = [];

        try {
            $results[] = $this->cleanupOldPowerRecords();
            $results[] = $this->cleanupOldEventLogs();
            $results[] = $this->cleanupOldNotifications();
            $results[] = $this->cleanupOldAlerts();
            $results[] = $this->cleanupOldExports();

            Log::info('Data retention: Full cleanup completed', [
                'total_tables_processed' => count($results),
                'total_records_deleted' => array_sum(array_column($results, 'deleted_count')),
            ]);

        } catch (\Exception $e) {
            Log::error('Data retention: Full cleanup failed', [
                'error' => $e->getMessage(),
                'results' => $results,
            ]);
            throw $e;
        }

        return $results;
    }

    public function getRetentionStats(): array
    {
        $now = Carbon::now();

        return [
            'power_records' => [
                'total' => PowerRecord::count(),
                'oldest' => PowerRecord::min('ts'),
                'newest' => PowerRecord::max('ts'),
                'retention_days' => self::DEFAULT_RETENTION_DAYS['power_records'],
                'cutoff_date' => $now->copy()->subDays(self::DEFAULT_RETENTION_DAYS['power_records'])->toISOString(),
            ],
            'event_logs' => [
                'total' => EventLog::count(),
                'oldest' => EventLog::min('occurred_at'),
                'newest' => EventLog::max('occurred_at'),
                'retention_days' => self::DEFAULT_RETENTION_DAYS['event_logs'],
                'cutoff_date' => $now->copy()->subDays(self::DEFAULT_RETENTION_DAYS['event_logs'])->toISOString(),
            ],
            'notifications' => [
                'total' => Notification::count(),
                'oldest' => Notification::min('created_at'),
                'newest' => Notification::max('created_at'),
                'retention_days' => self::DEFAULT_RETENTION_DAYS['notifications'],
                'cutoff_date' => $now->copy()->subDays(self::DEFAULT_RETENTION_DAYS['notifications'])->toISOString(),
            ],
            'alerts' => [
                'total' => Alert::count(),
                'resolved' => Alert::where('status', 'resolved')->count(),
                'oldest' => Alert::min('triggered_at'),
                'newest' => Alert::max('triggered_at'),
                'retention_days' => self::DEFAULT_RETENTION_DAYS['alerts'],
                'cutoff_date' => $now->copy()->subDays(self::DEFAULT_RETENTION_DAYS['alerts'])->toISOString(),
            ],
            'exports' => [
                'total_files' => count(\Storage::files('exports')),
                'retention_days' => self::DEFAULT_RETENTION_DAYS['exports'],
                'cutoff_date' => $now->copy()->subDays(self::DEFAULT_RETENTION_DAYS['exports'])->toISOString(),
            ],
        ];
    }

    public function estimateCleanupImpact(): array
    {
        $now = Carbon::now();
        $impact = [];

        // Power records
        $cutoffDate = $now->copy()->subDays(self::DEFAULT_RETENTION_DAYS['power_records']);
        $impact['power_records'] = PowerRecord::where('ts', '<', $cutoffDate)->count();

        // Event logs
        $cutoffDate = $now->copy()->subDays(self::DEFAULT_RETENTION_DAYS['event_logs']);
        $impact['event_logs'] = EventLog::where('occurred_at', '<', $cutoffDate)->count();

        // Notifications
        $cutoffDate = $now->copy()->subDays(self::DEFAULT_RETENTION_DAYS['notifications']);
        $impact['notifications'] = Notification::where('created_at', '<', $cutoffDate)->count();

        // Alerts
        $cutoffDate = $now->copy()->subDays(self::DEFAULT_RETENTION_DAYS['alerts']);
        $impact['alerts'] = Alert::where('status', 'resolved')
            ->where('resolved_at', '<', $cutoffDate)
            ->count();

        // Exports
        $cutoffDate = $now->copy()->subDays(self::DEFAULT_RETENTION_DAYS['exports']);
        $exportFiles = \Storage::files('exports');
        $impact['exports'] = 0;
        foreach ($exportFiles as $file) {
            $lastModified = Carbon::createFromTimestamp(\Storage::lastModified($file));
            if ($lastModified->lt($cutoffDate)) {
                $impact['exports']++;
            }
        }

        $impact['total'] = array_sum($impact);

        return $impact;
    }
}
