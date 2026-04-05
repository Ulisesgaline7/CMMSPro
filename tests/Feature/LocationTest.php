<?php

namespace Tests\Feature;

use App\Enums\LocationType;
use App\Models\Location;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationTest extends TestCase
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

    public function test_guests_are_redirected_from_locations_index(): void
    {
        $this->get(route('locations.index'))->assertRedirect(route('login'));
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_locations_index(): void
    {
        $this->actingAs($this->user);

        $this->get(route('locations.index'))->assertOk();
    }

    public function test_locations_index_only_shows_tenant_locations(): void
    {
        $otherTenant = Tenant::factory()->create();
        Location::factory()->create(['tenant_id' => $otherTenant->id, 'name' => 'Other Tenant Location']);

        Location::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'name'      => 'My Location',
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('locations.index'));
        $response->assertOk()
            ->assertSee('My Location')
            ->assertDontSee('Other Tenant Location');
    }

    public function test_locations_index_can_be_filtered_by_search(): void
    {
        Location::factory()->create(['tenant_id' => $this->user->tenant_id, 'name' => 'Planta Norte', 'code' => 'PLT-001']);
        Location::factory()->create(['tenant_id' => $this->user->tenant_id, 'name' => 'Edificio Sur', 'code' => 'EDI-002']);

        $this->actingAs($this->user);

        $response = $this->get(route('locations.index', ['search' => 'Planta']));
        $response->assertOk()
            ->assertSee('Planta Norte')
            ->assertDontSee('Edificio Sur');
    }

    public function test_locations_index_can_be_filtered_by_type(): void
    {
        Location::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'name'      => 'Planta Principal',
            'type'      => LocationType::Plant,
        ]);
        Location::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'name'      => 'Edificio Administrativo',
            'type'      => LocationType::Building,
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('locations.index', ['type' => LocationType::Plant->value]));
        $response->assertOk()
            ->assertSee('Planta Principal')
            ->assertDontSee('Edificio Administrativo');
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function test_create_page_is_accessible(): void
    {
        $this->actingAs($this->user);

        $this->get(route('locations.create'))->assertOk();
    }

    public function test_user_can_create_a_location(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('locations.store'), [
            'name' => 'Planta Noreste',
            'type' => LocationType::Plant->value,
            'code' => 'PLT-NE',
        ]);

        $location = Location::where('code', 'PLT-NE')->first();
        $this->assertNotNull($location);
        $this->assertEquals($this->user->tenant_id, $location->tenant_id);
        $this->assertEquals('Planta Noreste', $location->name);

        $response->assertRedirect(route('locations.index'));
    }

    public function test_location_creation_requires_name(): void
    {
        $this->actingAs($this->user);

        $this->post(route('locations.store'), [
            'type' => LocationType::Plant->value,
        ])->assertSessionHasErrors('name');
    }

    public function test_location_creation_requires_type(): void
    {
        $this->actingAs($this->user);

        $this->post(route('locations.store'), [
            'name' => 'Some Location',
        ])->assertSessionHasErrors('type');
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function test_edit_page_is_accessible_for_own_location(): void
    {
        $location = Location::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $this->get(route('locations.edit', $location))->assertOk();
    }

    public function test_user_cannot_edit_another_tenants_location(): void
    {
        $otherTenant = Tenant::factory()->create();
        $location = Location::factory()->create(['tenant_id' => $otherTenant->id]);

        $this->actingAs($this->user);

        $this->get(route('locations.edit', $location))->assertNotFound();
    }

    public function test_user_can_update_a_location(): void
    {
        $location = Location::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'name'      => 'Original Name',
            'type'      => LocationType::Plant,
        ]);

        $this->actingAs($this->user);

        $response = $this->patch(route('locations.update', $location), [
            'name' => 'Updated Name',
            'type' => LocationType::Building->value,
        ]);

        $location->refresh();
        $this->assertEquals('Updated Name', $location->name);
        $this->assertEquals(LocationType::Building, $location->type);

        $response->assertRedirect(route('locations.index'));
    }

    public function test_user_cannot_update_another_tenants_location(): void
    {
        $otherTenant = Tenant::factory()->create();
        $location = Location::factory()->create(['tenant_id' => $otherTenant->id]);

        $this->actingAs($this->user);

        $this->patch(route('locations.update', $location), [
            'name' => 'Hacked',
            'type' => LocationType::Plant->value,
        ])->assertNotFound();
    }
}
