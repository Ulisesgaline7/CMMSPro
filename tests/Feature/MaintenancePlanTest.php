<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\MaintenancePlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenancePlanTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    }

    // ── Auth ──────────────────────────────────────────────────────────────────

    public function test_guests_are_redirected_from_maintenance_plans_index(): void
    {
        $this->get(route('maintenance-plans.index'))->assertRedirect(route('login'));
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_maintenance_plans_index(): void
    {
        $this->actingAs($this->user);

        $this->get(route('maintenance-plans.index'))->assertOk();
    }

    public function test_index_only_shows_tenant_plans(): void
    {
        $otherTenant = Tenant::factory()->create();
        $otherAsset = Asset::factory()->create(['tenant_id' => $otherTenant->id]);

        MaintenancePlan::factory()->create([
            'tenant_id' => $otherTenant->id,
            'asset_id' => $otherAsset->id,
            'name' => 'Plan Otro Tenant',
        ]);

        $ownAsset = Asset::factory()->create(['tenant_id' => $this->tenant->id]);

        MaintenancePlan::factory()->create([
            'tenant_id' => $this->tenant->id,
            'asset_id' => $ownAsset->id,
            'name' => 'Mi Plan',
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('maintenance-plans.index'));
        $response->assertOk()
            ->assertSee('Mi Plan')
            ->assertDontSee('Plan Otro Tenant');
    }

    public function test_index_can_be_filtered_by_search(): void
    {
        $asset = Asset::factory()->create(['tenant_id' => $this->tenant->id]);

        MaintenancePlan::factory()->create(['tenant_id' => $this->tenant->id, 'asset_id' => $asset->id, 'name' => 'Cambio de aceite']);
        MaintenancePlan::factory()->create(['tenant_id' => $this->tenant->id, 'asset_id' => $asset->id, 'name' => 'Revisión eléctrica']);

        $this->actingAs($this->user);

        $response = $this->get(route('maintenance-plans.index', ['search' => 'aceite']));
        $response->assertOk()
            ->assertSee('Cambio de aceite')
            ->assertDontSee('Revisión eléctrica');
    }

    public function test_index_can_be_filtered_by_active_status(): void
    {
        $asset = Asset::factory()->create(['tenant_id' => $this->tenant->id]);

        MaintenancePlan::factory()->create(['tenant_id' => $this->tenant->id, 'asset_id' => $asset->id, 'name' => 'Plan Activo', 'is_active' => true]);
        MaintenancePlan::factory()->inactive()->create(['tenant_id' => $this->tenant->id, 'asset_id' => $asset->id, 'name' => 'Plan Inactivo']);

        $this->actingAs($this->user);

        $response = $this->get(route('maintenance-plans.index', ['status' => 'active']));
        $response->assertOk()
            ->assertSee('Plan Activo')
            ->assertDontSee('Plan Inactivo');
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function test_create_page_is_accessible(): void
    {
        $this->actingAs($this->user);

        $this->get(route('maintenance-plans.create'))->assertOk();
    }

    public function test_user_can_create_a_maintenance_plan(): void
    {
        $asset = Asset::factory()->create(['tenant_id' => $this->tenant->id]);

        $this->actingAs($this->user);

        $response = $this->post(route('maintenance-plans.store'), [
            'name' => 'Plan de Prueba',
            'asset_id' => $asset->id,
            'type' => 'preventive',
            'frequency' => 'monthly',
            'priority' => 'medium',
            'start_date' => '2026-01-01',
        ]);

        $plan = MaintenancePlan::where('name', 'Plan de Prueba')->first();
        $this->assertNotNull($plan);
        $this->assertEquals($this->user->tenant_id, $plan->tenant_id);
        $this->assertEquals('Plan de Prueba', $plan->name);

        $response->assertRedirect(route('maintenance-plans.show', $plan));
    }

    public function test_plan_creation_requires_name(): void
    {
        $asset = Asset::factory()->create(['tenant_id' => $this->tenant->id]);

        $this->actingAs($this->user);

        $this->post(route('maintenance-plans.store'), [
            'asset_id' => $asset->id,
            'type' => 'preventive',
            'frequency' => 'monthly',
            'priority' => 'medium',
            'start_date' => '2026-01-01',
        ])->assertSessionHasErrors('name');
    }

    public function test_plan_creation_requires_asset_id(): void
    {
        $this->actingAs($this->user);

        $this->post(route('maintenance-plans.store'), [
            'name' => 'Plan sin activo',
            'type' => 'preventive',
            'frequency' => 'monthly',
            'priority' => 'medium',
            'start_date' => '2026-01-01',
        ])->assertSessionHasErrors('asset_id');
    }

    public function test_plan_creation_requires_start_date(): void
    {
        $asset = Asset::factory()->create(['tenant_id' => $this->tenant->id]);

        $this->actingAs($this->user);

        $this->post(route('maintenance-plans.store'), [
            'name' => 'Plan sin fecha',
            'asset_id' => $asset->id,
            'type' => 'preventive',
            'frequency' => 'monthly',
            'priority' => 'medium',
        ])->assertSessionHasErrors('start_date');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function test_show_page_is_accessible_for_own_plan(): void
    {
        $asset = Asset::factory()->create(['tenant_id' => $this->tenant->id]);
        $plan = MaintenancePlan::factory()->create(['tenant_id' => $this->tenant->id, 'asset_id' => $asset->id]);

        $this->actingAs($this->user);

        $this->get(route('maintenance-plans.show', $plan))->assertOk();
    }

    public function test_cannot_view_another_tenants_plan(): void
    {
        $otherTenant = Tenant::factory()->create();
        $otherAsset = Asset::factory()->create(['tenant_id' => $otherTenant->id]);
        $plan = MaintenancePlan::factory()->create(['tenant_id' => $otherTenant->id, 'asset_id' => $otherAsset->id]);

        $this->actingAs($this->user);

        $this->get(route('maintenance-plans.show', $plan))->assertNotFound();
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function test_edit_page_is_accessible(): void
    {
        $asset = Asset::factory()->create(['tenant_id' => $this->tenant->id]);
        $plan = MaintenancePlan::factory()->create(['tenant_id' => $this->tenant->id, 'asset_id' => $asset->id]);

        $this->actingAs($this->user);

        $this->get(route('maintenance-plans.edit', $plan))->assertOk();
    }

    public function test_cannot_edit_another_tenants_plan(): void
    {
        $otherTenant = Tenant::factory()->create();
        $otherAsset = Asset::factory()->create(['tenant_id' => $otherTenant->id]);
        $plan = MaintenancePlan::factory()->create(['tenant_id' => $otherTenant->id, 'asset_id' => $otherAsset->id]);

        $this->actingAs($this->user);

        $this->get(route('maintenance-plans.edit', $plan))->assertNotFound();
    }

    public function test_user_can_update_a_plan(): void
    {
        $asset = Asset::factory()->create(['tenant_id' => $this->tenant->id]);
        $plan = MaintenancePlan::factory()->create([
            'tenant_id' => $this->tenant->id,
            'asset_id' => $asset->id,
            'name' => 'Nombre Original',
        ]);

        $this->actingAs($this->user);

        $response = $this->patch(route('maintenance-plans.update', $plan), [
            'name' => 'Nombre Actualizado',
            'asset_id' => $asset->id,
            'type' => 'preventive',
            'frequency' => 'weekly',
            'priority' => 'high',
            'start_date' => '2026-01-01',
        ]);

        $plan->refresh();
        $this->assertEquals('Nombre Actualizado', $plan->name);

        $response->assertRedirect(route('maintenance-plans.show', $plan));
    }

    public function test_cannot_update_another_tenants_plan(): void
    {
        $otherTenant = Tenant::factory()->create();
        $otherAsset = Asset::factory()->create(['tenant_id' => $otherTenant->id]);
        $plan = MaintenancePlan::factory()->create(['tenant_id' => $otherTenant->id, 'asset_id' => $otherAsset->id]);

        $this->actingAs($this->user);

        $this->patch(route('maintenance-plans.update', $plan), [
            'name' => 'Hacked',
            'asset_id' => $otherAsset->id,
            'type' => 'preventive',
            'frequency' => 'monthly',
            'priority' => 'medium',
            'start_date' => '2026-01-01',
        ])->assertNotFound();
    }
}
