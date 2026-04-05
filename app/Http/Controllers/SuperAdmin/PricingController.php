<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\ModuleKey;
use App\Enums\TenantPlan;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function index(): View
    {
        $plans = TenantPlan::cases();

        $tenantsByPlan = Tenant::withoutGlobalScopes()
            ->selectRaw('plan, COUNT(*) as count')
            ->groupBy('plan')
            ->pluck('count', 'plan');

        $modules = ModuleKey::cases();

        return view('super-admin.pricing', compact('plans', 'tenantsByPlan', 'modules'));
    }
}
