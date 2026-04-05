<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkOrderEditTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $tenant = Tenant::factory()->create();
        $this->user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'role'      => 'admin',
            'status'    => 'active',
        ]);
    }

    // ── Auth ─────────────────────────────────────────────────────────────────

    public function test_guests_are_redirected_from_work_order_edit(): void
    {
        $workOrder = WorkOrder::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->get(route('work-orders.edit', $workOrder))->assertRedirect(route('login'));
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_edit_page_for_own_work_order(): void
    {
        $workOrder = WorkOrder::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $this->get(route('work-orders.edit', $workOrder))->assertOk();
    }

    public function test_user_cannot_edit_another_tenants_work_order(): void
    {
        $otherTenant = Tenant::factory()->create();
        $workOrder = WorkOrder::factory()->create(['tenant_id' => $otherTenant->id]);

        $this->actingAs($this->user);

        $this->get(route('work-orders.edit', $workOrder))->assertNotFound();
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function test_user_can_update_a_work_order(): void
    {
        $workOrder = WorkOrder::factory()->pending()->create([
            'tenant_id' => $this->user->tenant_id,
            'title'     => 'Original Title',
            'type'      => 'corrective',
            'priority'  => 'medium',
        ]);

        $this->actingAs($this->user);

        $response = $this->patch(route('work-orders.update', $workOrder), [
            'title'    => 'Updated Title',
            'type'     => 'preventive',
            'priority' => 'high',
        ]);

        $workOrder->refresh();
        $this->assertEquals('Updated Title', $workOrder->title);
        $this->assertEquals('preventive', $workOrder->type->value);
        $this->assertEquals('high', $workOrder->priority->value);

        $response->assertRedirect(route('work-orders.show', $workOrder));
    }

    public function test_user_cannot_update_another_tenants_work_order(): void
    {
        $otherTenant = Tenant::factory()->create();
        $workOrder = WorkOrder::factory()->create(['tenant_id' => $otherTenant->id]);

        $this->actingAs($this->user);

        $this->patch(route('work-orders.update', $workOrder), [
            'title'    => 'Hacked',
            'type'     => 'corrective',
            'priority' => 'low',
        ])->assertNotFound();
    }

    public function test_update_validation_requires_title(): void
    {
        $workOrder = WorkOrder::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $this->patch(route('work-orders.update', $workOrder), [
            'title'    => '',
            'type'     => 'corrective',
            'priority' => 'medium',
        ])->assertSessionHasErrors('title');
    }

    public function test_asset_id_must_belong_to_same_tenant(): void
    {
        $otherTenant = Tenant::factory()->create();
        $otherAsset = Asset::factory()->create(['tenant_id' => $otherTenant->id]);

        $workOrder = WorkOrder::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $this->patch(route('work-orders.update', $workOrder), [
            'title'    => 'Valid Title',
            'type'     => 'corrective',
            'priority' => 'medium',
            'asset_id' => $otherAsset->id,
        ])->assertSessionHasErrors('asset_id');
    }
}
