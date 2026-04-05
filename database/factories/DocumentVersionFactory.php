<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\DocumentVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentVersion>
 */
class DocumentVersionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document_id' => Document::factory(),
            'created_by' => null,
            'version' => fake()->randomElement(['1.0', '1.1', '2.0', '2.1', '3.0']),
            'change_summary' => fake()->optional()->sentence(),
            'file_path' => fake()->optional()->filePath(),
            'file_name' => fake()->optional()->word() . '.pdf',
            'file_size' => fake()->optional()->numberBetween(1024, 10485760),
        ];
    }
}
