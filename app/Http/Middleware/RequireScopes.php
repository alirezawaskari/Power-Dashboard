<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Auth\JwtVerifier;
use Symfony\Component\HttpFoundation\Response;

final class RequireScopes
{
    public function __construct(private readonly JwtVerifier $verifier)
    {
    }

    public function handle(Request $request, Closure $next, ...$requiredScopes): Response
    {
        $token = $request->bearerToken();
        if (!$token)
            return $this->unauthorized('Missing bearer token');

        try {
            $payload = ($this->verifier ?? JwtVerifier::fromConfig())->verify($token);
        } catch (\Throwable $e) {
            return $this->unauthorized('Invalid token');
        }

        $sc = $payload['scp'] ?? ($payload['scope'] ?? []);
        $granted = is_array($sc) ? $sc : array_filter(preg_split('/\s+/', (string) $sc));
        foreach ($requiredScopes as $need) {
            if (!in_array($need, $granted, true)) {
                return response()->json(['error' => 'insufficient_scope', 'needed' => $requiredScopes], 403);
            }
        }

        // attach auth info for controllers
        $request->attributes->set('auth.sub', $payload['sub'] ?? null);
        $request->attributes->set('auth.scopes', $granted);
        return $next($request);
    }

    private function unauthorized(string $msg): Response
    {
        return response()->json(['error' => 'unauthorized', 'message' => $msg], 401);
    }
}
