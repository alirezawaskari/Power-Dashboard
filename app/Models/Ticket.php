<?php declare(strict_types=1);

namespace App\Models;

use App\Enums\{TicketStatus, TicketPriority, ThreadMode};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    private const SNAPSHOT_MAX_BYTES = 262144; // ~256 KB

    protected $fillable = [
        'id',
        'user_id',
        'assignee_id',
        'device_id',
        'status',
        'priority',
        'thread_mode',
        'snapshot_json',
        'snapshot_version',
        'last_activity_at',
    ];
    public $timestamps = true;

    protected $casts = [
        'status' => TicketStatus::class,
        'priority' => TicketPriority::class,
        'thread_mode' => ThreadMode::class,
        'snapshot_json' => 'array',
        'last_activity_at' => 'immutable_datetime',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    // Relations
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    // Scopes
    public function scopeStatus($q, TicketStatus|string $status)
    {
        return $q->where('status', $status instanceof TicketStatus ? $status->value : $status);
    }

    // Invariants
    protected static function booted(): void
    {
        static::saving(function (self $t) {
            if ($t->thread_mode === ThreadMode::SnapshotJson && !is_null($t->snapshot_json)) {
                $bytes = strlen(json_encode($t->snapshot_json, JSON_UNESCAPED_UNICODE));
                if ($bytes > self::SNAPSHOT_MAX_BYTES) {
                    throw new \InvalidArgumentException('snapshot_json exceeds maximum allowed size');
                }
            }
        });
    }

    // Convenience
    public function touchActivity(): void
    {
        $this->forceFill(['last_activity_at' => now()])->save();
    }
}
