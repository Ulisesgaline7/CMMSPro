<?php

namespace Database\Seeders;

use App\Enums\LocationType;
use App\Models\Location;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // Planta raíz
            $plant = Location::factory()->plant()->create([
                'tenant_id' => $tenant->id,
                'name' => 'Planta Principal',
                'code' => 'PLT-001',
            ]);

            // Edificios dentro de la planta
            $building = Location::factory()->create([
                'tenant_id' => $tenant->id,
                'parent_id' => $plant->id,
                'name' => 'Nave Industrial A',
                'code' => 'NAV-001',
                'type' => LocationType::Building,
            ]);

            // Áreas dentro del edificio
            foreach (['Producción', 'Mantenimiento', 'Almacén'] as $i => $area) {
                Location::factory()->create([
                    'tenant_id' => $tenant->id,
                    'parent_id' => $building->id,
                    'name' => $area,
                    'code' => 'AREA-'.str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                    'type' => LocationType::Area,
                ]);
            }
        }
    }
}
