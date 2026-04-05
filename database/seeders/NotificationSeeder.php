<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Notifications\NewTenantRegistered;
use App\Notifications\SubscriptionPastDue;
use App\Notifications\SystemAlert;
use App\Notifications\TenantSuspended;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::where('is_super_admin', true)->firstOrFail();
        $tenants    = Tenant::withoutGlobalScopes()->with('subscription')->get();

        // Clear existing notifications for a clean test
        $superAdmin->notifications()->delete();

        // 1. New tenant registered (for each tenant)
        foreach ($tenants->take(3) as $tenant) {
            $superAdmin->notify(new NewTenantRegistered($tenant));
        }

        // 2. Subscription past due
        foreach ($tenants->take(2) as $tenant) {
            $superAdmin->notify(new SubscriptionPastDue($tenant));
        }

        // 3. Tenant suspended
        $superAdmin->notify(new TenantSuspended(
            $tenants->first(),
            'Falta de pago por más de 30 días'
        ));

        // 4. System alerts — varios tipos
        $superAdmin->notify(new SystemAlert(
            'CPU al 92% en el servidor principal',
            'El servidor prod-01 lleva 15 minutos con uso de CPU por encima del 90%. Revisa los procesos activos.',
            'error',
            '/super-admin'
        ));

        $superAdmin->notify(new SystemAlert(
            'Backup nocturno completado',
            'El respaldo automático de la base de datos se completó correctamente. 4.2 GB almacenados.',
            'success',
            '/super-admin'
        ));

        $superAdmin->notify(new SystemAlert(
            'Nuevo pago recibido — $249 USD',
            'Farmacéutica Central realizó el pago de su suscripción Professional correspondiente a este mes.',
            'info',
            '/super-admin/invoices'
        ));

        $superAdmin->notify(new SystemAlert(
            'Certificado SSL próximo a vencer',
            'El certificado SSL de empresa-demo.tuplataforma.com vence en 7 días. Renueva a la brevedad.',
            'warning',
            '/super-admin/access'
        ));

        $superAdmin->notify(new SystemAlert(
            'Tasa de conversión trial subió 12%',
            '8 cuentas trial se convirtieron a planes pagados esta semana. MRR incrementó +$1,992.',
            'success',
            '/super-admin/revenue'
        ));

        // Marcar algunas como leídas para simular uso real
        $superAdmin->notifications()
            ->latest()
            ->skip(4)
            ->take(4)
            ->get()
            ->each(fn ($n) => $n->markAsRead());

        $this->command->info("✓ {$superAdmin->notifications()->count()} notificaciones creadas para {$superAdmin->name}");
    }
}
