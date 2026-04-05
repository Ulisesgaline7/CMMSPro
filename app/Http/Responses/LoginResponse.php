<?php

namespace App\Http\Responses;

use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|HttpResponse
    {
        $user = Auth::user();

        // Super Admin goes to the SA panel — force full-page navigation out of Inertia SPA context
        if ($user->isSuperAdmin()) {
            return Inertia::location(route('super-admin.dashboard'));
        }

        // Tenant user: redirect to their subdomain in production
        if ($user->tenant_id) {
            $subdomainUrl = $this->tenantUrl($user->tenant_id);

            if ($subdomainUrl) {
                return redirect()->away($subdomainUrl);
            }
        }

        // Fallback (local dev or tenant without subdomain)
        return redirect()->intended(config('fortify.home', '/dashboard'));
    }

    /**
     * Build the absolute URL for the tenant's subdomain dashboard.
     * Returns null when running locally (no APP_DOMAIN configured).
     */
    private function tenantUrl(int $tenantId): ?string
    {
        $appDomain = config('app.domain');

        if (! $appDomain) {
            return null;
        }

        $tenant = Tenant::withoutGlobalScopes()->find($tenantId);

        if (! $tenant || ! $tenant->subdomain) {
            return null;
        }

        $scheme = app()->isProduction() ? 'https' : 'http';

        return "{$scheme}://{$tenant->subdomain}.{$appDomain}/dashboard";
    }
}
