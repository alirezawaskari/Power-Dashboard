<?php declare(strict_types=1);

namespace App\Models;

use App\Enums\AlertStatus;
use App\Enums\AlertType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'type',
        'title',
        'message',
        'threshold_value',
        'current_value',
        'status',
        'triggered_at',
        'acknowledged_at',
        'resolved_at',
        'escalation_level',
        'notification_sent',
    ];

    protected $casts = [
        'type' => AlertType::class,
        'status' => AlertStatus::class,
        'threshold_value' => 'float',
        'current_value' => 'float',
        'triggered_at' => 'immutable_datetime',
        'acknowledged_at' => 'immutable_datetime',
        'resolved_at' => 'immutable_datetime',
        'escalation_level' => 'integer',
        'notification_sent' => 'boolean',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', AlertStatus::Active);
    }

    public function scopeByType($query, AlertType $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByDevice($query, int $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeUnacknowledged($query)
    {
        return $query->whereNull('acknowledged_at');
    }

    // Methods
    public function acknowledge(): void
    {
        $this->update([
            'status' => AlertStatus::Acknowledged,
            'acknowledged_at' => now(),
        ]);
    }

    public function resolve(): void
    {
        $this->update([
            'status' => AlertStatus::Resolved,
            'resolved_at' => now(),
        ]);
    }

    public function escalate(): void
    {
        $this->update([
            'escalation_level' => $this->escalation_level + 1,
        ]);
    }

    public function markNotificationSent(): void
    {
        $this->update(['notification_sent' => true]);
    }

    public function isActive(): bool
    {
        return $this->status === AlertStatus::Active;
    }

    public function isAcknowledged(): bool
    {
        return $this->status === AlertStatus::Acknowledged;
    }

    public function isResolved(): bool
    {
        return $this->status === AlertStatus::Resolved;
    }

    public function needsEscalation(): bool
    {
        return $this->isActive() && 
               $this->triggered_at && 
               $this->triggered_at->diffInMinutes(now()) > (30 * $this->escalation_level);
    }
}
