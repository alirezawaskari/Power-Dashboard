<?php declare(strict_types=1);

namespace App\Services;

use App\Models\{Device, PowerRecord, Alert, User};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getPowerConsumptionTrends(Device $device, Carbon $from, Carbon $to, string $interval = '1 hour'): array
    {
        $records = PowerRecord::where('device_id', $device->id)
            ->whereBetween('ts', [$from, $to])
            ->selectRaw("
                DATE_TRUNC('{$interval}', ts) as time_bucket,
                AVG(power) as avg_power,
                MAX(power) as max_power,
                MIN(power) as min_power,
                COUNT(*) as record_count
            ")
            ->groupBy('time_bucket')
            ->orderBy('time_bucket')
            ->get();

        return [
            'device_id' => $device->id,
            'device_name' => $device->name,
            'period' => [
                'from' => $from->toISOString(),
                'to' => $to->toISOString(),
                'interval' => $interval,
            ],
            'data' => $records->map(function ($record) {
                return [
                    'timestamp' => $record->time_bucket->toISOString(),
                    'avg_power' => round($record->avg_power, 2),
                    'max_power' => round($record->max_power, 2),
                    'min_power' => round($record->min_power, 2),
                    'record_count' => $record->record_count,
                ];
            }),
        ];
    }

    public function getDeviceHealthMetrics(Device $device, Carbon $from, Carbon $to): array
    {
        $records = PowerRecord::where('device_id', $device->id)
            ->whereBetween('ts', [$from, $to])
            ->get();

        $totalRecords = $records->count();
        if ($totalRecords === 0) {
            return [
                'device_id' => $device->id,
                'device_name' => $device->name,
                'health_score' => 0,
                'metrics' => [],
            ];
        }

        // Calculate health metrics
        $avgPower = $records->avg('power');
        $avgVoltage = $records->avg('voltage');
        $avgCurrent = $records->avg('current');
        
        // Voltage stability (should be around 230V for most devices)
        $voltageStability = 100 - min(100, abs($avgVoltage - 230) / 230 * 100);
        
        // Power factor (assuming ideal is 1.0)
        $powerFactor = $avgPower / ($avgVoltage * $avgCurrent);
        $powerFactorScore = max(0, min(100, $powerFactor * 100));
        
        // Data consistency (check for gaps)
        $expectedRecords = $from->diffInMinutes($to) / 5; // Assuming 5-minute intervals
        $dataConsistency = min(100, ($totalRecords / $expectedRecords) * 100);
        
        // Overall health score
        $healthScore = round(($voltageStability + $powerFactorScore + $dataConsistency) / 3, 2);

        return [
            'device_id' => $device->id,
            'device_name' => $device->name,
            'health_score' => $healthScore,
            'metrics' => [
                'avg_power' => round($avgPower, 2),
                'avg_voltage' => round($avgVoltage, 2),
                'avg_current' => round($avgCurrent, 2),
                'voltage_stability' => round($voltageStability, 2),
                'power_factor' => round($powerFactor, 3),
                'power_factor_score' => round($powerFactorScore, 2),
                'data_consistency' => round($dataConsistency, 2),
                'total_records' => $totalRecords,
                'expected_records' => round($expectedRecords),
            ],
        ];
    }

    public function getUserDashboardMetrics(User $user): array
    {
        $devices = Device::where('user_id', $user->id)->get();
        $deviceIds = $devices->pluck('id');

        $now = now();
        $last24Hours = $now->copy()->subDay();
        $last7Days = $now->copy()->subWeek();
        $last30Days = $now->copy()->subMonth();

        // Device status summary
        $deviceStatus = $devices->groupBy('status')->map->count();

        // Power consumption in last 24 hours
        $power24h = PowerRecord::whereIn('device_id', $deviceIds)
            ->whereBetween('ts', [$last24Hours, $now])
            ->sum('power');

        // Power consumption in last 7 days
        $power7d = PowerRecord::whereIn('device_id', $deviceIds)
            ->whereBetween('ts', [$last7Days, $now])
            ->sum('power');

        // Power consumption in last 30 days
        $power30d = PowerRecord::whereIn('device_id', $deviceIds)
            ->whereBetween('ts', [$last30Days, $now])
            ->sum('power');

        // Active alerts
        $activeAlerts = Alert::where('user_id', $user->id)
            ->where('status', 'active')
            ->count();

        // Recent alerts (last 7 days)
        $recentAlerts = Alert::where('user_id', $user->id)
            ->whereBetween('triggered_at', [$last7Days, $now])
            ->count();

        return [
            'user_id' => $user->id,
            'devices' => [
                'total' => $devices->count(),
                'online' => $deviceStatus['online'] ?? 0,
                'offline' => $deviceStatus['offline'] ?? 0,
                'maintenance' => $deviceStatus['maintenance'] ?? 0,
                'decommissioned' => $deviceStatus['decommissioned'] ?? 0,
            ],
            'power_consumption' => [
                'last_24h' => round($power24h, 2),
                'last_7d' => round($power7d, 2),
                'last_30d' => round($power30d, 2),
            ],
            'alerts' => [
                'active' => $activeAlerts,
                'recent_7d' => $recentAlerts,
            ],
            'generated_at' => $now->toISOString(),
        ];
    }

    public function getSystemWideMetrics(): array
    {
        $now = now();
        $last24Hours = $now->copy()->subDay();

        // Device statistics
        $totalDevices = Device::count();
        $onlineDevices = Device::where('status', 'online')->count();
        $offlineDevices = Device::where('status', 'offline')->count();

        // Power consumption
        $totalPower24h = PowerRecord::whereBetween('ts', [$last24Hours, $now])
            ->sum('power');

        // Alert statistics
        $activeAlerts = Alert::where('status', 'active')->count();
        $alerts24h = Alert::whereBetween('triggered_at', [$last24Hours, $now])->count();

        // User statistics
        $totalUsers = User::count();
        $activeUsers = User::where('last_login_at', '>=', $last24Hours)->count();

        return [
            'devices' => [
                'total' => $totalDevices,
                'online' => $onlineDevices,
                'offline' => $offlineDevices,
                'online_percentage' => $totalDevices > 0 ? round(($onlineDevices / $totalDevices) * 100, 2) : 0,
            ],
            'power_consumption' => [
                'total_24h' => round($totalPower24h, 2),
                'avg_per_device' => $totalDevices > 0 ? round($totalPower24h / $totalDevices, 2) : 0,
            ],
            'alerts' => [
                'active' => $activeAlerts,
                'last_24h' => $alerts24h,
            ],
            'users' => [
                'total' => $totalUsers,
                'active_24h' => $activeUsers,
                'active_percentage' => $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 2) : 0,
            ],
            'generated_at' => $now->toISOString(),
        ];
    }

    public function getDeviceEfficiencyReport(Device $device, Carbon $from, Carbon $to): array
    {
        $records = PowerRecord::where('device_id', $device->id)
            ->whereBetween('ts', [$from, $to])
            ->orderBy('ts')
            ->get();

        if ($records->isEmpty()) {
            return [
                'device_id' => $device->id,
                'device_name' => $device->name,
                'efficiency_score' => 0,
                'recommendations' => ['No data available for analysis'],
            ];
        }

        // Calculate efficiency metrics
        $avgPower = $records->avg('power');
        $maxPower = $records->max('power');
        $minPower = $records->min('power');
        $powerVariance = $records->std('power');
        
        // Efficiency score based on power stability and usage patterns
        $powerStability = max(0, 100 - ($powerVariance / $avgPower * 100));
        $usageEfficiency = min(100, ($avgPower / $maxPower) * 100);
        
        $efficiencyScore = round(($powerStability + $usageEfficiency) / 2, 2);

        // Generate recommendations
        $recommendations = [];
        
        if ($powerVariance > $avgPower * 0.3) {
            $recommendations[] = 'High power variance detected. Consider checking for power fluctuations.';
        }
        
        if ($avgPower > $maxPower * 0.8) {
            $recommendations[] = 'Device operating near maximum capacity. Consider load balancing.';
        }
        
        if ($efficiencyScore < 70) {
            $recommendations[] = 'Low efficiency score. Review device configuration and usage patterns.';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'Device operating efficiently within normal parameters.';
        }

        return [
            'device_id' => $device->id,
            'device_name' => $device->name,
            'efficiency_score' => $efficiencyScore,
            'metrics' => [
                'avg_power' => round($avgPower, 2),
                'max_power' => round($maxPower, 2),
                'min_power' => round($minPower, 2),
                'power_variance' => round($powerVariance, 2),
                'power_stability' => round($powerStability, 2),
                'usage_efficiency' => round($usageEfficiency, 2),
            ],
            'recommendations' => $recommendations,
        ];
    }
}
