<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\ModuleKey;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantModule;
use Illuminate\View\View;

class ModuleCatalogController extends Controller
{
    public function index(): View
    {
        $modules = ModuleKey::cases();

        $activationCounts = TenantModule::withoutGlobalScopes()
            ->where('is_active', true)
            ->selectRaw('module_key, COUNT(*) as count')
            ->groupBy('module_key')
            ->pluck('count', 'module_key');

        $totalTenants = Tenant::withoutGlobalScopes()->count();

        $totalModuleRevenue = collect($modules)
            ->filter(fn ($m) => $m->price() > 0)
            ->sum(fn ($m) => $m->price() * ($activationCounts[$m->value] ?? 0));

        return view('super-admin.modules.catalog', compact(
            'modules',
            'activationCounts',
            'totalTenants',
            'totalModuleRevenue',
        ));
    }

    public function assignment(): View
    {
        $tenants = Tenant::withoutGlobalScopes()
            ->with(['modules' => fn ($q) => $q->where('is_active', true)])
            ->withCount(['modules as active_modules_count' => fn ($q) => $q->where('is_active', true)])
            ->orderByDesc('active_modules_count')
            ->paginate(20);

        $modules = ModuleKey::cases();

        return view('super-admin.modules.assignment', compact('tenants', 'modules'));
    }
}
