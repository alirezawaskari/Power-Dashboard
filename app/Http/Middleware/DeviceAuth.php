<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use App\Models\Device;
use App\Models\EventLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

final class DeviceAuth
{
    private function log(string $type, array $context = []): void
    {
        EventLog::create([
            'type' => $type,
            'actor_type' => 'device',
            'actor_id' => null,
            'subject_type' => 'device',
            'subject_id' => $context['device_id'] ?? null,
            'message' => $context['message'] ?? '',
            'context' => $context,
            'occurred_at' => now(),
        ]);
    }

    public function handle(Request $request, Closure $next): Response
    {
        $devId = $request->header('X-Device-ID') ?? $request->header('XDeviceID');
        $secret = $request->header('X-Device-Secret') ?? $request->header('XDeviceSecret');

        if (!$devId || !$secret) {
            $this->log('security.auth_failed_device', ['message' => 'missing headers']);
            return response()->json(['error' => 'unauthorized', 'message' => 'Missing device headers'], 401);
        }

        /** @var Device|null $device */
        $device = Device::query()->where('uuid', $devId)->first();
        if (!$device) {
            $this->log('security.auth_failed_device', ['device_id' => $devId, 'message' => 'unknown device']);
            return response()->json(['error' => 'unauthorized'], 401);
        }

        if ($device->isDecommissioned()) {
            $this->log('security.permission_denied', ['device_id' => $device->id, 'message' => 'decommissioned']);
            return response()->json(['error' => 'forbidden', 'message' => 'Device decommissioned'], 403);
        }

        if (!Hash::check($secret, $device->secret_hash)) {
            $this->log('security.auth_failed_device', ['device_id' => $device->id, 'message' => 'bad secret']);
            return response()->json(['error' => 'unauthorized', 'message' => 'Bad secret'], 401);
        }

        $request->attributes->set('device', $device);
        return $next($request);
    }
}
