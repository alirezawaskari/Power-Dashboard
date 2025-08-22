<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class LimitIngestSize
{
    public function handle(Request $request, Closure $next)
    {
        $max = (int) env('INGEST_MAX_BYTES', 32768);
        $lenHeader = $request->header('Content-Length');
        $len = is_numeric($lenHeader) ? (int) $lenHeader : null;

        if ($len !== null && $len > $max) {
            return response()->json(['error' => 'payload_too_large'], 413);
        }

        if ($len === null) {
            $body = (string) $request->getContent();
            if (strlen($body) > $max) {
                return response()->json(['error' => 'payload_too_large'], 413);
            }
        }
        return $next($request);
    }
}
