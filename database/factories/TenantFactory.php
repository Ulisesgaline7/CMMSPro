<?php

namespace Database\Factories;

use App\Enums\TenantPlan;
use App\Enums\TenantStatus;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('###'),
            'plan' => fake()->randomElement(TenantPlan::cases()),
            'status' => TenantStatus::Active,
            'settings' => null,
            'max_users' => 10,
            'max_assets' => 100,
            'trial_ends_at' => null,
        ];
    }

    public function trial(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TenantStatus::Trial,
            'plan' => TenantPlan::Starter,
            'trial_ends_at' => now()->addDays(14),
        ]);
    }

    public function professional(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan' => TenantPlan::Professional,
            'status' => TenantStatus::Active,
            'max_users' => 50,
            'max_assets' => 500,
        ]);
    }

    public function enterprise(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan' => TenantPlan::Enterprise,
            'status' => TenantStatus::Active,
            'max_users' => 9999,
            'max_assets' => 9999,
        ]);
    }
}
