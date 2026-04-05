<?php

namespace Tests\Feature;

use App\Models\AssetCategory;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetCategoryTest extends TestCase
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

    public function test_guests_are_redirected_from_index(): void
    {
        $this->get(route('asset-categories.index'))->assertRedirect(route('login'));
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_index(): void
    {
        $this->actingAs($this->user);

        $this->get(route('asset-categories.index'))->assertOk();
    }

    public function test_index_only_shows_own_tenant_categories(): void
    {
        $otherTenant = Tenant::factory()->create();
        AssetCategory::factory()->create(['tenant_id' => $otherTenant->id, 'name' => 'Other Tenant Category']);

        AssetCategory::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'name'      => 'My Category',
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('asset-categories.index'));
        $response->assertOk()
            ->assertSee('My Category')
            ->assertDontSee('Other Tenant Category');
    }

    public function test_index_can_be_filtered_by_search(): void
    {
        AssetCategory::factory()->create(['tenant_id' => $this->user->tenant_id, 'name' => 'Bombas y Compresores', 'code' => 'CAT-001']);
        AssetCategory::factory()->create(['tenant_id' => $this->user->tenant_id, 'name' => 'Motores Eléctricos',   'code' => 'CAT-002']);

        $this->actingAs($this->user);

        $response = $this->get(route('asset-categories.index', ['search' => 'Bombas']));
        $response->assertOk()
            ->assertSee('Bombas y Compresores')
            ->assertDontSee('Motores Eléctricos');
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function test_create_page_is_accessible(): void
    {
        $this->actingAs($this->user);

        $this->get(route('asset-categories.create'))->assertOk();
    }

    public function test_user_can_create_a_category(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('asset-categories.store'), [
            'name' => 'Bombas y Compresores',
            'code' => 'CAT-001',
        ]);

        $category = AssetCategory::where('code', 'CAT-001')->first();
        $this->assertNotNull($category);
        $this->assertEquals($this->user->tenant_id, $category->tenant_id);
        $this->assertEquals('Bombas y Compresores', $category->name);

        $response->assertRedirect(route('asset-categories.index'));
    }

    public function test_creation_requires_name(): void
    {
        $this->actingAs($this->user);

        $this->post(route('asset-categories.store'), [
            'code' => 'CAT-001',
        ])->assertSessionHasErrors('name');
    }

    public function test_creation_requires_unique_code(): void
    {
        AssetCategory::factory()->create(['tenant_id' => $this->user->tenant_id, 'code' => 'CAT-DUPE']);

        $this->actingAs($this->user);

        $this->post(route('asset-categories.store'), [
            'name' => 'Another Category',
            'code' => 'CAT-DUPE',
        ])->assertSessionHasErrors('code');
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function test_edit_page_is_accessible_for_own_category(): void
    {
        $category = AssetCategory::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $this->get(route('asset-categories.edit', $category))->assertOk();
    }

    public function test_cannot_access_edit_page_of_another_tenants_category(): void
    {
        $otherTenant = Tenant::factory()->create();
        $category = AssetCategory::factory()->create(['tenant_id' => $otherTenant->id]);

        $this->actingAs($this->user);

        $this->get(route('asset-categories.edit', $category))->assertNotFound();
    }

    public function test_user_can_update_a_category(): void
    {
        $category = AssetCategory::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'name'      => 'Original Name',
        ]);

        $this->actingAs($this->user);

        $response = $this->patch(route('asset-categories.update', $category), [
            'name' => 'Updated Name',
            'code' => 'CAT-UPD',
        ]);

        $category->refresh();
        $this->assertEquals('Updated Name', $category->name);
        $this->assertEquals('CAT-UPD', $category->code);

        $response->assertRedirect(route('asset-categories.index'));
    }

    public function test_code_uniqueness_ignores_own_category_on_update(): void
    {
        $category = AssetCategory::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'name'      => 'My Category',
            'code'      => 'CAT-SAME',
        ]);

        $this->actingAs($this->user);

        $this->patch(route('asset-categories.update', $category), [
            'name' => 'My Category Updated',
            'code' => 'CAT-SAME',
        ])->assertRedirect(route('asset-categories.index'));
    }

    public function test_cannot_update_another_tenants_category(): void
    {
        $otherTenant = Tenant::factory()->create();
        $category = AssetCategory::factory()->create(['tenant_id' => $otherTenant->id]);

        $this->actingAs($this->user);

        $this->patch(route('asset-categories.update', $category), [
            'name' => 'Hacked',
        ])->assertNotFound();
    }
}
