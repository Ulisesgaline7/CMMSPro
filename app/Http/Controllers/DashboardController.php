<?php

namespace App\Http\Controllers;

use App\Enums\AssetCriticality;
use App\Enums\AssetStatus;
use App\Enums\WorkOrderStatus;
use App\Enums\WorkOrderType;
use App\Models\Asset;
use App\Models\MaintenancePlan;
use App\Models\Part;
use App\Models\WorkOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }

        // ── Work Order KPIs ──────────────────────────────────────────────────
        $woStats = [
            'total'           => WorkOrder::count(),
            'pending'         => WorkOrder::where('status', WorkOrderStatus::Pending)->count(),
            'in_progress'     => WorkOrder::where('status', WorkOrderStatus::InProgress)->count(),
            'completed_today' => WorkOrder::where('status', WorkOrderStatus::Completed)
                ->whereDate('completed_at', today())
                ->count(),
            'overdue'         => WorkOrder::whereIn('status', [WorkOrderStatus::Pending, WorkOrderStatus::InProgress])
                ->where('due_date', '<', now())
                ->count(),
        ];

        // ── WO by status (for donut chart) ───────────────────────────────────
        $woByStatus = WorkOrder::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->status->value => (int) $row->count])
            ->toArray();

        // ── WO by type ───────────────────────────────────────────────────────
        $woByType = WorkOrder::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->type->value => (int) $row->count])
            ->toArray();

        // ── WO by priority ───────────────────────────────────────────────────
        $woByPriority = WorkOrder::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->priority->value => (int) $row->count])
            ->toArray();

        // ── Asset stats ──────────────────────────────────────────────────────
        $assetStats = [
            'total'             => Asset::count(),
            'active'            => Asset::where('status', AssetStatus::Active)->count(),
            'under_maintenance' => Asset::where('status', AssetStatus::UnderMaintenance)->count(),
            'critical'          => Asset::where('criticality', AssetCriticality::Critical)->count(),
        ];

        // ── Critical assets (top 5) ──────────────────────────────────────────
        $criticalAssets = Asset::with('location:id,name')
            ->where('criticality', AssetCriticality::Critical)
            ->select('id', 'name', 'code', 'status', 'criticality', 'location_id')
            ->limit(5)
            ->get();

        // ── Low-stock parts ──────────────────────────────────────────────────
        $lowStockParts = Part::whereColumn('stock_quantity', '<=', 'min_stock')
            ->select('id', 'name', 'part_number', 'stock_quantity', 'min_stock', 'unit')
            ->orderByRaw('CAST(stock_quantity AS SIGNED) - CAST(min_stock AS SIGNED) ASC')
            ->limit(5)
            ->get();

        // ── Recent work orders ───────────────────────────────────────────────
        $recentWorkOrders = WorkOrder::with([
            'asset:id,name,code',
            'assignedTo:id,name',
        ])
            ->select('id', 'code', 'title', 'type', 'status', 'priority', 'due_date', 'asset_id', 'assigned_to', 'created_at')
            ->latest()
            ->limit(8)
            ->get();

        // ── My work orders (for Technician role) ─────────────────────────────
        $myWorkOrders = WorkOrder::with(['asset:id,name,code'])
            ->where('assigned_to', $user?->id)
            ->whereIn('status', [WorkOrderStatus::Pending, WorkOrderStatus::InProgress])
            ->select('id', 'code', 'title', 'type', 'status', 'priority', 'due_date', 'asset_id')
            ->orderByRaw("FIELD(status, 'in_progress', 'pending')")
            ->orderByRaw("FIELD(priority, 'critical', 'high', 'medium', 'low')")
            ->limit(10)
            ->get();

        // ── Reliability KPIs ─────────────────────────────────────────────────
        $operatingHours90d = 90 * 8; // 720 hours

        $correctiveCount90d = WorkOrder::where('type', WorkOrderType::Corrective)
            ->where('created_at', '>=', now()->subDays(90))
            ->count();

        $mtbf = round($operatingHours90d / max($correctiveCount90d, 1), 1);

        $mttr = round(
            (float) (WorkOrder::where('type', WorkOrderType::Corrective)
                ->where('status', WorkOrderStatus::Completed)
                ->whereNotNull('actual_duration')
                ->avg('actual_duration') ?? 0) / 60,
            1
        );

        $oee = round(($assetStats['active'] / max($assetStats['total'], 1)) * 100, 1);

        // ── Upcoming maintenance plans ────────────────────────────────────────
        $upcomingMaintenance = MaintenancePlan::with('asset:id,name,code')
            ->where('is_active', true)
            ->whereNotNull('next_execution_date')
            ->where('next_execution_date', '>=', today())
            ->select('id', 'name', 'type', 'priority', 'next_execution_date', 'asset_id', 'estimated_duration')
            ->orderBy('next_execution_date')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'woStats'             => $woStats,
            'woByStatus'          => $woByStatus,
            'woByType'            => $woByType,
            'woByPriority'        => $woByPriority,
            'assetStats'          => $assetStats,
            'criticalAssets'      => $criticalAssets,
            'lowStockParts'       => $lowStockParts,
            'recentWorkOrders'    => $recentWorkOrders,
            'myWorkOrders'        => $myWorkOrders,
            'upcomingMaintenance' => $upcomingMaintenance,
            'reliability'         => [
                'mtbf' => $mtbf,
                'mttr' => $mttr,
                'oee'  => $oee,
            ],
            'userRole' => $user?->role?->value ?? 'reader',
        ]);
    }
}
