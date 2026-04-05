<?php

namespace Database\Factories;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Models\Document;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(DocumentStatus::cases());
        $type = fake()->randomElement(DocumentType::cases());
        $categories = ['safety', 'quality', 'maintenance', 'regulatory', null];

        return [
            'tenant_id' => Tenant::factory(),
            'created_by' => null,
            'asset_id' => null,
            'code' => 'DOC-' . fake()->unique()->numerify('######'),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'type' => $type,
            'status' => $status,
            'category' => fake()->randomElement($categories),
            'current_version' => fake()->randomElement(['1.0', '1.1', '2.0', '2.1', '3.0']),
            'review_date' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'approved_by' => null,
            'approved_at' => $status === DocumentStatus::Approved ? fake()->dateTimeBetween('-1 year', 'now') : null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DocumentStatus::Draft,
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DocumentStatus::Approved,
            'approved_at' => now()->subDays(fake()->numberBetween(1, 90)),
        ]);
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenant->id,
        ]);
    }
}
