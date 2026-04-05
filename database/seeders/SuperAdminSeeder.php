<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@cmms.app',
            'password' => Hash::make('superadmin123'),
            'tenant_id' => null,
            'role' => UserRole::Admin,
            'status' => UserStatus::Active,
            'is_super_admin' => true,
            'employee_code' => 'SA-001',
        ]);
    }
}
