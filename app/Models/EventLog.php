<?php declare(strict_types=1);

namespace App\Models;

use App\Enums\EventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['type', 'actor_type', 'actor_id', 'subject_type', 'subject_id', 'message', 'context', 'occurred_at'];
    protected $casts = [
        'type' => EventType::class,
        'context' => 'array', 
        'occurred_at' => 'immutable_datetime'
    ];

    protected static function booted(): void
    {
        static::updating(fn() => false); // append-only
        static::deleting(fn() => false);
    }

    // Scopes for filtering
    public function scopeByType($q, EventType $type)
    {
        return $q->where('type', $type);
    }

    public function scopeBySubject($q, string $subjectType, string $subjectId)
    {
        return $q->where('subject_type', $subjectType)->where('subject_id', $subjectId);
    }

    public function scopeByActor($q, string $actorType, string $actorId)
    {
        return $q->where('actor_type', $actorType)->where('actor_id', $actorId);
    }

    public function scopeInTimeRange($q, \DateTimeInterface $from, \DateTimeInterface $to)
    {
        return $q->whereBetween('occurred_at', [$from, $to]);
    }
}
