<?php

namespace Database\Factories;

use App\Enums\AuditStatus;
use App\Enums\AuditType;
use App\Models\Audit;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Audit>
 */
class AuditFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(AuditStatus::cases());
        $scheduledDate = fake()->dateTimeBetween('-60 days', '-1 day');
        $completedDate = $status === AuditStatus::Completed
            ? fake()->dateTimeBetween($scheduledDate, 'now')
            : null;

        return [
            'tenant_id' => Tenant::factory(),
            'created_by' => null,
            'auditor_id' => null,
            'code' => 'AUD-'.fake()->unique()->numerify('######'),
            'title' => fake()->sentence(5),
            'description' => fake()->optional()->paragraph(),
            'type' => fake()->randomElement(AuditType::cases()),
            'status' => $status,
            'scope' => fake()->optional()->sentence(),
            'scheduled_date' => $scheduledDate,
            'completed_date' => $completedDate,
            'location' => fake()->optional()->city(),
            'findings_count' => 0,
        ];
    }

    public function planned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AuditStatus::Planned,
            'completed_date' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AuditStatus::Completed,
            'completed_date' => now(),
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
