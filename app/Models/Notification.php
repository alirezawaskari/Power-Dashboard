<?php declare(strict_types=1);

namespace App\Models;

use App\Enums\{NotificationType, NotificationStatus};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'status',
        'read_at',
        'delivered_at',
        'failed_at',
        'retry_count',
        'max_retries',
        'channel', // email, sms, push, websocket
        'priority',
        'scheduled_at',
        'expires_at',
    ];

    protected $casts = [
        'type' => NotificationType::class,
        'status' => NotificationStatus::class,
        'data' => 'array',
        'read_at' => 'immutable_datetime',
        'delivered_at' => 'immutable_datetime',
        'failed_at' => 'immutable_datetime',
        'scheduled_at' => 'immutable_datetime',
        'expires_at' => 'immutable_datetime',
        'retry_count' => 'integer',
        'max_retries' => 'integer',
        'priority' => 'integer',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($q)
    {
        return $q->where('status', NotificationStatus::Pending);
    }

    public function scopeUnread($q)
    {
        return $q->whereNull('read_at');
    }

    public function scopeByType($q, NotificationType $type)
    {
        return $q->where('type', $type);
    }

    public function scopeByChannel($q, string $channel)
    {
        return $q->where('channel', $channel);
    }

    public function scopeExpired($q)
    {
        return $q->where('expires_at', '<', now());
    }

    public function scopeRetryable($q)
    {
        return $q->where('status', NotificationStatus::Failed)
                 ->whereRaw('retry_count < max_retries');
    }

    // Methods
    public function markAsRead(): void
    {
        $this->forceFill(['read_at' => now()])->save();
    }

    public function markAsDelivered(): void
    {
        $this->forceFill([
            'status' => NotificationStatus::Delivered,
            'delivered_at' => now()
        ])->save();
    }

    public function markAsFailed(): void
    {
        $this->forceFill([
            'status' => NotificationStatus::Failed,
            'failed_at' => now(),
            'retry_count' => $this->retry_count + 1
        ])->save();
    }

    public function canRetry(): bool
    {
        return $this->status === NotificationStatus::Failed && 
               $this->retry_count < $this->max_retries;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
