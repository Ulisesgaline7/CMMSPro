<?php

namespace Database\Factories;

use App\Enums\SensorStatus;
use App\Enums\SensorType;
use App\Models\Asset;
use App\Models\Sensor;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sensor>
 */
class SensorFactory extends Factory
{
    public function definition(): array
    {
        $type = $this->faker->randomElement(SensorType::cases());

        return [
            'tenant_id' => Tenant::factory(),
            'asset_id' => Asset::factory(),
            'code' => 'SEN-'.$this->faker->unique()->numerify('####'),
            'name' => $type->label().' '.$this->faker->word(),
            'type' => $type,
            'unit' => $type->unit(),
            'status' => SensorStatus::Active,
            'min_threshold' => $this->faker->randomFloat(2, -10, 10),
            'max_threshold' => $this->faker->randomFloat(2, 80, 120),
            'warning_threshold_low' => $this->faker->randomFloat(2, 0, 10),
            'warning_threshold_high' => $this->faker->randomFloat(2, 60, 80),
            'sampling_interval_seconds' => $this->faker->randomElement([30, 60, 120, 300]),
        ];
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn () => ['tenant_id' => $tenant->id]);
    }

    public function forAsset(Asset $asset): static
    {
        return $this->state(fn () => [
            'asset_id' => $asset->id,
            'tenant_id' => $asset->tenant_id,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => SensorStatus::Active]);
    }

    public function fault(): static
    {
        return $this->state(fn () => ['status' => SensorStatus::Fault]);
    }

    public function withThresholds(float $min, float $max): static
    {
        return $this->state(fn () => [
            'min_threshold' => $min,
            'max_threshold' => $max,
            'warning_threshold_low' => $min * 1.1,
            'warning_threshold_high' => $max * 0.9,
        ]);
    }
}
