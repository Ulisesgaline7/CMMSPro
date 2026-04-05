<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\MaintenancePlan;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class MaintenancePlanSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $assets = Asset::where('tenant_id', $tenant->id)->get();

            if ($assets->isEmpty()) {
                continue;
            }

            // 2 planes activos por activo (máximo 5 activos para no saturar)
            foreach ($assets->take(5) as $asset) {
                MaintenancePlan::factory()->monthly()->count(2)->create([
                    'tenant_id' => $tenant->id,
                    'asset_id' => $asset->id,
                ]);
            }
        }
    }
}
