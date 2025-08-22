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
}
