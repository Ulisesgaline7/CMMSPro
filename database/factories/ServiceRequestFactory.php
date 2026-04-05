<?php

namespace Database\Factories;

use App\Enums\ServiceRequestCategory;
use App\Enums\ServiceRequestPriority;
use App\Enums\ServiceRequestStatus;
use App\Models\ServiceRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRequest>
 */
class ServiceRequestFactory extends Factory
{
    private static int $counter = 0;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $priority = fake()->randomElement(ServiceRequestPriority::cases());
        $status = fake()->randomElement(ServiceRequestStatus::cases());
        $createdAt = fake()->dateTimeBetween('-60 days', 'now');
        $slaDeadline = (new \DateTime($createdAt->format('Y-m-d H:i:s')))->modify("+{$priority->slaHours()} hours");
        $resolvedAt = in_array($status, [ServiceRequestStatus::Resolved, ServiceRequestStatus::Closed])
            ? fake()->dateTimeBetween($createdAt, 'now')
            : null;

        self::$counter++;
        $code = 'SR-'.str_pad(self::$counter, 6, '0', STR_PAD_LEFT);

        return [
            'tenant_id'           => Tenant::factory(),
            'requested_by'        => User::factory(),
            'assigned_to'         => null,
            'asset_id'            => null,
            'code'                => $code,
            'title'               => fake()->sentence(4),
            'description'         => fake()->optional()->paragraph(),
            'category'            => fake()->randomElement(ServiceRequestCategory::cases()),
            'priority'            => $priority,
            'status'              => $status,
            'location_description' => fake()->optional()->sentence(3),
            'sla_deadline'        => $slaDeadline,
            'resolved_at'         => $resolvedAt,
            'closed_at'           => null,
            'resolution_time'     => $resolvedAt ? fake()->numberBetween(30, 480) : null,
            'sla_met'             => $resolvedAt ? ($resolvedAt <= $slaDeadline) : null,
            'resolution_notes'    => $resolvedAt ? fake()->optional()->sentence() : null,
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'      => ServiceRequestStatus::Open,
            'resolved_at' => null,
            'sla_met'     => null,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'      => ServiceRequestStatus::InProgress,
            'resolved_at' => null,
            'sla_met'     => null,
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'      => ServiceRequestStatus::Resolved,
            'resolved_at' => now(),
            'sla_met'     => true,
        ]);
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenant->id,
        ]);
    }
}
