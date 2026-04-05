<?php

namespace Database\Seeders;

use App\Models\Part;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class PartSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            Part::factory()->count(15)->create(['tenant_id' => $tenant->id]);
            Part::factory()->lowStock()->count(3)->create(['tenant_id' => $tenant->id]);
        }
    }
}
