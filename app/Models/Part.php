<?php

namespace App\Models;

use App\Concerns\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Part extends Model
{
    /** @use HasFactory<\Database\Factories\PartFactory> */
    use HasFactory, HasTenant;

    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'name',
        'part_number',
        'brand',
        'description',
        'unit',
        'stock_quantity',
        'min_stock',
        'unit_cost',
        'storage_location',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'unit_cost' => 'decimal:2',
        ];
    }

    public function isBelowMinStock(): bool
    {
        return $this->stock_quantity < $this->min_stock;
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function workOrderParts(): HasMany
    {
        return $this->hasMany(WorkOrderPart::class);
    }
}
