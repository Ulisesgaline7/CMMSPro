<?php

namespace App\Http\Controllers\Predictive;

use App\Enums\WorkOrderStatus;
use App\Enums\WorkOrderType;
use App\Http\Controllers\Controller;
use App\Jobs\CalculateAssetReliability;
use App\Models\Asset;
use App\Models\AssetReliabilityMetric;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssetAnalysisController extends Controller
{
    public function show(Asset $asset): View
    {
        $metric = AssetReliabilityMetric::where('asset_id', $asset->id)
            ->latest('calculated_at')
            ->first();

        $monthExpression = DB::getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', created_at) as month"
            : "DATE_FORMAT(created_at, '%Y-%m') as month";

        $woByMonth = WorkOrder::withoutGlobalScopes()
            ->where('asset_id', $asset->id)
            ->where('created_at', '>=', now()->subMonths(12))
            ->selectRaw($monthExpression.', COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $recentCorrectiveWOs = WorkOrder::withoutGlobalScopes()
            ->where('asset_id', $asset->id)
            ->where('type', WorkOrderType::Corrective)
            ->where('status', WorkOrderStatus::Completed)
            ->latest('completed_at')
            ->limit(10)
            ->get();

        return view('predictive.assets.show', [
            'asset' => $asset,
            'metric' => $metric,
            'woByMonth' => $woByMonth,
            'recentCorrectiveWOs' => $recentCorrectiveWOs,
        ]);
    }

    public function recalculate(Asset $asset): RedirectResponse
    {
        CalculateAssetReliability::dispatch($asset);

        return back()->with('success', 'Cálculo de confiabilidad disparado. Se actualizará en breve.');
    }
}
