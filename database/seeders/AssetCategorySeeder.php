<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class AssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Bombas y Compresores', 'code' => 'BOMB'],
            ['name' => 'Motores Eléctricos', 'code' => 'MOTR'],
            ['name' => 'Equipos de Transporte', 'code' => 'TRAN'],
            ['name' => 'Instrumentación y Control', 'code' => 'INST'],
            ['name' => 'Sistemas HVAC', 'code' => 'HVAC'],
            ['name' => 'Generadores', 'code' => 'GENR'],
            ['name' => 'Calderas y Hornos', 'code' => 'CALD'],
            ['name' => 'Vehículos', 'code' => 'VHCL'],
        ];

        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            foreach ($categories as $category) {
                AssetCategory::create([
                    'tenant_id' => $tenant->id,
                    'name' => $category['name'],
                    'code' => $category['code'],
                ]);
            }
        }
    }
}
