<?php

namespace Database\Seeders;

use App\Enums\TenantPlan;
use App\Enums\TenantStatus;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // Tenant 1: Metalúrgica Norte — Professional con IoT + AI Predictiva
        Tenant::factory()->professional()->create([
            'name' => 'Metalúrgica Norte',
            'slug' => 'metalurgica-norte',
            'primary_color' => '#002046',
            'secondary_color' => '#1b365d',
            'brand_name' => 'MetalCMMS',
        ]);

        // Tenant 2: Farmacéutica Central — Enterprise (pharma)
        Tenant::factory()->enterprise()->create([
            'name' => 'Farmacéutica Central',
            'slug' => 'farmaceutica-central',
            'primary_color' => '#059669',
            'secondary_color' => '#047857',
            'brand_name' => 'PharmaMaint',
        ]);

        // Tenant 3: Energía Renovable MX — Professional con Energy ESG
        Tenant::factory()->professional()->create([
            'name' => 'Energía Renovable MX',
            'slug' => 'energia-renovable-mx',
            'primary_color' => '#F59E0B',
            'secondary_color' => '#D97706',
            'brand_name' => 'EnerCMMS',
        ]);

        // Tenant demo adicional para pruebas
        Tenant::factory()->trial()->create([
            'name' => 'Empresa Demo',
            'slug' => 'empresa-demo',
        ]);
    }
}
