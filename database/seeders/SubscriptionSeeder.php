<?php

namespace Database\Seeders;

use App\Enums\DeploymentType;
use App\Enums\ModuleKey;
use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\TenantModule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::withoutGlobalScopes()->whereIn('slug', [
            'metalurgica-norte',
            'farmaceutica-central',
            'energia-renovable-mx',
        ])->get()->keyBy('slug');

        // Tenant 1: Metalúrgica Norte — IoT + AI
        if ($tenant1 = $tenants->get('metalurgica-norte')) {
            $modules = [ModuleKey::Iot->value, ModuleKey::AiPredictive->value, ModuleKey::Audits->value];
            $this->createSubscription($tenant1, DeploymentType::CloudSaas, $modules);
        }

        // Tenant 2: Farmacéutica Central — Pharma + LMS
        if ($tenant2 = $tenants->get('farmaceutica-central')) {
            $modules = [ModuleKey::PharmaModule->value, ModuleKey::Lms->value, ModuleKey::LotoSecurity->value];
            $this->createSubscription($tenant2, DeploymentType::Subdomain, $modules);
        }

        // Tenant 3: Energía Renovable MX — Energy + MultiSite
        if ($tenant3 = $tenants->get('energia-renovable-mx')) {
            $modules = [ModuleKey::EnergyEsg->value, ModuleKey::MultiSite->value, ModuleKey::Iot->value];
            $this->createSubscription($tenant3, DeploymentType::CloudSaas, $modules);
        }

        // Default: activate IoT and AI for all tenants that don't have it
        $allTenants = Tenant::withoutGlobalScopes()->get();
        foreach ($allTenants as $tenant) {
            TenantModule::updateOrCreate(
                ['tenant_id' => $tenant->id, 'module_key' => ModuleKey::Core->value],
                ['is_active' => true, 'activated_at' => now()]
            );
        }
    }

    /** @param array<int, string> $modules */
    private function createSubscription(Tenant $tenant, DeploymentType $deploymentType, array $modules): void
    {
        $modulesTotal = collect($modules)
            ->map(fn ($key) => ModuleKey::tryFrom($key)?->price() ?? 0)
            ->sum();

        Subscription::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'status' => SubscriptionStatus::Active,
                'deployment_type' => $deploymentType->value,
                'base_price_monthly' => $deploymentType->basePrice(),
                'modules_cost' => $modulesTotal,
                'users_cost' => 0,
                'total_monthly' => $deploymentType->basePrice() + $modulesTotal,
                'admin_count' => 2,
                'supervisor_count' => 3,
                'technician_count' => 10,
                'reader_count' => 2,
                'asset_count' => 500,
                'current_period_start' => now()->startOfMonth(),
                'current_period_end' => now()->endOfMonth(),
                'modules_json' => $modules,
            ]
        );

        foreach ($modules as $moduleKey) {
            TenantModule::updateOrCreate(
                ['tenant_id' => $tenant->id, 'module_key' => $moduleKey],
                ['is_active' => true, 'activated_at' => now()]
            );
        }
    }
}
