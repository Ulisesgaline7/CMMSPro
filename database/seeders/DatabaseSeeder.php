<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            TenantSeeder::class,
            UserSeeder::class,
            ModuleSeeder::class,
            AssetCategorySeeder::class,
            LocationSeeder::class,
            AssetSeeder::class,
            PartSeeder::class,
            MaintenancePlanSeeder::class,
            WorkOrderSeeder::class,
            SubscriptionSeeder::class,
            SensorSeeder::class,
        ]);
    }
}
