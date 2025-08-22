<?php declare(strict_types=1);

namespace App\Models;

use App\Enums\SettingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppSetting extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['scope_type', 'scope_id', 'key', 'value', 'updated_by', 'updated_at'];
    protected $casts = [
        'scope_type' => SettingScope::class,
        'value' => 'array',
        'updated_at' => 'immutable_datetime',
    ];

    public function scopeForDevice($q, string $deviceId)
    {
        return $q->where('scope_type', SettingScope::Device)->where('scope_id', $deviceId);
    }
    public function scopeForUser($q, string $userId)
    {
        return $q->where('scope_type', SettingScope::User)->where('scope_id', $userId);
    }
    public function scopeGlobal($q)
    {
        return $q->where('scope_type', SettingScope::Global)->whereNull('scope_id');
    }

    // Resolve setting with precedence: Device > User > Global
    public static function resolve(string $key, ?string $deviceId = null, ?string $userId = null): mixed
    {
        // Try device-specific setting first
        if ($deviceId) {
            $setting = static::forDevice($deviceId)->where('key', $key)->first();
            if ($setting) {
                return $setting->value;
            }
        }

        // Try user-specific setting
        if ($userId) {
            $setting = static::forUser($userId)->where('key', $key)->first();
            if ($setting) {
                return $setting->value;
            }
        }

        // Fall back to global setting
        $setting = static::global()->where('key', $key)->first();
        return $setting ? $setting->value : null;
    }

    // Set setting with scope
    public static function set(string $key, mixed $value, SettingScope $scope, ?string $scopeId = null, ?string $updatedBy = null): self
    {
        return static::updateOrCreate(
            [
                'key' => $key,
                'scope_type' => $scope,
                'scope_id' => $scopeId,
            ],
            [
                'value' => $value,
                'updated_by' => $updatedBy,
                'updated_at' => now(),
            ]
        );
    }
}
