<?php

namespace Database\Factories;

use App\Enums\PurchaseOrderPriority;
use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(PurchaseOrderStatus::cases());

        return [
            'tenant_id' => Tenant::factory(),
            'work_order_id' => null,
            'requested_by' => null,
            'approved_by' => null,
            'code' => 'PO-'.fake()->unique()->numerify('######'),
            'supplier_name' => fake()->company(),
            'supplier_contact' => fake()->optional()->email(),
            'status' => $status,
            'priority' => fake()->randomElement(PurchaseOrderPriority::cases()),
            'expected_delivery' => fake()->optional()->dateTimeBetween('now', '+60 days'),
            'received_at' => $status === PurchaseOrderStatus::Received ? fake()->dateTimeBetween('-30 days', 'now') : null,
            'total_amount' => fake()->randomFloat(2, 100, 50000),
            'currency' => 'MXN',
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PurchaseOrderStatus::Draft,
        ]);
    }

    public function pendingApproval(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PurchaseOrderStatus::PendingApproval,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PurchaseOrderStatus::Approved,
        ]);
    }

    public function ordered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PurchaseOrderStatus::Ordered,
        ]);
    }

    public function received(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PurchaseOrderStatus::Received,
            'received_at' => now(),
        ]);
    }
}
