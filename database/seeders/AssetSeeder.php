<?php

namespace Database\Seeders;

use App\Enums\AssetCriticality;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Location;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::with(['locations', 'assetCategories'])->get();

        foreach ($tenants as $tenant) {
            $locations = $tenant->locations;
            $categories = $tenant->assetCategories;

            if ($locations->isEmpty() || $categories->isEmpty()) {
                continue;
            }

            // 10 activos por tenant con datos variados
            Asset::factory()->count(10)->create([
                'tenant_id' => $tenant->id,
                'location_id' => fn () => $locations->random()->id,
                'asset_category_id' => fn () => $categories->random()->id,
            ]);

            // 2 activos críticos garantizados por tenant
            Asset::factory()->critical()->count(2)->create([
                'tenant_id' => $tenant->id,
                'location_id' => $locations->first()->id,
                'asset_category_id' => $categories->first()->id,
            ]);
        }
    }
}
