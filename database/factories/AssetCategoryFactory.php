<?php

namespace Database\Factories;

use App\Models\AssetCategory;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssetCategory>
 */
class AssetCategoryFactory extends Factory
{
    private static array $categories = [
        'Bombas y Compresores',
        'Motores Eléctricos',
        'Equipos de Transporte',
        'Instrumentación y Control',
        'Sistemas HVAC',
        'Generadores',
        'Calderas y Hornos',
        'Vehículos',
        'Herramientas y Equipos',
        'Infraestructura',
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement(self::$categories);

        return [
            'tenant_id' => Tenant::factory(),
            'name' => $name,
            'code' => strtoupper(fake()->bothify('CAT-###')),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
