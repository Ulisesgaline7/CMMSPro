<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseOrderItem>
 */
class PurchaseOrderItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->randomFloat(2, 1, 50);
        $unitPrice = fake()->randomFloat(2, 10, 5000);

        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'part_id' => null,
            'description' => fake()->words(3, true),
            'part_number' => fake()->optional()->bothify('PN-####??'),
            'quantity' => $quantity,
            'unit' => fake()->randomElement(['pz', 'kg', 'lt', 'm', 'caja', 'par']),
            'unit_price' => $unitPrice,
            'total_price' => round($quantity * $unitPrice, 2),
        ];
    }
}
