<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(ReferralVerification::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ReferralActivity::class);
    }

    public function authorizedBenefits(): HasMany
    {
        return $this->hasMany(BenefitHistory::class, 'authorized_by');
    }
}
