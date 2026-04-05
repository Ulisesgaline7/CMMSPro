<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccessController extends Controller
{
    public function index(): View
    {
        $tenants = Tenant::withoutGlobalScopes()
            ->latest()
            ->paginate(25);

        $byLevel = Tenant::withoutGlobalScopes()
            ->selectRaw('white_label_level, COUNT(*) as count')
            ->groupBy('white_label_level')
            ->pluck('count', 'white_label_level');

        $withCustomDomain = Tenant::withoutGlobalScopes()->whereNotNull('custom_domain')->count();
        $withSubdomain = Tenant::withoutGlobalScopes()->whereNotNull('subdomain')->count();
        $verifiedDomains = Tenant::withoutGlobalScopes()->where('custom_domain_verified', true)->count();

        return view('super-admin.access', compact(
            'tenants',
            'byLevel',
            'withCustomDomain',
            'withSubdomain',
            'verifiedDomains',
        ));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $tenant = Tenant::withoutGlobalScopes()->findOrFail($id);

        $validated = $request->validate([
            'subdomain'            => ['nullable', 'string', 'max:100', 'unique:tenants,subdomain,' . $id],
            'custom_domain'        => ['nullable', 'string', 'max:255', 'unique:tenants,custom_domain,' . $id],
            'custom_domain_verified' => ['boolean'],
            'white_label_level'    => ['required', 'integer', 'min:0', 'max:4'],
            'brand_name'           => ['nullable', 'string', 'max:255'],
            'primary_color'        => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'reseller_id'          => ['nullable', 'string', 'max:255'],
        ]);

        $tenant->update($validated);

        return back()->with('success', "Configuración de acceso de {$tenant->name} actualizada.");
    }
}
