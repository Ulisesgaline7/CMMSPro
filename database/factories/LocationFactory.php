<?php

namespace Database\Factories;

use App\Enums\LocationType;
use App\Models\Location;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'parent_id' => null,
            'name' => fake()->words(2, true),
            'code' => strtoupper(fake()->bothify('LOC-###')),
            'type' => fake()->randomElement(LocationType::cases()),
            'description' => fake()->optional()->sentence(),
            'address' => fake()->optional()->address(),
        ];
    }

    public function plant(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => LocationType::Plant,
            'parent_id' => null,
        ]);
    }

    public function childOf(Location $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $parent->tenant_id,
            'parent_id' => $parent->id,
        ]);
    }
}
