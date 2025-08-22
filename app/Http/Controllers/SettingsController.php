<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;

final class SettingsController extends Controller
{
    public function resolve(Request $req)
    {
        $data = $req->validate([
            'keys' => 'required|array|min:1|max:50',
            'keys.*' => 'string|max:100',
            'device_id' => 'nullable|integer',
            'user_id' => 'nullable|integer',
        ]);
        $keys = $data['keys'];
        $deviceId = $data['device_id'] ?? null;
        $userId = $data['user_id'] ?? null;

        // Pull all candidates and apply precedence: device > user > global
        $settings = AppSetting::query()
            ->whereIn('key', $keys)
            ->where(function ($q) use ($deviceId, $userId) {
                $q->when($deviceId, fn($qq) => $qq->orWhere(fn($w) => $w->where('scope_type', 'device')->where('scope_id', $deviceId)))
                    ->when($userId, fn($qq) => $qq->orWhere(fn($w) => $w->where('scope_type', 'user')->where('scope_id', $userId)))
                    ->orWhere(fn($w) => $w->where('scope_type', 'global')->whereNull('scope_id'));
            })
            ->orderByRaw("CASE scope_type WHEN 'device' THEN 1 WHEN 'user' THEN 2 ELSE 3 END")
            ->get()
            ->groupBy('key')
            ->map(fn($rows) => optional($rows->first())->value);

        return response()->json($settings);
    }
}
