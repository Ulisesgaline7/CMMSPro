<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::withoutGlobalScopes()->get();

        foreach ($tenants as $index => $tenant) {
            $slugBase = str_replace('-', '', $tenant->slug);

            // Admin por tenant (primer tenant usa email fijo para pruebas)
            $adminEmail = $index === 0 ? 'admin@metalurgica.com' : "admin@{$slugBase}.com";

            User::factory()->create([
                'tenant_id' => $tenant->id,
                'name' => 'Admin '.$tenant->name,
                'email' => $adminEmail,
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'status' => UserStatus::Active,
                'employee_code' => 'ADM-'.str_pad($tenant->id, 3, '0', STR_PAD_LEFT),
            ]);

            // Supervisor
            User::factory()->create([
                'tenant_id' => $tenant->id,
                'name' => 'Supervisor '.$tenant->name,
                'email' => "supervisor@{$slugBase}.com",
                'password' => Hash::make('password'),
                'role' => UserRole::Supervisor,
                'status' => UserStatus::Active,
                'employee_code' => 'SUP-'.str_pad($tenant->id, 3, '0', STR_PAD_LEFT),
            ]);

            // 3 Técnicos
            User::factory()->count(3)->create([
                'tenant_id' => $tenant->id,
                'role' => UserRole::Technician,
                'status' => UserStatus::Active,
            ]);

            // 1 Lector
            User::factory()->create([
                'tenant_id' => $tenant->id,
                'role' => UserRole::Reader,
                'status' => UserStatus::Active,
            ]);

            // 1 Solicitante
            User::factory()->create([
                'tenant_id' => $tenant->id,
                'role' => UserRole::Requester,
                'status' => UserStatus::Active,
            ]);
        }
    }
}
