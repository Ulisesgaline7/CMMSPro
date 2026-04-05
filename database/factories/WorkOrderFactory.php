<?php

namespace Database\Factories;

use App\Enums\WorkOrderPriority;
use App\Enums\WorkOrderStatus;
use App\Enums\WorkOrderType;
use App\Models\Asset;
use App\Models\Tenant;
use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<WorkOrder>
 */
class WorkOrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(WorkOrderStatus::cases());
        $type = fake()->randomElement(WorkOrderType::cases());
        $startedAt = in_array($status, [WorkOrderStatus::InProgress, WorkOrderStatus::Completed])
            ? fake()->dateTimeBetween('-30 days', '-1 day')
            : null;
        $completedAt = $status === WorkOrderStatus::Completed
            ? fake()->dateTimeBetween($startedAt ?? '-30 days', 'now')
            : null;

        return [
            'tenant_id' => Tenant::factory(),
            'asset_id' => Asset::factory(),
            'maintenance_plan_id' => null,
            'requested_by' => null,
            'assigned_to' => null,
            'approved_by' => null,
            'code' => $type->abbreviation().'-'.fake()->unique()->numerify('######'),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'type' => $type,
            'status' => $status,
            'priority' => fake()->randomElement(WorkOrderPriority::cases()),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
            'estimated_duration' => fake()->optional()->randomElement([60, 120, 180, 240, 480]),
            'actual_duration' => $completedAt ? fake()->numberBetween(30, 600) : null,
            'failure_cause' => $type === WorkOrderType::Corrective ? fake()->optional()->sentence() : null,
            'resolution_notes' => $completedAt ? fake()->optional()->sentence() : null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WorkOrderStatus::Pending,
            'started_at' => null,
            'completed_at' => null,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WorkOrderStatus::InProgress,
            'started_at' => now()->subHours(fake()->numberBetween(1, 24)),
            'completed_at' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WorkOrderStatus::Completed,
            'started_at' => fake()->dateTimeBetween('-30 days', '-2 hours'),
            'completed_at' => now(),
            'actual_duration' => fake()->numberBetween(30, 480),
        ]);
    }

    public function corrective(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => WorkOrderType::Corrective,
            'code' => 'CM-'.fake()->unique()->numerify('######'),
        ]);
    }
}
