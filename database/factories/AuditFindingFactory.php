<?php

namespace Database\Factories;

use App\Enums\FindingSeverity;
use App\Models\Audit;
use App\Models\AuditFinding;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditFinding>
 */
class AuditFindingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['open', 'in_progress', 'closed', 'accepted_risk']);
        $closedAt = $status === 'closed' ? now() : null;

        return [
            'audit_id' => Audit::factory(),
            'assigned_to' => null,
            'code' => 'F-'.fake()->numerify('###'),
            'description' => fake()->sentence(),
            'severity' => fake()->randomElement(FindingSeverity::cases()),
            'status' => $status,
            'due_date' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'closed_at' => $closedAt,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'open',
            'closed_at' => null,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
            'closed_at' => now(),
        ]);
    }
}
