<?php

namespace App\Http\Controllers\Predictive;

use App\Http\Controllers\Controller;
use App\Models\AssetReliabilityMetric;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $topRiskAssets = AssetReliabilityMetric::with('asset:id,name,code,criticality')
            ->orderByDesc('failure_probability_30d')
            ->limit(10)
            ->get();

        $avgMtbf = AssetReliabilityMetric::whereNotNull('mtbf_hours')->avg('mtbf_hours');
        $lowAvailability = AssetReliabilityMetric::where('availability_percent', '<', 90)->count();
        $assetsWithMetrics = AssetReliabilityMetric::distinct('asset_id')->count('asset_id');

        return view('predictive.dashboard', [
            'topRiskAssets' => $topRiskAssets,
            'avgMtbf' => $avgMtbf ? round((float) $avgMtbf, 1) : null,
            'lowAvailability' => $lowAvailability,
            'assetsWithMetrics' => $assetsWithMetrics,
        ]);
    }
}
