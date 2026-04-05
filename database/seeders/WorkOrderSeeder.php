<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Tenant;
use App\Models\WorkOrder;
use Illuminate\Database\Seeder;

class WorkOrderSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $assets = Asset::where('tenant_id', $tenant->id)->get();

            if ($assets->isEmpty()) {
                continue;
            }

            // Órdenes en diferentes estados
            WorkOrder::factory()->pending()->count(5)->create([
                'tenant_id' => $tenant->id,
                'asset_id' => fn () => $assets->random()->id,
            ]);

            WorkOrder::factory()->inProgress()->count(3)->create([
                'tenant_id' => $tenant->id,
                'asset_id' => fn () => $assets->random()->id,
            ]);

            WorkOrder::factory()->completed()->count(10)->create([
                'tenant_id' => $tenant->id,
                'asset_id' => fn () => $assets->random()->id,
            ]);

            // Algunas correctivas urgentes
            WorkOrder::factory()->corrective()->count(2)->create([
                'tenant_id' => $tenant->id,
                'asset_id' => fn () => $assets->random()->id,
            ]);
        }
    }
}
