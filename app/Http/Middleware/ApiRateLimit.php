<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use App\Models\EventLog;
use App\Enums\EventType;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimit
{
    private const DEFAULT_LIMITS = [
        'devices:read' => 1000,      // 1000 requests per minute
        'devices:write' => 100,      // 100 requests per minute
        'tickets:read' => 500,       // 500 requests per minute
        'tickets:write' => 50,       // 50 requests per minute
        'settings:read' => 200,      // 200 requests per minute
        'settings:write' => 20,      // 20 requests per minute
        'notifications:read' => 300, // 300 requests per minute
        'ingest' => 1000,           // 1000 requests per minute (device ingest)
    ];

    public function handle(Request $request, Closure $next, string $scope = null): Response
    {
        $identifier = $this->getIdentifier($request);
        $limit = $this->getLimit($scope);
        $key = "api_rate_limit:{$scope}:{$identifier}";

        if (RateLimiter::tooManyAttempts($key, $limit)) {
            $this->logRateLimitExceeded($request, $scope, $identifier);
            
            return response()->json([
                'error' => 'rate_limit_exceeded',
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key),
            ], 429);
        }

        RateLimiter::hit($key, 60); // 1 minute window

        $response = $next($request);

        // Add rate limit headers
        $response->headers->set('X-RateLimit-Limit', $limit);
        $response->headers->set('X-RateLimit-Remaining', RateLimiter::remaining($key, $limit));
        $response->headers->set('X-RateLimit-Reset', time() + 60);

        return $response;
    }

    private function getIdentifier(Request $request): string
    {
        // For device authentication
        if ($device = $request->attributes->get('device')) {
            return "device:{$device->id}";
        }

        // For user authentication
        if ($userId = $request->attributes->get('auth.sub')) {
            return "user:{$userId}";
        }

        // For API key authentication
        if ($apiKey = $request->attributes->get('api_key')) {
            return "apikey:{$apiKey->id}";
        }

        // Fallback to IP address
        return "ip:" . $request->ip();
    }

    private function getLimit(?string $scope): int
    {
        if (!$scope) {
            return 100; // Default limit
        }

        return self::DEFAULT_LIMITS[$scope] ?? 100;
    }

    private function logRateLimitExceeded(Request $request, ?string $scope, string $identifier): void
    {
        Log::warning('API rate limit exceeded', [
            'scope' => $scope,
            'identifier' => $identifier,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'path' => $request->path(),
        ]);

        // Log to event log if we have user/device context
        $actorType = null;
        $actorId = null;

        if (str_starts_with($identifier, 'device:')) {
            $actorType = 'device';
            $actorId = str_replace('device:', '', $identifier);
        } elseif (str_starts_with($identifier, 'user:')) {
            $actorType = 'user';
            $actorId = str_replace('user:', '', $identifier);
        } elseif (str_starts_with($identifier, 'apikey:')) {
            $actorType = 'apikey';
            $actorId = str_replace('apikey:', '', $identifier);
        }

        if ($actorType && $actorId) {
            EventLog::create([
                'type' => EventType::IngestRateLimited->value,
                'actor_type' => $actorType,
                'actor_id' => $actorId,
                'subject_type' => 'api',
                'subject_id' => null,
                'message' => "API rate limit exceeded for scope: {$scope}",
                'context' => [
                    'scope' => $scope,
                    'identifier' => $identifier,
                    'ip' => $request->ip(),
                    'path' => $request->path(),
                ],
                'occurred_at' => now(),
            ]);
        }
    }
}
