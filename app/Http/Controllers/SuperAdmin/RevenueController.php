<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RevenueController extends Controller
{
    public function index(): View
    {
        $mrr = (float) Subscription::withoutGlobalScopes()
            ->where('status', SubscriptionStatus::Active)
            ->sum('total_monthly');

        $arr = $mrr * 12;

        $activeSubscriptions = Subscription::withoutGlobalScopes()
            ->where('status', SubscriptionStatus::Active)
            ->count();

        $avgRevenue = $activeSubscriptions > 0 ? $mrr / $activeSubscriptions : 0;

        $mrrByMonth = Invoice::withoutGlobalScopes()
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', now()->subMonths(12))
            ->select(
                DB::raw("DATE_FORMAT(paid_at, '%Y-%m') as month"),
                DB::raw('SUM(amount_paid) as total'),
                DB::raw('COUNT(*) as count'),
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $topTenants = Subscription::withoutGlobalScopes()
            ->with('tenant')
            ->where('status', SubscriptionStatus::Active)
            ->orderByDesc('total_monthly')
            ->limit(10)
            ->get();

        $trialConversions = Tenant::withoutGlobalScopes()
            ->where('status', \App\Enums\TenantStatus::Active)
            ->count();

        $trialActive = Tenant::withoutGlobalScopes()
            ->where('status', \App\Enums\TenantStatus::Trial)
            ->count();

        return view('super-admin.revenue', compact(
            'mrr',
            'arr',
            'activeSubscriptions',
            'avgRevenue',
            'mrrByMonth',
            'topTenants',
            'trialConversions',
            'trialActive',
        ));
    }

    public function report(): View
    {
        $revenueByPlan = Subscription::withoutGlobalScopes()
            ->where('status', SubscriptionStatus::Active)
            ->join('tenants', 'tenants.id', '=', 'subscriptions.tenant_id')
            ->select('tenants.plan', DB::raw('SUM(subscriptions.total_monthly) as mrr'), DB::raw('COUNT(*) as count'))
            ->groupBy('tenants.plan')
            ->get();

        $monthlyData = Invoice::withoutGlobalScopes()
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', now()->subMonths(12))
            ->select(
                DB::raw("DATE_FORMAT(paid_at, '%Y-%m') as month"),
                DB::raw('SUM(amount_paid) as revenue'),
                DB::raw('COUNT(*) as invoices'),
                DB::raw('COUNT(DISTINCT tenant_id) as tenants'),
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $churnRisk = Subscription::withoutGlobalScopes()
            ->with('tenant')
            ->where('status', SubscriptionStatus::PastDue)
            ->orWhere(function ($q): void {
                $q->where('status', SubscriptionStatus::Active)
                    ->where('cancel_at_period_end', true);
            })
            ->get();

        $totalInvoiced = (float) Invoice::withoutGlobalScopes()->sum('amount_due') / 100;
        $totalCollected = (float) Invoice::withoutGlobalScopes()->where('status', 'paid')->sum('amount_paid') / 100;
        $collectionRate = $totalInvoiced > 0 ? round($totalCollected / $totalInvoiced * 100, 1) : 0;

        return view('super-admin.revenue-report', compact(
            'revenueByPlan',
            'monthlyData',
            'churnRisk',
            'totalInvoiced',
            'totalCollected',
            'collectionRate',
        ));
    }
}
