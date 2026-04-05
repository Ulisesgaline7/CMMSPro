<?php

namespace Database\Factories;

use App\Enums\MaintenancePlanFrequency;
use App\Enums\WorkOrderPriority;
use App\Enums\WorkOrderType;
use App\Models\Asset;
use App\Models\MaintenancePlan;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenancePlan>
 */
class MaintenancePlanFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $frequency = fake()->randomElement(MaintenancePlanFrequency::cases());
        $startDate = fake()->dateTimeBetween('-6 months', 'now');

        return [
            'tenant_id' => Tenant::factory(),
            'asset_id' => Asset::factory(),
            'assigned_to' => null,
            'name' => 'Mantenimiento '.fake()->randomElement(['preventivo', 'predictivo']).' '.fake()->words(2, true),
            'description' => fake()->optional()->paragraph(),
            'type' => WorkOrderType::Preventive,
            'frequency' => $frequency,
            'frequency_value' => $frequency->isMetricBased() ? fake()->numberBetween(100, 500) : null,
            'priority' => fake()->randomElement(WorkOrderPriority::cases()),
            'estimated_duration' => fake()->randomElement([60, 120, 180, 240, 480]),
            'start_date' => $startDate,
            'end_date' => null,
            'next_execution_date' => fake()->dateTimeBetween('now', '+3 months'),
            'last_execution_date' => fake()->optional()->dateTimeBetween('-6 months', 'now'),
            'is_active' => true,
            'checklist_template' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function monthly(): static
    {
        return $this->state(fn (array $attributes) => [
            'frequency' => MaintenancePlanFrequency::Monthly,
            'frequency_value' => null,
        ]);
    }
}
