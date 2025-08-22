<?php declare(strict_types=1);

namespace App\Models;

use App\Enums\{DeviceStatus, EventType};
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

    // State Machine Methods
    public function markOnline(): void
    {
        if ($this->status === DeviceStatus::Decommissioned) {
            throw new \InvalidArgumentException('Cannot mark decommissioned device as online');
        }
        
        $this->forceFill(['status' => DeviceStatus::Online, 'last_seen_at' => now()])->save();
        
        // Log the state change
        EventLog::create([
            'type' => EventType::DeviceOnline,
            'actor_type' => 'device',
            'actor_id' => $this->id,
            'subject_type' => 'device',
            'subject_id' => $this->id,
            'message' => 'Device came online',
            'context' => ['device_name' => $this->name],
            'occurred_at' => now(),
        ]);
    }

    public function markOffline(): void
    {
        if ($this->status === DeviceStatus::Decommissioned) {
            return; // Already offline
        }
        
        $this->forceFill(['status' => DeviceStatus::Offline])->save();
        
        // Log the state change
        EventLog::create([
            'type' => EventType::DeviceOffline,
            'actor_type' => 'system',
            'actor_id' => null,
            'subject_type' => 'device',
            'subject_id' => $this->id,
            'message' => 'Device went offline',
            'context' => ['device_name' => $this->name, 'last_seen' => $this->last_seen_at],
            'occurred_at' => now(),
        ]);
    }

    public function setMaintenance(bool $maintenance = true): void
    {
        if ($this->status === DeviceStatus::Decommissioned) {
            throw new \InvalidArgumentException('Cannot set maintenance on decommissioned device');
        }
        
        $newStatus = $maintenance ? DeviceStatus::Maintenance : DeviceStatus::Offline;
        $this->forceFill(['status' => $newStatus])->save();
        
        // Log the state change
        EventLog::create([
            'type' => $maintenance ? EventType::DeviceMaintenanceOn : EventType::DeviceMaintenanceOff,
            'actor_type' => 'user',
            'actor_id' => auth()->id(),
            'subject_type' => 'device',
            'subject_id' => $this->id,
            'message' => $maintenance ? 'Device put in maintenance' : 'Device taken out of maintenance',
            'context' => ['device_name' => $this->name],
            'occurred_at' => now(),
        ]);
    }

    public function decommission(): void
    {
        $this->forceFill(['status' => DeviceStatus::Decommissioned])->save();
        
        // Log the state change
        EventLog::create([
            'type' => EventType::DeviceDecommissioned,
            'actor_type' => 'user',
            'actor_id' => auth()->id(),
            'subject_type' => 'device',
            'subject_id' => $this->id,
            'message' => 'Device decommissioned',
            'context' => ['device_name' => $this->name],
            'occurred_at' => now(),
        ]);
    }

    // Helper methods
    public function isDecommissioned(): bool
    {
        return $this->status === DeviceStatus::Decommissioned;
    }

    public function isInMaintenance(): bool
    {
        return $this->status === DeviceStatus::Maintenance;
    }

    public function isOnline(): bool
    {
        return $this->status === DeviceStatus::Online;
    }

    public function isOffline(): bool
    {
        return $this->status === DeviceStatus::Offline;
    }

    // Check if device should be marked offline based on heartbeat policy
    public function shouldBeOffline(): bool
    {
        if ($this->status === DeviceStatus::Online && $this->last_seen_at) {
            $heartbeatThreshold = AppSetting::resolve('device.heartbeat_threshold_minutes', (string) $this->id, (string) $this->user_id) ?? 5;
            return $this->last_seen_at->addMinutes($heartbeatThreshold)->isPast();
        }
        return false;
    }
}
