<?php

namespace Database\Factories;

use App\Enums\ShiftStatus;
use App\Enums\ShiftType;
use App\Models\Shift;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shift>
 */
class ShiftFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(ShiftType::cases());

        $times = match ($type) {
            ShiftType::Morning   => ['06:00', '14:00'],
            ShiftType::Afternoon => ['14:00', '22:00'],
            ShiftType::Night     => ['22:00', '06:00'],
            ShiftType::Custom    => ['08:00', '16:00'],
        };

        return [
            'tenant_id'  => Tenant::factory(),
            'user_id'    => User::factory(),
            'name'       => $type->label(),
            'type'       => $type,
            'date'       => fake()->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d'),
            'start_time' => $times[0],
            'end_time'   => $times[1],
            'status'     => fake()->randomElement(ShiftStatus::cases()),
            'notes'      => fake()->optional()->sentence(),
        ];
    }

    public function morning(): static
    {
        return $this->state(fn (array $attributes) => [
            'type'       => ShiftType::Morning,
            'name'       => 'Turno Mañana',
            'start_time' => '06:00',
            'end_time'   => '14:00',
        ]);
    }

    public function afternoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'type'       => ShiftType::Afternoon,
            'name'       => 'Turno Tarde',
            'start_time' => '14:00',
            'end_time'   => '22:00',
        ]);
    }

    public function night(): static
    {
        return $this->state(fn (array $attributes) => [
            'type'       => ShiftType::Night,
            'name'       => 'Turno Noche',
            'start_time' => '22:00',
            'end_time'   => '06:00',
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ShiftStatus::Scheduled,
        ]);
    }

    public function forDate(string $date): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => $date,
        ]);
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenant->id,
        ]);
    }
}
