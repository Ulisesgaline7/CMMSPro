<?php

namespace App\Models;

use App\Enums\TenantPlan;
use App\Enums\TenantStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    /** @use HasFactory<\Database\Factories\TenantFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'slug',
        'subdomain',
        'custom_domain',
        'custom_domain_verified',
        'plan',
        'status',
        'settings',
        'max_users',
        'max_assets',
        'trial_ends_at',
        'logo_path',
        'primary_color',
        'secondary_color',
        'brand_name',
        'white_label_level',
        'reseller_id',
        'stripe_customer_id',
        'billing_email',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'plan' => TenantPlan::class,
            'status' => TenantStatus::class,
            'settings' => 'array',
            'trial_ends_at' => 'datetime',
            'white_label_level' => 'integer',
            'custom_domain_verified' => 'boolean',
        ];
    }

    public function isOperational(): bool
    {
        return $this->status->isOperational();
    }

    public function hasReachedUserLimit(): bool
    {
        return $this->users()->count() >= $this->max_users;
    }

    public function hasReachedAssetLimit(): bool
    {
        return $this->assets()->count() >= $this->max_assets;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function assetCategories(): HasMany
    {
        return $this->hasMany(AssetCategory::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function parts(): HasMany
    {
        return $this->hasMany(Part::class);
    }

    public function maintenancePlans(): HasMany
    {
        return $this->hasMany(MaintenancePlan::class);
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(TenantModule::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function hasModule(string $key): bool
    {
        return $this->modules()->where('module_key', $key)->where('is_active', true)->exists();
    }

    /** @return array<int, string> */
    public function activeModules(): array
    {
        return $this->modules()->where('is_active', true)->pluck('module_key')->toArray();
    }
}
