<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'password', 'tenant_id', 'role', 'status', 'phone', 'employee_code', 'is_super_admin'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'role' => UserRole::class,
            'status' => UserStatus::class,
            'is_super_admin' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(UserCertification::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(UserSkill::class);
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }

    public function hasRole(UserRole $role): bool
    {
        return $this->role === $role;
    }

    public function canManageWorkOrders(): bool
    {
        return $this->role->canManageWorkOrders();
    }

    public function canExecuteWorkOrders(): bool
    {
        return $this->role->canExecuteWorkOrders();
    }

    public function canManageAssets(): bool
    {
        return $this->role->canManageAssets();
    }

    public function canViewReports(): bool
    {
        return $this->role->canViewReports();
    }
}
