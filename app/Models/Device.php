<?php declare(strict_types=1);

namespace App\Models;

use App\Enums\DeviceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'uuid',
        'user_id',
        'name',
        'slug',
        'status',
        'secret_hash',
        'secret_version',
        'last_rotated_at',
        'last_seen_at',
        'firmware',
        'model',
        'location',
        'tags',
        'metadata',
    ];
    protected $hidden = ['secret_hash'];

    protected $casts = [
        'status' => DeviceStatus::class,
        'tags' => 'array',
        'metadata' => 'array',
        'last_rotated_at' => 'immutable_datetime',
        'last_seen_at' => 'immutable_datetime',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function records(): HasMany
    {
        return $this->hasMany(PowerRecord::class);
    }
    public function latestRecord(): HasOne
    {
        return $this->hasOne(PowerRecord::class)->latestOfMany('ts');
    }
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    // Scopes
    public function scopeStatus($q, DeviceStatus|string $status)
    {
        return $q->where('status', $status instanceof DeviceStatus ? $status->value : $status);
    }

    // Helpers
    public function markOnline(): void
    {
        $this->forceFill(['status' => DeviceStatus::Online, 'last_seen_at' => now()])->save();
    }
    public function markOffline(): void
    {
        $this->forceFill(['status' => DeviceStatus::Offline])->save();
    }
    public function isDecommissioned(): bool
    {
        return $this->status === DeviceStatus::Decommissioned;
    }
}
