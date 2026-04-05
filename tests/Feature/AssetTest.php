<?php

namespace Tests\Feature;

use App\Enums\AssetCriticality;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $tenant = Tenant::factory()->create();
        $this->user = User::factory()->create(['tenant_id' => $tenant->id]);
    }

    // ── Auth ─────────────────────────────────────────────────────────────────

    public function test_guests_are_redirected_from_asset_index(): void
    {
        $this->get(route('assets.index'))->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_asset_create(): void
    {
        $this->get(route('assets.create'))->assertRedirect(route('login'));
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_assets_index(): void
    {
        $this->actingAs($this->user);

        $this->get(route('assets.index'))->assertOk();
    }

    public function test_assets_index_only_shows_tenant_assets(): void
    {
        $otherTenant = Tenant::factory()->create();
        Asset::factory()->forTenant($otherTenant)->create(['name' => 'Other Tenant Asset']);

        $ownAsset = Asset::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'name' => 'My Asset',
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('assets.index'));
        $response->assertOk()
            ->assertSee('My Asset')
            ->assertDontSee('Other Tenant Asset');
    }

    public function test_assets_index_can_be_filtered_by_search(): void
    {
        Asset::factory()->create(['tenant_id' => $this->user->tenant_id, 'name' => 'Compresor ABC', 'code' => 'COMP-001']);
        Asset::factory()->create(['tenant_id' => $this->user->tenant_id, 'name' => 'Bomba XYZ',     'code' => 'BOMB-001']);

        $this->actingAs($this->user);

        $response = $this->get(route('assets.index', ['search' => 'Compresor']));
        $response->assertOk()
            ->assertSee('Compresor ABC')
            ->assertDontSee('Bomba XYZ');
    }

    public function test_assets_index_can_be_filtered_by_status(): void
    {
        Asset::factory()->create(['tenant_id' => $this->user->tenant_id, 'name' => 'Active Asset',   'status' => AssetStatus::Active,   'code' => 'A-001']);
        Asset::factory()->create(['tenant_id' => $this->user->tenant_id, 'name' => 'Retired Asset',  'status' => AssetStatus::Retired,  'code' => 'A-002']);

        $this->actingAs($this->user);

        $response = $this->get(route('assets.index', ['status' => 'active']));
        $response->assertOk()
            ->assertSee('Active Asset')
            ->assertDontSee('Retired Asset');
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function test_create_page_is_accessible(): void
    {
        $this->actingAs($this->user);

        $this->get(route('assets.create'))->assertOk();
    }

    public function test_user_can_create_an_asset(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('assets.store'), [
            'name'        => 'Compresor de Aire',
            'code'        => 'COMP-001',
            'status'      => 'active',
            'criticality' => 'medium',
        ]);

        $asset = Asset::where('code', 'COMP-001')->first();
        $this->assertNotNull($asset);
        $this->assertEquals($this->user->tenant_id, $asset->tenant_id);
        $this->assertEquals('Compresor de Aire', $asset->name);

        $response->assertRedirect(route('assets.show', $asset));
    }

    public function test_asset_creation_requires_name(): void
    {
        $this->actingAs($this->user);

        $this->post(route('assets.store'), [
            'code'        => 'COMP-001',
            'status'      => 'active',
            'criticality' => 'medium',
        ])->assertSessionHasErrors('name');
    }

    public function test_asset_creation_requires_unique_code(): void
    {
        Asset::factory()->create(['tenant_id' => $this->user->tenant_id, 'code' => 'COMP-001']);

        $this->actingAs($this->user);

        $this->post(route('assets.store'), [
            'name'        => 'Otro Compresor',
            'code'        => 'COMP-001',
            'status'      => 'active',
            'criticality' => 'medium',
        ])->assertSessionHasErrors('code');
    }

    public function test_asset_creation_validates_status_enum(): void
    {
        $this->actingAs($this->user);

        $this->post(route('assets.store'), [
            'name'        => 'Test',
            'code'        => 'T-001',
            'status'      => 'invalid_status',
            'criticality' => 'medium',
        ])->assertSessionHasErrors('status');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function test_user_can_view_own_asset(): void
    {
        $asset = Asset::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $this->get(route('assets.show', $asset))->assertOk();
    }

    public function test_user_cannot_view_another_tenants_asset(): void
    {
        $otherTenant = Tenant::factory()->create();
        $asset = Asset::factory()->forTenant($otherTenant)->create();

        $this->actingAs($this->user);

        $this->get(route('assets.show', $asset))->assertNotFound();
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function test_edit_page_is_accessible(): void
    {
        $asset = Asset::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $this->get(route('assets.edit', $asset))->assertOk();
    }

    public function test_user_can_update_an_asset(): void
    {
        $asset = Asset::factory()->create([
            'tenant_id'   => $this->user->tenant_id,
            'name'        => 'Original Name',
            'code'        => 'ORIG-001',
            'status'      => AssetStatus::Active,
            'criticality' => AssetCriticality::Medium,
        ]);

        $this->actingAs($this->user);

        $response = $this->patch(route('assets.update', $asset), [
            'name'        => 'Updated Name',
            'code'        => 'ORIG-001',
            'status'      => 'under_maintenance',
            'criticality' => 'high',
        ]);

        $asset->refresh();
        $this->assertEquals('Updated Name', $asset->name);
        $this->assertEquals(AssetStatus::UnderMaintenance, $asset->status);
        $this->assertEquals(AssetCriticality::High, $asset->criticality);

        $response->assertRedirect(route('assets.show', $asset));
    }

    public function test_code_uniqueness_ignores_own_asset_on_update(): void
    {
        $asset = Asset::factory()->create([
            'tenant_id'   => $this->user->tenant_id,
            'name'        => 'Asset',
            'code'        => 'SAME-001',
            'status'      => AssetStatus::Active,
            'criticality' => AssetCriticality::Medium,
        ]);

        $this->actingAs($this->user);

        $this->patch(route('assets.update', $asset), [
            'name'        => 'Asset Updated',
            'code'        => 'SAME-001',
            'status'      => 'active',
            'criticality' => 'medium',
        ])->assertRedirect(route('assets.show', $asset));
    }

    public function test_user_cannot_update_another_tenants_asset(): void
    {
        $otherTenant = Tenant::factory()->create();
        $asset = Asset::factory()->forTenant($otherTenant)->create();

        $this->actingAs($this->user);

        $this->patch(route('assets.update', $asset), [
            'name'        => 'Hacked',
            'code'        => 'HACK-001',
            'status'      => 'active',
            'criticality' => 'low',
        ])->assertNotFound();
    }
}
