<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['type', 'actor_type', 'actor_id', 'subject_type', 'subject_id', 'message', 'context', 'occurred_at'];
    protected $casts = ['context' => 'array', 'occurred_at' => 'immutable_datetime'];

    protected static function booted(): void
    {
        static::updating(fn() => false); // append-only
        static::deleting(fn() => false);
    }
}
