<?php

namespace App\Http\Controllers\Predictive;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetReliabilityMetric;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $metrics = AssetReliabilityMetric::with('asset:id,name,code,criticality')
            ->when($request->sort === 'risk', fn ($q) => $q->orderByDesc('failure_probability_30d'))
            ->when($request->sort === 'availability', fn ($q) => $q->orderBy('availability_percent'))
            ->when($request->sort === 'mtbf', fn ($q) => $q->orderBy('mtbf_hours'))
            ->when(! $request->sort, fn ($q) => $q->orderByDesc('calculated_at'))
            ->paginate(25)
            ->withQueryString();

        return view('predictive.report', [
            'metrics' => $metrics,
            'sort' => $request->sort,
        ]);
    }
}
