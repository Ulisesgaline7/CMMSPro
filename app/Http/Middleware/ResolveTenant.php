<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        // Resolve tenant from subdomain route parameter (production subdomain routing)
        $subdomain = $request->route('subdomain');

        if ($subdomain) {
            $tenant = Tenant::where('subdomain', $subdomain)->first();

            if (! $tenant || ! $tenant->isOperational()) {
                abort(403, 'Tu cuenta no está activa. Contacta con soporte.');
            }

            // Verify the logged-in user belongs to this tenant
            if ($user && $user->tenant_id !== $tenant->id) {
                abort(403, 'No tienes acceso a esta cuenta.');
            }

            app()->instance('tenant', $tenant);

            return $next($request);
        }

        // Fallback: resolve tenant from the authenticated user's tenant_id
        if ($user && $user->tenant_id) {
            $tenant = Tenant::find($user->tenant_id);

            if (! $tenant || ! $tenant->isOperational()) {
                abort(403, 'Tu cuenta no está activa. Contacta con soporte.');
            }

            app()->instance('tenant', $tenant);
        }

        return $next($request);
    }
}
