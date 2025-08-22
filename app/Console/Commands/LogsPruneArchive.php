<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LogsPruneArchive extends Command
{
    protected $signature = 'logs:prune-archive {--days=90} {--no-archive}';
    protected $description = 'Archive then prune event_logs older than N days (chunked)';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);
        $archive = !$this->option('no-archive');
        $path = 'archives/event_logs/' . now()->format('Ymd_His') . "_lt_{$days}d.csv";

        $exported = 0;
        $deleted = 0;
        do {
            $rows = DB::table('event_logs')
                ->where('occurred_at', '<', $cutoff)
                ->orderBy('id')->limit(10000)->get();
            if ($rows->isEmpty())
                break;

            if ($archive) {
                if ($exported === 0) {
                    Storage::disk('local')->put($path, implode(',', [
                        'id',
                        'type',
                        'actor_type',
                        'actor_id',
                        'subject_type',
                        'subject_id',
                        'message',
                        'context',
                        'occurred_at',
                        'created_at',
                        'updated_at'
                    ]) . PHP_EOL);
                }
                $lines = $rows->map(function ($r) {
                    $csv = fn($v) => '"' . str_replace('"', '""', is_string($v) ? $v : json_encode($v)) . '"';
                    return implode(',', [
                        $r->id,
                        $csv($r->type),
                        $csv($r->actor_type),
                        $r->actor_id ?? '',
                        $csv($r->subject_type),
                        $r->subject_id ?? '',
                        $csv($r->message),
                        $csv($r->context),
                        $r->occurred_at,
                        $r->created_at,
                        $r->updated_at
                    ]);
                })->implode(PHP_EOL) . PHP_EOL;
                Storage::disk('local')->append($path, $lines);
                $exported += $rows->count();
            }

            $ids = $rows->pluck('id')->all();
            $deleted += DB::table('event_logs')->whereIn('id', $ids)->delete();
        } while (true);

        if ($archive)
            $this->info("Archive: storage/app/{$path} (exported {$exported})");
        $this->info("Deleted: {$deleted}");
        return self::SUCCESS;
    }
}