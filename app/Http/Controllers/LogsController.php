<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EventLog;
use Illuminate\Http\Request;

final class LogsController extends Controller
{
    public function index(Request $req)
    {
        $kind = $req->query('subject_type');
        $id = $req->query('subject_id');
        $from = $req->query('from');
        $to = $req->query('to');

        $q = EventLog::query()->where('subject_type', $kind)->where('subject_id', $id)->orderByDesc('occurred_at');
        $maxDays = (int) env('RAW_LOG_WINDOW_DAYS_MAX', 7);
        if ($from && $to && (strtotime($to) - strtotime($from) > $maxDays * 86400)) {
            return response()->json(['error' => 'window_too_large', 'max_days' => $maxDays], 422);
        }
        if ($from && $to)
            $q->whereBetween('occurred_at', [$from, $to]);

        return response()->json($q->paginate(50));
    }
}
