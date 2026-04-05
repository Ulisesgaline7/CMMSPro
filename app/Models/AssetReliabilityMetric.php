<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetReliabilityMetric extends Model
{
    /** @use HasFactory<\Database\Factories\AssetReliabilityMetricFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'asset_id',
        'calculated_at',
        'period_start',
        'period_end',
        'total_work_orders',
        'corrective_count',
        'total_downtime_minutes',
        'total_repair_time_minutes',
        'mtbf_hours',
        'mttr_hours',
        'availability_percent',
        'failure_rate',
        'recommended_pm_interval_days',
        'failure_probability_30d',
        'anomaly_score',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'calculated_at' => 'datetime',
            'period_start' => 'date',
            'period_end' => 'date',
            'mtbf_hours' => 'decimal:2',
            'mttr_hours' => 'decimal:2',
            'availability_percent' => 'decimal:2',
            'failure_rate' => 'decimal:8',
            'failure_probability_30d' => 'decimal:2',
            'anomaly_score' => 'decimal:2',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
