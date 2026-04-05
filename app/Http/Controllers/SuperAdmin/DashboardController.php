<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\SubscriptionStatus;
use App\Enums\TenantStatus;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // ── Tenant counts ─────────────────────────────────────────────────
        $totalTenants = Tenant::withoutGlobalScopes()->count();
        $activeTenants = Tenant::withoutGlobalScopes()->where('status', TenantStatus::Active)->count();
        $trialTenants = Tenant::withoutGlobalScopes()->where('status', TenantStatus::Trial)->count();
        $suspendedTenants = Tenant::withoutGlobalScopes()->where('status', TenantStatus::Suspended)->count();

        // ── New + churn this month ────────────────────────────────────────
        $newTenantsThisMonth = Tenant::withoutGlobalScopes()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $churnCount = Tenant::withoutGlobalScopes()
            ->where('status', TenantStatus::Suspended)
            ->whereYear('updated_at', now()->year)
            ->whereMonth('updated_at', now()->month)
            ->count();

        // ── Users ─────────────────────────────────────────────────────────
        $totalUsers = User::withoutGlobalScopes()->count();

        // ── MRR ───────────────────────────────────────────────────────────
        $mrr = (float) Subscription::withoutGlobalScopes()
            ->where('status', SubscriptionStatus::Active)
            ->sum('total_monthly');

        $mrrPreviousMonth = (float) Subscription::withoutGlobalScopes()
            ->where('status', SubscriptionStatus::Active)
            ->whereDate('current_period_start', '<=', now()->subMonth())
            ->sum('total_monthly');

        $mrrGrowth = $mrrPreviousMonth > 0
            ? round((($mrr - $mrrPreviousMonth) / $mrrPreviousMonth) * 100, 1)
            : 0;

        // ── Past due ──────────────────────────────────────────────────────
        $pastDueCount = Subscription::withoutGlobalScopes()->where('status', SubscriptionStatus::PastDue)->count();
        $pastDueRevenue = (float) Subscription::withoutGlobalScopes()->where('status', SubscriptionStatus::PastDue)->sum('total_monthly');

        // ── Plan distribution ─────────────────────────────────────────────
        $planDistribution = Tenant::withoutGlobalScopes()
            ->selectRaw('plan, COUNT(*) as count')
            ->groupBy('plan')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->plan->value => (int) $row->count])
            ->toArray();

        // ── Platform totals ───────────────────────────────────────────────
        $totalWorkOrders = WorkOrder::withoutGlobalScopes()->count();
        $totalAssets = Asset::withoutGlobalScopes()->count();

        // ── Recent tenants ────────────────────────────────────────────────
        $recentTenants = Tenant::withoutGlobalScopes()
            ->with('subscription')
            ->withCount(['users', 'assets', 'workOrders'])
            ->latest()
            ->limit(15)
            ->get();

        // ── Adoption alerts (active tenants with 0 activity) ─────────────
        $adoptionAlerts = Tenant::withoutGlobalScopes()
            ->where('status', TenantStatus::Active)
            ->withCount(['workOrders', 'assets'])
            ->get()
            ->filter(fn ($t) => $t->work_orders_count === 0 || $t->assets_count === 0)
            ->take(5)
            ->values();

        return view('super-admin.dashboard', [
            'mrr' => $mrr,
            'mrrGrowth' => $mrrGrowth,
            'totalTenants' => $totalTenants,
            'activeTenants' => $activeTenants,
            'trialTenants' => $trialTenants,
            'suspendedTenants' => $suspendedTenants,
            'newTenantsThisMonth' => $newTenantsThisMonth,
            'churnCount' => $churnCount,
            'totalUsers' => $totalUsers,
            'pastDueCount' => $pastDueCount,
            'pastDueRevenue' => $pastDueRevenue,
            'planDistribution' => $planDistribution,
            'totalWorkOrders' => $totalWorkOrders,
            'totalAssets' => $totalAssets,
            'recentTenants' => $recentTenants,
            'adoptionAlerts' => $adoptionAlerts,
        ]);
    }
}
