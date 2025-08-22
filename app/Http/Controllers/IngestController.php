<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PowerRecord;
use App\Models\EventLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

final class IngestController extends Controller
{
    public function store(Request $req): Response
    {
        $device = $req->attributes->get('device');

        try {
            $data = $req->validate([
                'ts' => ['required', 'date'],
                'current' => ['required', 'numeric', 'gte:0'],
                'voltage' => ['required', 'numeric', 'gte:0'],
                'attributes' => ['array'],
            ]);
        } catch (\Throwable $e) {
            EventLog::create([
                'type' => 'ingest.rejected_schema',
                'actor_type' => 'device',
                'actor_id' => $device->id ?? null,
                'subject_type' => 'device',
                'subject_id' => $device->id ?? null,
                'message' => 'validation failed',
                'context' => ['error' => $e->getMessage()],
                'occurred_at' => now(),
            ]);
            throw $e;
        }

        // write as a single transaction
        return DB::transaction(function () use ($device, $data) {
            $power = (float) $data['current'] * (float) $data['voltage'];

            $rec = PowerRecord::create([
                'device_id' => $device->id,
                'user_id' => $device->user_id,   // invariant: matches device owner at ingest time
                'ts' => $data['ts'],
                'current' => $data['current'],
                'voltage' => $data['voltage'],
                'power' => $power,
                'attributes' => $data['attributes'] ?? [],
            ]);

            // presence
            $device->markOnline();

            // audit
            EventLog::create([
                'type' => 'ingest.accepted',
                'actor_type' => 'device',
                'actor_id' => $device->id,
                'subject_type' => 'device',
                'subject_id' => $device->id,
                'message' => 'telemetry accepted',
                'context' => ['record_id' => $rec->id, 'power' => $power],
                'occurred_at' => now(),
            ]);

            return response()->json(['ok' => true, 'id' => $rec->id], 202);
        });
    }
}
