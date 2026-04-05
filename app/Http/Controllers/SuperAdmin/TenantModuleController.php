<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\ModuleKey;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TenantModuleController extends Controller
{
    public function index(int $tenantId): View
    {
        $tenant = Tenant::withoutGlobalScopes()->findOrFail($tenantId);
        $activeModuleKeys = TenantModule::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->pluck('module_key')
            ->toArray();

        return view('super-admin.modules.index', [
            'tenant' => $tenant,
            'modules' => ModuleKey::cases(),
            'activeModuleKeys' => $activeModuleKeys,
        ]);
    }

    public function toggle(Request $request, int $tenantId): RedirectResponse
    {
        $request->validate([
            'module_key' => ['required', 'string'],
        ]);

        $tenant = Tenant::withoutGlobalScopes()->findOrFail($tenantId);
        $moduleKey = $request->module_key;

        $existing = TenantModule::where('tenant_id', $tenantId)
            ->where('module_key', $moduleKey)
            ->first();

        if ($existing) {
            $existing->update([
                'is_active' => ! $existing->is_active,
                'deactivated_at' => $existing->is_active ? null : now(),
                'activated_at' => $existing->is_active ? now() : $existing->activated_at,
                'activated_by' => Auth::id(),
            ]);
        } else {
            TenantModule::create([
                'tenant_id' => $tenantId,
                'module_key' => $moduleKey,
                'is_active' => true,
                'activated_at' => now(),
                'activated_by' => Auth::id(),
            ]);
        }

        return back()->with('success', "Módulo '{$moduleKey}' actualizado para {$tenant->name}.");
    }
}
