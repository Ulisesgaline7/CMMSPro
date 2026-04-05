<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\TenantPlan;
use App\Enums\TenantStatus;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(Request $request): View
    {
        $tenants = Tenant::withoutGlobalScopes()
            ->with('subscription')
            ->withCount('users')
            ->when($request->search, function ($q, $s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('slug', 'like', "%{$s}%");
            })
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('super-admin.tenants.index', [
            'tenants' => $tenants,
            'filters' => $request->only(['search', 'status']),
            'statuses' => TenantStatus::cases(),
        ]);
    }

    public function show(int $id): View
    {
        $tenant = Tenant::withoutGlobalScopes()
            ->with(['subscription', 'modules'])
            ->withCount(['users', 'assets'])
            ->findOrFail($id);

        return view('super-admin.tenants.show', [
            'tenant' => $tenant,
        ]);
    }

    public function edit(int $id): View
    {
        $tenant = Tenant::withoutGlobalScopes()->findOrFail($id);

        return view('super-admin.tenants.edit', [
            'tenant' => $tenant,
            'plans' => TenantPlan::cases(),
            'statuses' => TenantStatus::cases(),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $tenant = Tenant::withoutGlobalScopes()->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'plan' => ['required', 'string'],
            'status' => ['required', 'string'],
            'max_users' => ['required', 'integer', 'min:1'],
            'max_assets' => ['required', 'integer', 'min:1'],
            'billing_email' => ['nullable', 'email'],
        ]);

        $tenant->update($request->only(['name', 'plan', 'status', 'max_users', 'max_assets', 'billing_email']));

        return redirect()->route('super-admin.tenants.show', $tenant->id)
            ->with('success', 'Tenant actualizado correctamente.');
    }

    public function suspend(int $id): RedirectResponse
    {
        $tenant = Tenant::withoutGlobalScopes()->findOrFail($id);
        $tenant->update(['status' => TenantStatus::Suspended]);

        return back()->with('success', 'Tenant suspendido.');
    }

    public function activate(int $id): RedirectResponse
    {
        $tenant = Tenant::withoutGlobalScopes()->findOrFail($id);
        $tenant->update(['status' => TenantStatus::Active]);

        return back()->with('success', 'Tenant activado.');
    }
}
