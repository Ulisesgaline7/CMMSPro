<?php

namespace App\Models;

use App\Concerns\HasTenant;
use App\Enums\SensorStatus;
use App\Enums\SensorType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sensor extends Model
{
    /** @use HasFactory<\Database\Factories\SensorFactory> */
    use HasFactory, HasTenant;

    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'asset_id',
        'code',
        'name',
        'type',
        'unit',
        'status',
        'min_threshold',
        'max_threshold',
        'warning_threshold_low',
        'warning_threshold_high',
        'sampling_interval_seconds',
        'last_reading_value',
        'last_reading_at',
        'notes',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'type' => SensorType::class,
            'status' => SensorStatus::class,
            'min_threshold' => 'decimal:4',
            'max_threshold' => 'decimal:4',
            'warning_threshold_low' => 'decimal:4',
            'warning_threshold_high' => 'decimal:4',
            'last_reading_value' => 'decimal:4',
            'last_reading_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(SensorReading::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(SensorAlert::class);
    }

    public function latestReading(): ?SensorReading
    {
        return $this->readings()->latest('read_at')->first();
    }

    public function isAlerting(): bool
    {
        return $this->alerts()->where('is_active', true)->exists();
    }
}
