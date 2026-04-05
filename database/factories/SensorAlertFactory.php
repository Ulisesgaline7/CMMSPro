<?php

namespace Database\Factories;

use App\Models\SensorAlert;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SensorAlert>
 */
class SensorAlertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sensor_id' => \App\Models\Sensor::factory(),
            'tenant_id' => \App\Models\Tenant::factory(),
            'type' => $this->faker->randomElement(['max_exceeded', 'min_below', 'warning_high', 'warning_low']),
            'severity' => \App\Enums\AlertSeverity::Warning,
            'message' => $this->faker->sentence(),
            'value' => $this->faker->randomFloat(2, 0, 100),
            'threshold' => $this->faker->randomFloat(2, 0, 100),
            'triggered_at' => now(),
            'is_active' => true,
        ];
    }
}
