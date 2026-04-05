<?php

namespace Tests\Feature;

use App\Enums\AuditStatus;
use App\Enums\AuditType;
use App\Models\Audit;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditTest extends TestCase
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

    public function test_guests_are_redirected_from_audits_index(): void
    {
        $this->get(route('audits.index'))->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_audits_create(): void
    {
        $this->get(route('audits.create'))->assertRedirect(route('login'));
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_audits_index(): void
    {
        $this->actingAs($this->user);

        $this->get(route('audits.index'))->assertOk();
    }

    public function test_audits_index_only_shows_tenant_audits(): void
    {
        $otherTenant = Tenant::factory()->create();
        Audit::factory()->forTenant($otherTenant)->create(['title' => 'Other Tenant Audit']);

        Audit::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'title' => 'My Audit',
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('audits.index'));
        $response->assertOk()
            ->assertSee('My Audit')
            ->assertDontSee('Other Tenant Audit');
    }

    public function test_audits_index_can_be_filtered_by_status(): void
    {
        Audit::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'title' => 'Planned Audit',
            'status' => AuditStatus::Planned,
        ]);
        Audit::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'title' => 'Completed Audit',
            'status' => AuditStatus::Completed,
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('audits.index', ['status' => 'planned']));
        $response->assertOk()
            ->assertSee('Planned Audit')
            ->assertDontSee('Completed Audit');
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function test_create_page_is_accessible(): void
    {
        $this->actingAs($this->user);

        $this->get(route('audits.create'))->assertOk();
    }

    public function test_user_can_create_an_audit(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('audits.store'), [
            'title' => 'Auditoría Interna Q1',
            'type' => 'internal',
            'scheduled_date' => '2026-04-01',
        ]);

        $audit = Audit::where('title', 'Auditoría Interna Q1')->first();
        $this->assertNotNull($audit);
        $this->assertEquals($this->user->tenant_id, $audit->tenant_id);
        $this->assertEquals($this->user->id, $audit->created_by);
        $this->assertEquals(AuditStatus::Planned, $audit->status);
        $this->assertStringStartsWith('AUD-', $audit->code);

        $response->assertRedirect(route('audits.show', $audit));
    }

    public function test_audit_creation_requires_title(): void
    {
        $this->actingAs($this->user);

        $this->post(route('audits.store'), [
            'type' => 'internal',
            'scheduled_date' => '2026-04-01',
        ])->assertSessionHasErrors('title');
    }

    public function test_audit_creation_requires_type(): void
    {
        $this->actingAs($this->user);

        $this->post(route('audits.store'), [
            'title' => 'Test Audit',
            'scheduled_date' => '2026-04-01',
        ])->assertSessionHasErrors('type');
    }

    public function test_audit_creation_validates_type_enum(): void
    {
        $this->actingAs($this->user);

        $this->post(route('audits.store'), [
            'title' => 'Test Audit',
            'type' => 'invalid_type',
            'scheduled_date' => '2026-04-01',
        ])->assertSessionHasErrors('type');
    }

    public function test_audit_code_is_sequential(): void
    {
        $this->actingAs($this->user);

        $this->post(route('audits.store'), [
            'title' => 'First Audit',
            'type' => 'internal',
            'scheduled_date' => '2026-04-01',
        ]);

        $this->post(route('audits.store'), [
            'title' => 'Second Audit',
            'type' => 'external',
            'scheduled_date' => '2026-04-15',
        ]);

        $first = Audit::where('title', 'First Audit')->first();
        $second = Audit::where('title', 'Second Audit')->first();

        $this->assertEquals('AUD-000001', $first->code);
        $this->assertEquals('AUD-000002', $second->code);
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function test_user_can_view_own_audit(): void
    {
        $audit = Audit::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $this->get(route('audits.show', $audit))->assertOk();
    }

    public function test_user_cannot_view_another_tenants_audit(): void
    {
        $otherTenant = Tenant::factory()->create();
        $audit = Audit::factory()->forTenant($otherTenant)->create();

        $this->actingAs($this->user);

        $this->get(route('audits.show', $audit))->assertNotFound();
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function test_edit_page_is_accessible(): void
    {
        $audit = Audit::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $this->get(route('audits.edit', $audit))->assertOk();
    }

    public function test_user_can_update_an_audit(): void
    {
        $audit = Audit::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'title' => 'Original Title',
            'type' => AuditType::Internal,
            'status' => AuditStatus::Planned,
        ]);

        $this->actingAs($this->user);

        $response = $this->patch(route('audits.update', $audit), [
            'title' => 'Updated Title',
            'type' => 'external',
            'status' => 'in_progress',
            'scheduled_date' => $audit->scheduled_date->format('Y-m-d'),
        ]);

        $audit->refresh();
        $this->assertEquals('Updated Title', $audit->title);
        $this->assertEquals(AuditType::External, $audit->type);
        $this->assertEquals(AuditStatus::InProgress, $audit->status);

        $response->assertRedirect(route('audits.show', $audit));
    }

    public function test_user_cannot_update_another_tenants_audit(): void
    {
        $otherTenant = Tenant::factory()->create();
        $audit = Audit::factory()->forTenant($otherTenant)->create();

        $this->actingAs($this->user);

        $this->patch(route('audits.update', $audit), [
            'title' => 'Hacked',
            'type' => 'internal',
            'status' => 'planned',
            'scheduled_date' => '2026-04-01',
        ])->assertNotFound();
    }

    // ── Findings ──────────────────────────────────────────────────────────────

    public function test_user_can_add_a_finding_to_audit(): void
    {
        $audit = Audit::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $response = $this->post(route('audits.findings.store', $audit), [
            'description' => 'Procedimiento no documentado',
            'severity' => 'minor',
        ]);

        $this->assertEquals(1, $audit->findings()->count());
        $finding = $audit->findings()->first();
        $this->assertEquals('F-001', $finding->code);
        $this->assertEquals('open', $finding->status);

        $response->assertRedirect(route('audits.show', $audit));
    }

    public function test_finding_creation_requires_description(): void
    {
        $audit = Audit::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $this->post(route('audits.findings.store', $audit), [
            'severity' => 'minor',
        ])->assertSessionHasErrors('description');
    }
}
