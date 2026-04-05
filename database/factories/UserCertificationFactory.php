<?php

namespace Database\Factories;

use App\Enums\CertificationStatus;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserCertification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserCertification>
 */
class UserCertificationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $issuedAt = fake()->dateTimeBetween('-5 years', '-1 month');
        $expiresAt = fake()->optional(0.8)->dateTimeBetween($issuedAt, '+3 years');
        $status = $expiresAt && $expiresAt < now()
            ? CertificationStatus::Expired
            : fake()->randomElement(CertificationStatus::cases());

        return [
            'tenant_id' => Tenant::factory(),
            'user_id' => User::factory(),
            'name' => fake()->randomElement([
                'OSHA 30-Hour General Industry',
                'ISO 9001 Internal Auditor',
                'NFPA 70E Electrical Safety',
                'Forklift Operator Certification',
                'First Aid & CPR',
                'Confined Space Entry',
                'Arc Flash Safety',
                'Rigging & Lifting',
                'Hazmat Awareness',
                'Lockout/Tagout (LOTO)',
            ]),
            'issuing_body' => fake()->randomElement([
                'OSHA',
                'ISO',
                'NFPA',
                'ANSI',
                'ASME',
                'Red Cross',
                'NSC',
                fake()->company(),
            ]),
            'certificate_number' => fake()->optional(0.7)->numerify('CERT-####-##'),
            'issued_at' => $issuedAt,
            'expires_at' => $expiresAt,
            'status' => $status,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CertificationStatus::Active,
            'expires_at' => fake()->dateTimeBetween('+1 month', '+2 years'),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CertificationStatus::Expired,
            'expires_at' => fake()->dateTimeBetween('-2 years', '-1 day'),
        ]);
    }

    public function expiringSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CertificationStatus::Active,
            'expires_at' => fake()->dateTimeBetween('now', '+29 days'),
        ]);
    }
}
