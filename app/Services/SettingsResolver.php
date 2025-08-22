<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\AppSetting;

class SettingsResolver
{
    public function get(string $key, ?int $userId = null, ?int $deviceId = null, int $ttlSeconds = 120)
    {
        $cacheKey = $this->cacheKey($key, $userId, $deviceId);

        return Cache::remember($cacheKey, $ttlSeconds, function () use ($key, $userId, $deviceId) {
            // device override
            if ($deviceId) {
                $deviceSetting = AppSetting::query()
                    ->where('scope_type', 'device')
                    ->where('scope_id', $deviceId)
                    ->where('key', $key)
                    ->first();
                if ($deviceSetting)
                    return $deviceSetting->value;
            }
            // user override
            if ($userId) {
                $userSetting = AppSetting::query()
                    ->where('scope_type', 'user')
                    ->where('scope_id', $userId)
                    ->where('key', $key)
                    ->first();
                if ($userSetting)
                    return $userSetting->value;
            }
            // global
            $global = AppSetting::query()
                ->where('scope_type', 'global')
                ->whereNull('scope_id')
                ->where('key', $key)
                ->first();

            return $global?->value;
        });
    }

    public function bust(string $key, ?int $userId = null, ?int $deviceId = null): void
    {
        Cache::forget($this->cacheKey($key, $userId, $deviceId));
    }

    protected function cacheKey(string $key, ?int $userId, ?int $deviceId): string
    {
        return sprintf('settings:resolve:%s:%s:%s', $deviceId ?? 'none', $userId ?? 'none', $key);
    }
}
