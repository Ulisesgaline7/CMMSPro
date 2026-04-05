<?php

namespace Tests\Feature\SuperAdmin;

use App\Enums\TenantStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createSuperAdmin(): User
    {
        return User::factory()->create([
            'is_super_admin' => true,
            'tenant_id' => null,
            'role' => UserRole::Admin,
            'status' => UserStatus::Active,
        ]);
    }

    private function createRegularAdmin(): User
    {
        $tenant = Tenant::factory()->create();

        return User::factory()->create([
            'tenant_id' => $tenant->id,
            'role' => UserRole::Admin,
            'status' => UserStatus::Active,
            'is_super_admin' => false,
        ]);
    }

    public function test_super_admin_can_view_dashboard(): void
    {
        $superAdmin = $this->createSuperAdmin();

        $response = $this->actingAs($superAdmin)->get('/super-admin');

        $response->assertStatus(200);
    }

    public function test_regular_user_cannot_access_super_admin(): void
    {
        $admin = $this->createRegularAdmin();

        $response = $this->actingAs($admin)->get('/super-admin');

        $response->assertStatus(403);
    }

    public function test_super_admin_can_list_tenants(): void
    {
        $superAdmin = $this->createSuperAdmin();
        Tenant::factory()->count(3)->create();

        $response = $this->actingAs($superAdmin)->get('/super-admin/tenants');

        $response->assertStatus(200);
    }

    public function test_super_admin_can_view_tenant(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::factory()->create();

        $response = $this->actingAs($superAdmin)->get("/super-admin/tenants/{$tenant->id}");

        $response->assertStatus(200);
    }

    public function test_super_admin_can_suspend_tenant(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::factory()->create(['status' => TenantStatus::Active]);

        $response = $this->actingAs($superAdmin)
            ->post("/super-admin/tenants/{$tenant->id}/suspend");

        $response->assertRedirect();
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'status' => TenantStatus::Suspended->value,
        ]);
    }

    public function test_super_admin_can_activate_tenant(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::factory()->create(['status' => TenantStatus::Suspended]);

        $response = $this->actingAs($superAdmin)
            ->post("/super-admin/tenants/{$tenant->id}/activate");

        $response->assertRedirect();
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'status' => TenantStatus::Active->value,
        ]);
    }

    public function test_super_admin_can_update_tenant(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::factory()->create();

        $response = $this->actingAs($superAdmin)
            ->patch("/super-admin/tenants/{$tenant->id}", [
                'name' => 'Updated Name',
                'plan' => 'professional',
                'status' => TenantStatus::Active->value,
                'max_users' => 50,
                'max_assets' => 1000,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_is_super_admin_method_returns_correct_value(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $regularUser = $this->createRegularAdmin();

        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertFalse($regularUser->isSuperAdmin());
    }
}
