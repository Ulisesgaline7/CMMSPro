<?php

namespace Database\Factories;

use App\Models\Part;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Part>
 */
class PartFactory extends Factory
{
    private static array $parts = [
        'Rodamiento SKF 6205',
        'Sello mecánico 25mm',
        'Correa en V tipo B',
        'Filtro de aceite hidráulico',
        'Fusible 16A 250V',
        'Válvula solenoide 24VDC',
        'Contactor 3P 25A',
        'Aceite mineral SAE 40',
        'Grasa NLGI 2',
        'Empaque de hule neopreno',
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $units = ['pieza', 'litro', 'kg', 'metro', 'juego', 'caja'];

        return [
            'tenant_id' => Tenant::factory(),
            'name' => fake()->randomElement(self::$parts).' '.fake()->bothify('??-##'),
            'part_number' => strtoupper(fake()->bothify('PN-#####')),
            'brand' => fake()->optional()->randomElement(['SKF', 'NSK', 'Parker', 'Bosch', 'Siemens', 'WEG']),
            'description' => fake()->optional()->sentence(),
            'unit' => fake()->randomElement($units),
            'stock_quantity' => fake()->numberBetween(0, 50),
            'min_stock' => fake()->numberBetween(1, 10),
            'unit_cost' => fake()->optional()->randomFloat(2, 10, 5000),
            'storage_location' => fake()->optional()->bothify('Estante ##-?'),
        ];
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
            'min_stock' => 5,
        ]);
    }
}
