<?php

namespace App\Models;

use App\Concerns\HasTenant;
use App\Enums\LocationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    /** @use HasFactory<\Database\Factories\LocationFactory> */
    use HasFactory, HasTenant;

    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'parent_id',
        'name',
        'code',
        'type',
        'description',
        'address',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'type' => LocationType::class,
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
