<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\UserRole;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'role',
        'last_login_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => UserRole::class,
        'last_login_at' => 'immutable_datetime',
        'password' => 'hashed',
    ];

    // Relations
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }
    public function ticketsCreated(): HasMany
    {
        return $this->hasMany(Ticket::class, 'creator_id');
    }
    public function ticketsAssigned(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assignee_id');
    }

    // Scopes
    public function scopeRole($q, UserRole|string $role)
    {
        return $q->where('role', $role instanceof UserRole ? $role->value : $role);
    }
}
