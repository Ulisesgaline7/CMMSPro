<?php

namespace App\Models;

use App\Concerns\HasTenant;
use App\Enums\AssetCriticality;
use App\Enums\AssetStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    /** @use HasFactory<\Database\Factories\AssetFactory> */
    use HasFactory, HasTenant;

    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'location_id',
        'asset_category_id',
        'parent_id',
        'name',
        'code',
        'serial_number',
        'brand',
        'model',
        'manufacture_year',
        'purchase_date',
        'installation_date',
        'warranty_expires_at',
        'purchase_cost',
        'status',
        'criticality',
        'specs',
        'notes',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => AssetStatus::class,
            'criticality' => AssetCriticality::class,
            'specs' => 'array',
            'purchase_date' => 'date',
            'installation_date' => 'date',
            'warranty_expires_at' => 'date',
            'purchase_cost' => 'decimal:2',
        ];
    }

    public function isOperational(): bool
    {
        return $this->status->isOperational();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Asset::class, 'parent_id');
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function maintenancePlans(): HasMany
    {
        return $this->hasMany(MaintenancePlan::class);
    }
}
