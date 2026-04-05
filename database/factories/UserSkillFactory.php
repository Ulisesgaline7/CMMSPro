<?php

namespace Database\Factories;

use App\Enums\SkillLevel;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserSkill>
 */
class UserSkillFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = fake()->randomElement(['mechanical', 'electrical', 'instrumentation', 'safety', 'other']);

        $skillsByCategory = [
            'mechanical' => ['Hidráulica industrial', 'Neumática', 'Soldadura MIG/TIG', 'Mantenimiento de compresores', 'Alineación de ejes', 'Balanceo de rodetes'],
            'electrical' => ['PLCs Siemens', 'Variadores de frecuencia', 'Paneles de control', 'Instalaciones eléctricas MT', 'Termografía eléctrica'],
            'instrumentation' => ['Calibración de instrumentos', 'SCADA', 'Sensores y transmisores', 'Loops de control', 'Válvulas de control'],
            'safety' => ['Análisis de riesgos (HAZOP)', 'Planes de emergencia', 'Investigación de accidentes', 'Señalización industrial'],
            'other' => ['Gestión de mantenimiento', 'Reportes técnicos', 'Lectura de planos', 'Inglés técnico'],
        ];

        return [
            'tenant_id' => Tenant::factory(),
            'user_id' => User::factory(),
            'name' => fake()->randomElement($skillsByCategory[$category]),
            'category' => $category,
            'level' => fake()->randomElement(SkillLevel::cases()),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }
}
