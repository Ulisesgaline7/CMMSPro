<?php

namespace Database\Factories;

use App\Enums\AssetCriticality;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Location;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    private static int $sequence = 1;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brands = ['Siemens', 'ABB', 'Grundfos', 'WEG', 'Schneider Electric', 'Caterpillar', 'Bosch', 'Atlas Copco'];

        return [
            'tenant_id' => Tenant::factory(),
            'location_id' => null,
            'asset_category_id' => null,
            'parent_id' => null,
            'name' => fake()->words(3, true),
            'code' => 'ASS-'.str_pad(self::$sequence++, 5, '0', STR_PAD_LEFT),
            'serial_number' => strtoupper(fake()->bothify('SN-#####??')),
            'brand' => fake()->randomElement($brands),
            'model' => strtoupper(fake()->bothify('MOD-###?')),
            'manufacture_year' => fake()->year(),
            'purchase_date' => fake()->optional()->date(),
            'installation_date' => fake()->optional()->date(),
            'warranty_expires_at' => fake()->optional()->dateTimeBetween('now', '+3 years'),
            'purchase_cost' => fake()->optional()->randomFloat(2, 1000, 500000),
            'status' => fake()->randomElement(AssetStatus::cases()),
            'criticality' => fake()->randomElement(AssetCriticality::cases()),
            'specs' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssetStatus::Active,
        ]);
    }

    public function critical(): static
    {
        return $this->state(fn (array $attributes) => [
            'criticality' => AssetCriticality::Critical,
            'status' => AssetStatus::Active,
        ]);
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenant->id,
        ]);
    }
}
