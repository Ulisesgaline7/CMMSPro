<?php

namespace Tests\Feature;

use App\Enums\PurchaseOrderPriority;
use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
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

    public function test_guests_are_redirected_from_purchase_orders_index(): void
    {
        $this->get(route('purchase-orders.index'))->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_purchase_orders_create(): void
    {
        $this->get(route('purchase-orders.create'))->assertRedirect(route('login'));
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_purchase_orders_index(): void
    {
        $this->actingAs($this->user);

        $this->get(route('purchase-orders.index'))->assertOk();
    }

    public function test_index_only_shows_tenant_purchase_orders(): void
    {
        $otherTenant = Tenant::factory()->create();
        PurchaseOrder::factory()->create([
            'tenant_id'     => $otherTenant->id,
            'supplier_name' => 'Other Tenant Supplier',
        ]);

        PurchaseOrder::factory()->create([
            'tenant_id'     => $this->user->tenant_id,
            'supplier_name' => 'My Supplier',
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('purchase-orders.index'));
        $response->assertOk()
            ->assertSee('My Supplier')
            ->assertDontSee('Other Tenant Supplier');
    }

    public function test_index_can_be_filtered_by_search(): void
    {
        PurchaseOrder::factory()->create([
            'tenant_id'     => $this->user->tenant_id,
            'supplier_name' => 'Ferretería Industrial',
            'code'          => 'PO-000001',
        ]);
        PurchaseOrder::factory()->create([
            'tenant_id'     => $this->user->tenant_id,
            'supplier_name' => 'Refacciones López',
            'code'          => 'PO-000002',
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('purchase-orders.index', ['search' => 'Ferretería']));
        $response->assertOk()
            ->assertSee('Ferretería Industrial')
            ->assertDontSee('Refacciones López');
    }

    public function test_index_can_be_filtered_by_status(): void
    {
        PurchaseOrder::factory()->draft()->create([
            'tenant_id'     => $this->user->tenant_id,
            'supplier_name' => 'Draft Supplier',
            'code'          => 'PO-000010',
        ]);
        PurchaseOrder::factory()->received()->create([
            'tenant_id'     => $this->user->tenant_id,
            'supplier_name' => 'Received Supplier',
            'code'          => 'PO-000011',
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('purchase-orders.index', ['status' => 'draft']));
        $response->assertOk()
            ->assertSee('Draft Supplier')
            ->assertDontSee('Received Supplier');
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function test_create_page_is_accessible(): void
    {
        $this->actingAs($this->user);

        $this->get(route('purchase-orders.create'))->assertOk();
    }

    public function test_user_can_create_a_purchase_order(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('purchase-orders.store'), [
            'supplier_name'     => 'Proveedor Test S.A.',
            'supplier_contact'  => 'compras@proveedor.com',
            'priority'          => 'medium',
            'currency'          => 'MXN',
            'items'             => [
                [
                    'description' => 'Rodamiento SKF 6205',
                    'part_number' => 'SKF-6205',
                    'quantity'    => 5,
                    'unit'        => 'pz',
                    'unit_price'  => 150.00,
                ],
            ],
        ]);

        $order = PurchaseOrder::where('supplier_name', 'Proveedor Test S.A.')->first();
        $this->assertNotNull($order);
        $this->assertEquals($this->user->tenant_id, $order->tenant_id);
        $this->assertEquals(PurchaseOrderStatus::Draft, $order->status);
        $this->assertStringStartsWith('PO-', $order->code);
        $this->assertEquals(750.00, (float) $order->total_amount);

        $this->assertCount(1, $order->items);
        $this->assertEquals('Rodamiento SKF 6205', $order->items->first()->description);

        $response->assertRedirect(route('purchase-orders.show', $order));
    }

    public function test_purchase_order_creation_requires_supplier_name(): void
    {
        $this->actingAs($this->user);

        $this->post(route('purchase-orders.store'), [
            'priority' => 'medium',
            'items'    => [
                ['description' => 'Item', 'quantity' => 1, 'unit' => 'pz', 'unit_price' => 100],
            ],
        ])->assertSessionHasErrors('supplier_name');
    }

    public function test_purchase_order_creation_requires_at_least_one_item(): void
    {
        $this->actingAs($this->user);

        $this->post(route('purchase-orders.store'), [
            'supplier_name' => 'Test Supplier',
            'priority'      => 'medium',
            'items'         => [],
        ])->assertSessionHasErrors('items');
    }

    public function test_purchase_order_creation_validates_item_quantity(): void
    {
        $this->actingAs($this->user);

        $this->post(route('purchase-orders.store'), [
            'supplier_name' => 'Test Supplier',
            'priority'      => 'medium',
            'items'         => [
                ['description' => 'Item', 'quantity' => 0, 'unit' => 'pz', 'unit_price' => 100],
            ],
        ])->assertSessionHasErrors('items.0.quantity');
    }

    public function test_generated_code_is_sequential(): void
    {
        $this->actingAs($this->user);

        $this->post(route('purchase-orders.store'), [
            'supplier_name' => 'Supplier A',
            'priority'      => 'low',
            'currency'      => 'MXN',
            'items'         => [['description' => 'Item A', 'quantity' => 1, 'unit' => 'pz', 'unit_price' => 100]],
        ]);

        $this->post(route('purchase-orders.store'), [
            'supplier_name' => 'Supplier B',
            'priority'      => 'low',
            'currency'      => 'MXN',
            'items'         => [['description' => 'Item B', 'quantity' => 1, 'unit' => 'pz', 'unit_price' => 100]],
        ]);

        $orders = PurchaseOrder::withoutGlobalScopes()
            ->where('code', 'like', 'PO-%')
            ->orderBy('id')
            ->get();

        $this->assertCount(2, $orders);
        $this->assertEquals('PO-000001', $orders->first()->code);
        $this->assertEquals('PO-000002', $orders->last()->code);
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function test_user_can_view_own_purchase_order(): void
    {
        $order = PurchaseOrder::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $this->get(route('purchase-orders.show', $order))->assertOk();
    }

    public function test_user_cannot_view_another_tenants_purchase_order(): void
    {
        $otherTenant = Tenant::factory()->create();
        $order = PurchaseOrder::factory()->create(['tenant_id' => $otherTenant->id]);

        $this->actingAs($this->user);

        $this->get(route('purchase-orders.show', $order))->assertNotFound();
    }

    public function test_show_page_displays_items(): void
    {
        $order = PurchaseOrder::factory()->create(['tenant_id' => $this->user->tenant_id]);
        PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $order->id,
            'description'       => 'Bearing XYZ',
        ]);

        $this->actingAs($this->user);

        $this->get(route('purchase-orders.show', $order))
            ->assertOk()
            ->assertSee('Bearing XYZ');
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function test_edit_page_is_accessible(): void
    {
        $order = PurchaseOrder::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $this->get(route('purchase-orders.edit', $order))->assertOk();
    }

    public function test_user_cannot_access_another_tenants_edit_page(): void
    {
        $otherTenant = Tenant::factory()->create();
        $order = PurchaseOrder::factory()->create(['tenant_id' => $otherTenant->id]);

        $this->actingAs($this->user);

        $this->get(route('purchase-orders.edit', $order))->assertNotFound();
    }

    public function test_user_can_update_a_purchase_order(): void
    {
        $order = PurchaseOrder::factory()->create([
            'tenant_id'     => $this->user->tenant_id,
            'supplier_name' => 'Original Supplier',
        ]);

        $this->actingAs($this->user);

        $response = $this->patch(route('purchase-orders.update', $order), [
            'supplier_name'    => 'Updated Supplier',
            'priority'         => 'high',
            'status'           => 'pending_approval',
            'currency'         => 'MXN',
            'items'            => [
                [
                    'description' => 'Updated Item',
                    'quantity'    => 2,
                    'unit'        => 'pz',
                    'unit_price'  => 200.00,
                ],
            ],
        ]);

        $order->refresh();
        $this->assertEquals('Updated Supplier', $order->supplier_name);
        $this->assertEquals(PurchaseOrderPriority::High, $order->priority);
        $this->assertEquals(PurchaseOrderStatus::PendingApproval, $order->status);
        $this->assertEquals(400.00, (float) $order->total_amount);
        $this->assertCount(1, $order->items);

        $response->assertRedirect(route('purchase-orders.show', $order));
    }

    public function test_user_cannot_update_another_tenants_purchase_order(): void
    {
        $otherTenant = Tenant::factory()->create();
        $order = PurchaseOrder::factory()->create(['tenant_id' => $otherTenant->id]);

        $this->actingAs($this->user);

        $this->patch(route('purchase-orders.update', $order), [
            'supplier_name' => 'Hacked Supplier',
            'priority'      => 'medium',
            'status'        => 'draft',
            'currency'      => 'MXN',
            'items'         => [
                ['description' => 'Hacked Item', 'quantity' => 1, 'unit' => 'pz', 'unit_price' => 999],
            ],
        ])->assertNotFound();
    }

    // ── Tenant Isolation ──────────────────────────────────────────────────────

    public function test_stats_only_count_tenant_orders(): void
    {
        $otherTenant = Tenant::factory()->create();

        PurchaseOrder::factory()->draft()->create(['tenant_id' => $this->user->tenant_id, 'code' => 'PO-T1-001']);
        PurchaseOrder::factory()->draft()->create(['tenant_id' => $otherTenant->id, 'code' => 'PO-T2-001']);

        $this->actingAs($this->user);

        $response = $this->get(route('purchase-orders.index'));
        $response->assertOk();

        // The view should only show 1 total (tenant-scoped), not 2
        $viewData = $response->viewData('stats');
        $this->assertEquals(1, $viewData['total']);
        $this->assertEquals(1, $viewData['draft']);
    }
}
