<?php

namespace Database\Seeders;

use App\Enums\ModuleKey;
use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (ModuleKey::cases() as $index => $moduleKey) {
            Module::updateOrCreate(
                ['key' => $moduleKey->value],
                [
                    'name' => $moduleKey->label(),
                    'description' => $moduleKey->description(),
                    'base_price_monthly' => $moduleKey->price(),
                    'is_core' => $moduleKey === ModuleKey::Core,
                    'sort_order' => $index,
                ]
            );
        }
    }
}
