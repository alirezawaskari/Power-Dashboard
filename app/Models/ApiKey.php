<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'key_hash',
        'scopes',
        'last_used_at',
        'expires_at',
        'is_active',
    ];

    protected $hidden = ['key_hash'];

    protected $casts = [
        'scopes' => 'array',
        'last_used_at' => 'immutable_datetime',
        'expires_at' => 'immutable_datetime',
        'is_active' => 'boolean',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public static function generateKey(): string
    {
        return 'pk_' . Str::random(32);
    }

    public static function createForUser(User $user, string $name, array $scopes = [], ?string $expiresAt = null): array
    {
        $key = self::generateKey();
        $keyHash = Hash::make($key);

        $apiKey = static::create([
            'user_id' => $user->id,
            'name' => $name,
            'key_hash' => $keyHash,
            'scopes' => $scopes,
            'expires_at' => $expiresAt,
            'is_active' => true,
        ]);

        return [
            'id' => $apiKey->id,
            'key' => $key, // Only returned once
            'name' => $apiKey->name,
            'scopes' => $apiKey->scopes,
            'expires_at' => $apiKey->expires_at,
        ];
    }

    public function hasScope(string $scope): bool
    {
        return in_array($scope, $this->scopes ?? [], true);
    }

    public function hasAnyScope(array $scopes): bool
    {
        return !empty(array_intersect($scopes, $this->scopes ?? []));
    }

    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    public function revoke(): void
    {
        $this->update(['is_active' => false]);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }
}
