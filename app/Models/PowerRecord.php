<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PowerRecord extends Model
{
    use HasFactory;

    protected $fillable = ['device_id', 'user_id', 'ts', 'current', 'voltage', 'power', 'sampling_ms', 'phase', 'flags'];
    public $timestamps = true; // created_at = ingest time

    protected $casts = [
        'ts' => 'immutable_datetime',
        'sampling_ms' => 'integer',
        'flags' => 'array',
        'current' => 'float',
        'voltage' => 'float',
        'power' => 'float',
    ];

    // Relations
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForDeviceBetween($q, string $deviceId, \DateTimeInterface $from, \DateTimeInterface $to)
    {
        return $q->where('device_id', $deviceId)->whereBetween('ts', [$from, $to]);
    }

    // Invariant safeguard (if service layer forgot to compute power)
    protected static function booted(): void
    {
        static::saving(function (self $m) {
            if (is_null($m->power) || $m->isDirty('current') || $m->isDirty('voltage')) {
                $c = (float) $m->current;
                $v = (float) $m->voltage;
                if ($c < 0 || $v < 0)
                    throw new \InvalidArgumentException('current/voltage must be non‑negative');
                $m->power = $c * $v;
            }
        });
    }
}
