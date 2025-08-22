<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\{Device, PowerRecord, EventLog, Ticket};
use Illuminate\Http\Request;

final class DeviceController extends Controller
{
    public function index(Request $req)
    {
        $q = Device::query();
        
        // Filter by status
        if ($s = $req->query('status')) {
            $q->where('status', $s);
        }
        
        // Filter by tags
        if ($tags = $req->query('tags')) {
            $tagArray = explode(',', $tags);
            foreach ($tagArray as $tag) {
                $q->whereJsonContains('tags', trim($tag));
            }
        }
        
        // Filter by user (for multi-tenant later)
        if ($userId = $req->query('user_id')) {
            $q->where('user_id', $userId);
        }

        // with latest snapshot: eager load one latest record per device
        $devices = $q->with(['latestRecord', 'user:id,name'])
            ->orderByDesc('last_seen_at')
            ->paginate(20);
            
        return response()->json($devices);
    }

    public function show(Request $req, string $id)
    {
        $from = $req->query('from');
        $to = $req->query('to');
        $limit = min((int) $req->input('limit', 1000), 5000);

        $device = Device::with(['latestRecord', 'user:id,name'])->findOrFail($id);

        // Enforce bounded time range for telemetry queries
        if ($from && $to) {
            $maxDays = (int) env('RAW_TELEMETRY_DAYS_MAX', 7);
            if (strtotime($to) - strtotime($from) > $maxDays * 86400) {
                return response()->json(['error' => 'window_too_large', 'max_days' => $maxDays], 422);
            }
            
            $records = PowerRecord::query()
                ->where('device_id', $id)
                ->whereBetween('ts', [$from, $to])
                ->orderBy('ts')
                ->limit($limit)
                ->get();
                
            // Calculate stats for the time window
            $stats = PowerRecord::query()
                ->where('device_id', $id)
                ->whereBetween('ts', [$from, $to])
                ->selectRaw('
                    MIN(power) as min_power,
                    MAX(power) as max_power,
                    AVG(power) as avg_power,
                    MIN(current) as min_current,
                    MAX(current) as max_current,
                    AVG(current) as avg_current,
                    MIN(voltage) as min_voltage,
                    MAX(voltage) as max_voltage,
                    AVG(voltage) as avg_voltage,
                    COUNT(*) as record_count
                ')
                ->first();
        } else {
            // Default to last 24 hours
            $defaultFrom = now()->subDay();
            $defaultTo = now();
            
            $records = PowerRecord::query()
                ->where('device_id', $id)
                ->whereBetween('ts', [$defaultFrom, $defaultTo])
                ->orderBy('ts')
                ->limit($limit)
                ->get();
                
            $stats = PowerRecord::query()
                ->where('device_id', $id)
                ->whereBetween('ts', [$defaultFrom, $defaultTo])
                ->selectRaw('
                    MIN(power) as min_power,
                    MAX(power) as max_power,
                    AVG(power) as avg_power,
                    MIN(current) as min_current,
                    MAX(current) as max_current,
                    AVG(current) as avg_current,
                    MIN(voltage) as min_voltage,
                    MAX(voltage) as max_voltage,
                    AVG(voltage) as avg_voltage,
                    COUNT(*) as record_count
                ')
                ->first();
        }

        // Get recent logs for this device
        $recentLogs = EventLog::query()
            ->bySubject('device', $id)
            ->orderByDesc('occurred_at')
            ->limit(10)
            ->get();

        // Get linked tickets
        $linkedTickets = Ticket::query()
            ->where('device_id', $id)
            ->with(['creator:id,name', 'assignee:id,name'])
            ->orderByDesc('last_activity_at')
            ->limit(5)
            ->get();

        return response()->json([
            'device' => $device,
            'records' => $records,
            'stats' => $stats,
            'recent_logs' => $recentLogs,
            'linked_tickets' => $linkedTickets,
        ]);
    }
}
