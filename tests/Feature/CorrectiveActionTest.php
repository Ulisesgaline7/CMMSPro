<?php

namespace Tests\Feature;

use App\Enums\CorrectiveActionStatus;
use App\Models\CorrectiveAction;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CorrectiveActionTest extends TestCase
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

    public function test_guests_are_redirected_from_corrective_actions_index(): void
    {
        $this->get(route('corrective-actions.index'))->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_corrective_actions_create(): void
    {
        $this->get(route('corrective-actions.create'))->assertRedirect(route('login'));
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_corrective_actions_index(): void
    {
        $this->actingAs($this->user);

        $this->get(route('corrective-actions.index'))->assertOk();
    }

    public function test_index_only_shows_tenant_corrective_actions(): void
    {
        $otherTenant = Tenant::factory()->create();
        CorrectiveAction::factory()->forTenant($otherTenant)->create(['title' => 'Other Tenant CAPA']);

        CorrectiveAction::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'title' => 'My CAPA',
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('corrective-actions.index'));
        $response->assertOk()
            ->assertSee('My CAPA')
            ->assertDontSee('Other Tenant CAPA');
    }

    public function test_index_can_be_filtered_by_status(): void
    {
        CorrectiveAction::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'title' => 'Open CAPA',
            'status' => CorrectiveActionStatus::Open,
        ]);
        CorrectiveAction::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'title' => 'Completed CAPA',
            'status' => CorrectiveActionStatus::Completed,
            'completed_at' => now(),
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('corrective-actions.index', ['status' => 'open']));
        $response->assertOk()
            ->assertSee('Open CAPA')
            ->assertDontSee('Completed CAPA');
    }

    public function test_index_can_be_filtered_by_type(): void
    {
        CorrectiveAction::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'title' => 'Corrective Action',
            'type' => 'corrective',
        ]);
        CorrectiveAction::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'title' => 'Preventive Action',
            'type' => 'preventive',
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('corrective-actions.index', ['type' => 'corrective']));
        $response->assertOk()
            ->assertSee('Corrective Action')
            ->assertDontSee('Preventive Action');
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function test_create_page_is_accessible(): void
    {
        $this->actingAs($this->user);

        $this->get(route('corrective-actions.create'))->assertOk();
    }

    public function test_user_can_create_a_corrective_action(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('corrective-actions.store'), [
            'title' => 'Corregir proceso de calibración',
            'type' => 'corrective',
            'priority' => 'high',
            'description' => 'Implementar procedimiento de calibración mensual',
        ]);

        $ca = CorrectiveAction::where('title', 'Corregir proceso de calibración')->first();
        $this->assertNotNull($ca);
        $this->assertEquals($this->user->tenant_id, $ca->tenant_id);
        $this->assertEquals($this->user->id, $ca->created_by);
        $this->assertEquals(CorrectiveActionStatus::Open, $ca->status);
        $this->assertStringStartsWith('CAP-', $ca->code);

        $response->assertRedirect(route('corrective-actions.show', $ca));
    }

    public function test_corrective_action_creation_requires_title(): void
    {
        $this->actingAs($this->user);

        $this->post(route('corrective-actions.store'), [
            'type' => 'corrective',
            'priority' => 'medium',
            'description' => 'Some description',
        ])->assertSessionHasErrors('title');
    }

    public function test_corrective_action_creation_requires_description(): void
    {
        $this->actingAs($this->user);

        $this->post(route('corrective-actions.store'), [
            'title' => 'Test CAPA',
            'type' => 'corrective',
            'priority' => 'medium',
        ])->assertSessionHasErrors('description');
    }

    public function test_corrective_action_creation_validates_type(): void
    {
        $this->actingAs($this->user);

        $this->post(route('corrective-actions.store'), [
            'title' => 'Test CAPA',
            'type' => 'invalid_type',
            'priority' => 'medium',
            'description' => 'Some description',
        ])->assertSessionHasErrors('type');
    }

    public function test_corrective_action_creation_validates_priority(): void
    {
        $this->actingAs($this->user);

        $this->post(route('corrective-actions.store'), [
            'title' => 'Test CAPA',
            'type' => 'corrective',
            'priority' => 'invalid_priority',
            'description' => 'Some description',
        ])->assertSessionHasErrors('priority');
    }

    public function test_corrective_action_code_is_sequential(): void
    {
        $this->actingAs($this->user);

        $this->post(route('corrective-actions.store'), [
            'title' => 'First CAPA',
            'type' => 'corrective',
            'priority' => 'medium',
            'description' => 'First description',
        ]);

        $this->post(route('corrective-actions.store'), [
            'title' => 'Second CAPA',
            'type' => 'preventive',
            'priority' => 'low',
            'description' => 'Second description',
        ]);

        $first = CorrectiveAction::where('title', 'First CAPA')->first();
        $second = CorrectiveAction::where('title', 'Second CAPA')->first();

        $this->assertEquals('CAP-000001', $first->code);
        $this->assertEquals('CAP-000002', $second->code);
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function test_user_can_view_own_corrective_action(): void
    {
        $ca = CorrectiveAction::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $this->get(route('corrective-actions.show', $ca))->assertOk();
    }

    public function test_user_cannot_view_another_tenants_corrective_action(): void
    {
        $otherTenant = Tenant::factory()->create();
        $ca = CorrectiveAction::factory()->forTenant($otherTenant)->create();

        $this->actingAs($this->user);

        $this->get(route('corrective-actions.show', $ca))->assertNotFound();
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function test_edit_page_is_accessible(): void
    {
        $ca = CorrectiveAction::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $this->get(route('corrective-actions.edit', $ca))->assertOk();
    }

    public function test_user_can_update_a_corrective_action(): void
    {
        $ca = CorrectiveAction::factory()->open()->create([
            'tenant_id' => $this->user->tenant_id,
            'title' => 'Original CAPA',
            'type' => 'corrective',
            'priority' => 'low',
            'description' => 'Original description',
        ]);

        $this->actingAs($this->user);

        $response = $this->patch(route('corrective-actions.update', $ca), [
            'title' => 'Updated CAPA',
            'type' => 'preventive',
            'status' => 'in_progress',
            'priority' => 'high',
            'description' => 'Updated description',
        ]);

        $ca->refresh();
        $this->assertEquals('Updated CAPA', $ca->title);
        $this->assertEquals('preventive', $ca->type);
        $this->assertEquals(CorrectiveActionStatus::InProgress, $ca->status);
        $this->assertEquals('high', $ca->priority);

        $response->assertRedirect(route('corrective-actions.show', $ca));
    }

    public function test_completed_at_is_set_when_status_changes_to_completed(): void
    {
        $ca = CorrectiveAction::factory()->open()->create([
            'tenant_id' => $this->user->tenant_id,
            'type' => 'corrective',
            'priority' => 'medium',
        ]);

        $this->actingAs($this->user);

        $this->patch(route('corrective-actions.update', $ca), [
            'title' => $ca->title,
            'type' => $ca->type,
            'status' => 'completed',
            'priority' => $ca->priority,
            'description' => $ca->description,
        ]);

        $ca->refresh();
        $this->assertEquals(CorrectiveActionStatus::Completed, $ca->status);
        $this->assertNotNull($ca->completed_at);
    }

    public function test_user_cannot_update_another_tenants_corrective_action(): void
    {
        $otherTenant = Tenant::factory()->create();
        $ca = CorrectiveAction::factory()->forTenant($otherTenant)->create();

        $this->actingAs($this->user);

        $this->patch(route('corrective-actions.update', $ca), [
            'title' => 'Hacked',
            'type' => 'corrective',
            'status' => 'open',
            'priority' => 'low',
            'description' => 'Hacked description',
        ])->assertNotFound();
    }
}
