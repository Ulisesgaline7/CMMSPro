<?php

namespace Database\Factories;

use App\Enums\CorrectiveActionStatus;
use App\Models\CorrectiveAction;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CorrectiveAction>
 */
class CorrectiveActionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(CorrectiveActionStatus::cases());
        $completedAt = in_array($status, [CorrectiveActionStatus::Completed, CorrectiveActionStatus::Verified])
            ? fake()->dateTimeBetween('-30 days', 'now')
            : null;
        $verifiedAt = $status === CorrectiveActionStatus::Verified
            ? fake()->dateTimeBetween($completedAt ?? '-30 days', 'now')
            : null;

        return [
            'tenant_id' => Tenant::factory(),
            'finding_id' => null,
            'work_order_id' => null,
            'assigned_to' => null,
            'created_by' => null,
            'code' => 'CAP-'.fake()->unique()->numerify('######'),
            'title' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'type' => fake()->randomElement(['corrective', 'preventive']),
            'status' => $status,
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'critical']),
            'root_cause' => fake()->optional()->sentence(),
            'action_taken' => fake()->optional()->sentence(),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'completed_at' => $completedAt,
            'verified_at' => $verifiedAt,
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CorrectiveActionStatus::Open,
            'completed_at' => null,
            'verified_at' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CorrectiveActionStatus::Completed,
            'completed_at' => now(),
            'verified_at' => null,
        ]);
    }

    /**
     * @param  \App\Models\Tenant  $tenant
     */
    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenant->id,
        ]);
    }
}
