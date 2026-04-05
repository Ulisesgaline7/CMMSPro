<?php

namespace App\Jobs;

use App\Enums\WorkOrderStatus;
use App\Enums\WorkOrderType;
use App\Models\Asset;
use App\Models\AssetReliabilityMetric;
use App\Models\WorkOrder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CalculateAssetReliability implements ShouldQueue
{
    use Queueable;

    public function __construct(public Asset $asset) {}

    public function handle(): void
    {
        $periodStart = now()->subMonths(12)->startOfDay();
        $periodEnd = now()->endOfDay();

        $allWorkOrders = WorkOrder::withoutGlobalScopes()
            ->where('asset_id', $this->asset->id)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->get();

        $correctiveWorkOrders = $allWorkOrders->filter(
            fn ($wo) => $wo->type === WorkOrderType::Corrective && $wo->status === WorkOrderStatus::Completed
        );

        $totalWorkOrders = $allWorkOrders->count();
        $correctiveCount = $correctiveWorkOrders->count();

        $totalRepairTimeMinutes = (int) $correctiveWorkOrders->sum(fn ($wo) => (float) ($wo->actual_duration ?? 0));

        $periodHours = 12 * 30 * 24; // 8640 hours
        $totalDowntimeHours = $totalRepairTimeMinutes / 60;

        $mtbf = $correctiveCount > 0
            ? ($periodHours - $totalDowntimeHours) / $correctiveCount
            : null;

        $mttr = $correctiveCount > 0
            ? $totalDowntimeHours / $correctiveCount
            : null;

        $availabilityPercent = (($periodHours - $totalDowntimeHours) / $periodHours) * 100;

        $failureRate = ($mtbf && $mtbf > 0) ? 1 / $mtbf : null;

        $failureProbability30d = $failureRate
            ? min(100.0, (1 - exp(-$failureRate * 720)) * 100)
            : null;

        $recommendedPmIntervalDays = $mtbf
            ? max(7, (int) ($mtbf * 0.8 / 24))
            : 30;

        AssetReliabilityMetric::updateOrCreate(
            [
                'asset_id' => $this->asset->id,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
            ],
            [
                'tenant_id' => $this->asset->tenant_id,
                'calculated_at' => now(),
                'total_work_orders' => $totalWorkOrders,
                'corrective_count' => $correctiveCount,
                'total_downtime_minutes' => (int) ($totalDowntimeHours * 60),
                'total_repair_time_minutes' => $totalRepairTimeMinutes,
                'mtbf_hours' => $mtbf ? round($mtbf, 2) : null,
                'mttr_hours' => $mttr ? round($mttr, 2) : null,
                'availability_percent' => round($availabilityPercent, 2),
                'failure_rate' => $failureRate ? round($failureRate, 8) : null,
                'recommended_pm_interval_days' => $recommendedPmIntervalDays,
                'failure_probability_30d' => $failureProbability30d ? round($failureProbability30d, 2) : null,
                'anomaly_score' => null,
            ]
        );
    }
}
