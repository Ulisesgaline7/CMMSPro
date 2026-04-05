<?php

namespace Tests\Feature;

use App\Models\Part;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
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

    public function test_guests_are_redirected_from_inventory_index(): void
    {
        $this->get(route('inventory.index'))->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_inventory_create(): void
    {
        $this->get(route('inventory.create'))->assertRedirect(route('login'));
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_inventory_index(): void
    {
        $this->actingAs($this->user);

        $this->get(route('inventory.index'))->assertOk();
    }

    public function test_inventory_index_only_shows_tenant_parts(): void
    {
        $otherTenant = Tenant::factory()->create();
        Part::factory()->create(['tenant_id' => $otherTenant->id, 'name' => 'Other Tenant Part']);

        Part::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'name'      => 'My Part',
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('inventory.index'));
        $response->assertOk()
            ->assertSee('My Part')
            ->assertDontSee('Other Tenant Part');
    }

    public function test_inventory_index_can_be_filtered_by_search(): void
    {
        Part::factory()->create(['tenant_id' => $this->user->tenant_id, 'name' => 'Rodamiento SKF', 'part_number' => 'PN-001']);
        Part::factory()->create(['tenant_id' => $this->user->tenant_id, 'name' => 'Correa en V',    'part_number' => 'PN-002']);

        $this->actingAs($this->user);

        $response = $this->get(route('inventory.index', ['search' => 'Rodamiento']));
        $response->assertOk()
            ->assertSee('Rodamiento SKF')
            ->assertDontSee('Correa en V');
    }

    public function test_inventory_index_can_be_filtered_by_low_stock(): void
    {
        Part::factory()->lowStock()->create(['tenant_id' => $this->user->tenant_id, 'name' => 'Low Stock Part']);
        Part::factory()->create(['tenant_id' => $this->user->tenant_id, 'name' => 'OK Stock Part', 'stock_quantity' => 20, 'min_stock' => 5]);

        $this->actingAs($this->user);

        $response = $this->get(route('inventory.index', ['stock' => 'low']));
        $response->assertOk()
            ->assertSee('Low Stock Part')
            ->assertDontSee('OK Stock Part');
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function test_create_page_is_accessible(): void
    {
        $this->actingAs($this->user);

        $this->get(route('inventory.create'))->assertOk();
    }

    public function test_user_can_create_a_part(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('inventory.store'), [
            'name'           => 'Rodamiento SKF 6205',
            'part_number'    => 'PN-SKF-6205',
            'unit'           => 'pieza',
            'stock_quantity' => 10,
            'min_stock'      => 3,
        ]);

        $part = Part::where('part_number', 'PN-SKF-6205')->first();
        $this->assertNotNull($part);
        $this->assertEquals($this->user->tenant_id, $part->tenant_id);
        $this->assertEquals('Rodamiento SKF 6205', $part->name);

        $response->assertRedirect(route('inventory.show', $part));
    }

    public function test_part_creation_requires_name(): void
    {
        $this->actingAs($this->user);

        $this->post(route('inventory.store'), [
            'unit'           => 'pieza',
            'stock_quantity' => 5,
            'min_stock'      => 2,
        ])->assertSessionHasErrors('name');
    }

    public function test_part_creation_requires_unique_part_number(): void
    {
        Part::factory()->create(['tenant_id' => $this->user->tenant_id, 'part_number' => 'PN-DUPLICATE']);

        $this->actingAs($this->user);

        $this->post(route('inventory.store'), [
            'name'           => 'Another Part',
            'part_number'    => 'PN-DUPLICATE',
            'unit'           => 'pieza',
            'stock_quantity' => 5,
            'min_stock'      => 2,
        ])->assertSessionHasErrors('part_number');
    }

    public function test_part_creation_validates_stock_quantity_is_not_negative(): void
    {
        $this->actingAs($this->user);

        $this->post(route('inventory.store'), [
            'name'           => 'Test Part',
            'unit'           => 'pieza',
            'stock_quantity' => -1,
            'min_stock'      => 2,
        ])->assertSessionHasErrors('stock_quantity');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function test_user_can_view_own_part(): void
    {
        $part = Part::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $this->get(route('inventory.show', $part))->assertOk();
    }

    public function test_user_cannot_view_another_tenants_part(): void
    {
        $otherTenant = Tenant::factory()->create();
        $part = Part::factory()->create(['tenant_id' => $otherTenant->id]);

        $this->actingAs($this->user);

        $this->get(route('inventory.show', $part))->assertNotFound();
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function test_edit_page_is_accessible(): void
    {
        $part = Part::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $this->get(route('inventory.edit', $part))->assertOk();
    }

    public function test_user_can_update_a_part(): void
    {
        $part = Part::factory()->create([
            'tenant_id'      => $this->user->tenant_id,
            'name'           => 'Original Name',
            'unit'           => 'pieza',
            'stock_quantity' => 10,
            'min_stock'      => 2,
        ]);

        $this->actingAs($this->user);

        $response = $this->patch(route('inventory.update', $part), [
            'name'           => 'Updated Name',
            'unit'           => 'litro',
            'stock_quantity' => 15,
            'min_stock'      => 5,
        ]);

        $part->refresh();
        $this->assertEquals('Updated Name', $part->name);
        $this->assertEquals(15, $part->stock_quantity);
        $this->assertEquals(5, $part->min_stock);

        $response->assertRedirect(route('inventory.show', $part));
    }

    public function test_part_number_uniqueness_ignores_own_part_on_update(): void
    {
        $part = Part::factory()->create([
            'tenant_id'      => $this->user->tenant_id,
            'name'           => 'Part',
            'part_number'    => 'PN-SAME',
            'unit'           => 'pieza',
            'stock_quantity' => 5,
            'min_stock'      => 1,
        ]);

        $this->actingAs($this->user);

        $this->patch(route('inventory.update', $part), [
            'name'           => 'Part Updated',
            'part_number'    => 'PN-SAME',
            'unit'           => 'pieza',
            'stock_quantity' => 5,
            'min_stock'      => 1,
        ])->assertRedirect(route('inventory.show', $part));
    }

    public function test_user_cannot_update_another_tenants_part(): void
    {
        $otherTenant = Tenant::factory()->create();
        $part = Part::factory()->create(['tenant_id' => $otherTenant->id]);

        $this->actingAs($this->user);

        $this->patch(route('inventory.update', $part), [
            'name'           => 'Hacked',
            'unit'           => 'pieza',
            'stock_quantity' => 99,
            'min_stock'      => 0,
        ])->assertNotFound();
    }
}
