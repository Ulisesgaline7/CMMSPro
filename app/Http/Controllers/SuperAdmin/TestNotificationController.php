<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Notifications\NewTenantRegistered;
use App\Notifications\SubscriptionPastDue;
use App\Notifications\SystemAlert;
use App\Notifications\TenantSuspended;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TestNotificationController extends Controller
{
    public function send(Request $request): RedirectResponse
    {
        $type = $request->input('type', 'system_info');
        $user = $request->user();

        $tenant = Tenant::withoutGlobalScopes()->with('subscription')->inRandomOrder()->first();

        match ($type) {
            'new_tenant'  => $user->notify(new NewTenantRegistered($tenant)),
            'past_due'    => $user->notify(new SubscriptionPastDue($tenant)),
            'suspended'   => $user->notify(new TenantSuspended($tenant, 'Prueba de notificación push')),
            'system_error' => $user->notify(new SystemAlert(
                '⚠️ Error crítico detectado',
                'El servicio de procesamiento de pagos no responde. Revisando conectividad con Stripe.',
                'error',
                '/super-admin/subscriptions',
            )),
            'system_warning' => $user->notify(new SystemAlert(
                'Uso de disco al 85%',
                'El servidor principal está alcanzando el límite de almacenamiento. Considera limpiar logs.',
                'warning',
            )),
            'system_success' => $user->notify(new SystemAlert(
                'Despliegue completado exitosamente',
                'La versión v1.1.0 fue desplegada correctamente en producción sin interrupciones.',
                'success',
            )),
            default => $user->notify(new SystemAlert(
                'Notificación de prueba',
                'Esta es una notificación de prueba para verificar el sistema push en tiempo real.',
                'info',
            )),
        };

        return back()->with('success', 'Notificación push enviada.');
    }
}
