<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\{Device, PowerRecord};
use Illuminate\Http\Request;

final class DeviceController extends Controller
{
    public function index(Request $req)
    {
        $q = Device::query();
        if ($s = $req->query('status'))
            $q->where('status', $s);

        // with latest snapshot: eager load one latest record per device
        $devices = $q->with('latestRecord')->orderByDesc('last_seen_at')->paginate(20);
        return response()->json($devices);
    }

    public function show(Request $req, string $id)
    {
        $from = $req->query('from');
        $to = $req->query('to');
        $limit = min((int) $req->input('limit', 1000), 5000);


        $device = Device::with('latestRecord')->findOrFail($id);

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
        } else {
            $records = PowerRecord::query()
                ->where('device_id', $id)
                ->orderByDesc('ts')
                ->limit($limit)
                ->get();
        }

        return response()->json([
            'device' => $device,
            'records' => $records,
        ]);
    }
}
