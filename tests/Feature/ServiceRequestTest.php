<?php

namespace Tests\Feature;

use App\Enums\ServiceRequestCategory;
use App\Enums\ServiceRequestPriority;
use App\Enums\ServiceRequestStatus;
use App\Models\ServiceRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $tenant = Tenant::factory()->create();
        $this->user = User::factory()->create(['tenant_id' => $tenant->id]);
    }

    // ── Auth ──────────────────────────────────────────────────────────────────

    public function test_guests_are_redirected_from_service_requests_index(): void
    {
        $this->get(route('service-requests.index'))->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_service_requests_create(): void
    {
        $this->get(route('service-requests.create'))->assertRedirect(route('login'));
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_service_requests_index(): void
    {
        $this->actingAs($this->user)
            ->get(route('service-requests.index'))
            ->assertOk();
    }

    public function test_service_requests_index_only_shows_tenant_requests(): void
    {
        $otherTenant = Tenant::factory()->create();
        $otherUser = User::factory()->create(['tenant_id' => $otherTenant->id]);

        ServiceRequest::factory()->forTenant($otherTenant)->create([
            'requested_by' => $otherUser->id,
            'title'        => 'Other Tenant SR',
        ]);

        ServiceRequest::factory()->forTenant(Tenant::find($this->user->tenant_id))->create([
            'requested_by' => $this->user->id,
            'title'        => 'My SR',
        ]);

        $this->actingAs($this->user)
            ->get(route('service-requests.index'))
            ->assertOk()
            ->assertSee('My SR')
            ->assertDontSee('Other Tenant SR');
    }

    public function test_service_requests_index_shows_stats(): void
    {
        $this->actingAs($this->user)
            ->get(route('service-requests.index'))
            ->assertOk()
            ->assertViewHas('stats');
    }

    // ── Create / Store ────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_create_form(): void
    {
        $this->actingAs($this->user)
            ->get(route('service-requests.create'))
            ->assertOk()
            ->assertViewHas('technicians')
            ->assertViewHas('assets');
    }

    public function test_can_create_a_service_request(): void
    {
        $this->actingAs($this->user)
            ->post(route('service-requests.store'), [
                'title'    => 'Falla en HVAC planta 1',
                'category' => ServiceRequestCategory::HVAC->value,
                'priority' => ServiceRequestPriority::High->value,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('service_requests', [
            'tenant_id' => $this->user->tenant_id,
            'title'     => 'Falla en HVAC planta 1',
            'status'    => ServiceRequestStatus::Open->value,
        ]);
    }

    public function test_sla_deadline_is_calculated_on_store(): void
    {
        $this->actingAs($this->user)
            ->post(route('service-requests.store'), [
                'title'    => 'Test SLA',
                'category' => ServiceRequestCategory::General->value,
                'priority' => ServiceRequestPriority::High->value,
            ]);

        $sr = ServiceRequest::where('title', 'Test SLA')->first();

        $this->assertNotNull($sr->sla_deadline);
        // High = 8 hours SLA
        $this->assertTrue($sr->sla_deadline->isFuture());
    }

    public function test_service_request_code_is_generated(): void
    {
        $this->actingAs($this->user)
            ->post(route('service-requests.store'), [
                'title'    => 'Código test',
                'category' => ServiceRequestCategory::IT->value,
                'priority' => ServiceRequestPriority::Medium->value,
            ]);

        $sr = ServiceRequest::where('title', 'Código test')->first();

        $this->assertStringStartsWith('SR-', $sr->code);
    }

    // ── Validation ────────────────────────────────────────────────────────────

    public function test_title_is_required(): void
    {
        $this->actingAs($this->user)
            ->post(route('service-requests.store'), [
                'category' => ServiceRequestCategory::General->value,
                'priority' => ServiceRequestPriority::Low->value,
            ])
            ->assertSessionHasErrors('title');
    }

    public function test_invalid_category_is_rejected(): void
    {
        $this->actingAs($this->user)
            ->post(route('service-requests.store'), [
                'title'    => 'Test',
                'category' => 'invalid',
                'priority' => ServiceRequestPriority::Low->value,
            ])
            ->assertSessionHasErrors('category');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function test_can_view_service_request_details(): void
    {
        $sr = ServiceRequest::factory()->open()->forTenant(Tenant::find($this->user->tenant_id))->create([
            'requested_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('service-requests.show', $sr))
            ->assertOk()
            ->assertViewHas('sr');
    }

    public function test_cannot_view_another_tenants_service_request(): void
    {
        $otherTenant = Tenant::factory()->create();
        $otherUser = User::factory()->create(['tenant_id' => $otherTenant->id]);
        $otherSr = ServiceRequest::factory()->forTenant($otherTenant)->create([
            'requested_by' => $otherUser->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('service-requests.show', $otherSr))
            ->assertNotFound();
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function test_can_edit_a_service_request(): void
    {
        $sr = ServiceRequest::factory()->open()->forTenant(Tenant::find($this->user->tenant_id))->create([
            'requested_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('service-requests.edit', $sr))
            ->assertOk();
    }

    public function test_status_update_to_resolved_sets_resolved_at(): void
    {
        $sr = ServiceRequest::factory()->open()->forTenant(Tenant::find($this->user->tenant_id))->create([
            'requested_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->patch(route('service-requests.update', $sr), [
                'title'    => $sr->title,
                'category' => $sr->category->value,
                'priority' => $sr->priority->value,
                'status'   => ServiceRequestStatus::Resolved->value,
            ]);

        $sr->refresh();

        $this->assertEquals(ServiceRequestStatus::Resolved, $sr->status);
        $this->assertNotNull($sr->resolved_at);
        $this->assertNotNull($sr->resolution_time);
    }

    // ── Filters ───────────────────────────────────────────────────────────────

    public function test_can_filter_by_status(): void
    {
        $this->actingAs($this->user)
            ->get(route('service-requests.index', ['status' => 'open']))
            ->assertOk();
    }

    public function test_can_filter_by_category(): void
    {
        $this->actingAs($this->user)
            ->get(route('service-requests.index', ['category' => 'hvac']))
            ->assertOk();
    }
}
