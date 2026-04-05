<?php

namespace App\Models;

use App\Enums\AlertSeverity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorAlert extends Model
{
    /** @use HasFactory<\Database\Factories\SensorAlertFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'sensor_id',
        'tenant_id',
        'type',
        'severity',
        'message',
        'value',
        'threshold',
        'triggered_at',
        'acknowledged_at',
        'acknowledged_by',
        'resolved_at',
        'is_active',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'severity' => AlertSeverity::class,
            'value' => 'decimal:4',
            'threshold' => 'decimal:4',
            'triggered_at' => 'datetime',
            'acknowledged_at' => 'datetime',
            'resolved_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class);
    }

    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }
}
