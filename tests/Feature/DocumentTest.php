<?php

namespace Tests\Feature;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentTest extends TestCase
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

    public function test_guests_are_redirected_from_documents_index(): void
    {
        $this->get(route('documents.index'))->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_documents_create(): void
    {
        $this->get(route('documents.create'))->assertRedirect(route('login'));
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_documents_index(): void
    {
        $this->actingAs($this->user);

        $this->get(route('documents.index'))->assertOk();
    }

    public function test_documents_index_only_shows_tenant_documents(): void
    {
        $otherTenant = Tenant::factory()->create();
        Document::factory()->forTenant($otherTenant)->create(['title' => 'Other Tenant Doc']);

        Document::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'title' => 'My Document',
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('documents.index'));
        $response->assertOk()
            ->assertSee('My Document')
            ->assertDontSee('Other Tenant Doc');
    }

    public function test_documents_index_can_be_filtered_by_search(): void
    {
        Document::factory()->create(['tenant_id' => $this->user->tenant_id, 'title' => 'Procedimiento LOTO', 'code' => 'DOC-000001']);
        Document::factory()->create(['tenant_id' => $this->user->tenant_id, 'title' => 'Manual de Seguridad', 'code' => 'DOC-000002']);

        $this->actingAs($this->user);

        $response = $this->get(route('documents.index', ['search' => 'LOTO']));
        $response->assertOk()
            ->assertSee('Procedimiento LOTO')
            ->assertDontSee('Manual de Seguridad');
    }

    public function test_documents_index_can_be_filtered_by_status(): void
    {
        Document::factory()->draft()->create(['tenant_id' => $this->user->tenant_id, 'title' => 'Draft Doc']);
        Document::factory()->approved()->create(['tenant_id' => $this->user->tenant_id, 'title' => 'Approved Doc']);

        $this->actingAs($this->user);

        $response = $this->get(route('documents.index', ['status' => 'draft']));
        $response->assertOk()
            ->assertSee('Draft Doc')
            ->assertDontSee('Approved Doc');
    }

    public function test_documents_index_can_be_filtered_by_type(): void
    {
        Document::factory()->create(['tenant_id' => $this->user->tenant_id, 'title' => 'Procedure Doc', 'type' => DocumentType::Procedure]);
        Document::factory()->create(['tenant_id' => $this->user->tenant_id, 'title' => 'Manual Doc', 'type' => DocumentType::Manual]);

        $this->actingAs($this->user);

        $response = $this->get(route('documents.index', ['type' => 'procedure']));
        $response->assertOk()
            ->assertSee('Procedure Doc')
            ->assertDontSee('Manual Doc');
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_create_document_form(): void
    {
        $this->actingAs($this->user);

        $this->get(route('documents.create'))->assertOk();
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function test_user_can_create_a_document(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('documents.store'), [
            'title' => 'Procedimiento de Bloqueo',
            'type' => 'procedure',
            'description' => 'Descripción del procedimiento.',
            'category' => 'safety',
            'current_version' => '1.0',
            'review_date' => now()->addYear()->format('Y-m-d'),
        ]);

        $document = Document::where('title', 'Procedimiento de Bloqueo')->first();
        $this->assertNotNull($document);

        $response->assertRedirect(route('documents.show', $document));

        $this->assertDatabaseHas('documents', [
            'title' => 'Procedimiento de Bloqueo',
            'type' => 'procedure',
            'status' => 'draft',
            'tenant_id' => $this->user->tenant_id,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_document_code_is_auto_generated_on_create(): void
    {
        $this->actingAs($this->user);

        $this->post(route('documents.store'), [
            'title' => 'Test Document',
            'type' => 'manual',
        ]);

        $document = Document::where('title', 'Test Document')->first();
        $this->assertStringStartsWith('DOC-', $document->code);
    }

    public function test_document_store_requires_title(): void
    {
        $this->actingAs($this->user);

        $this->post(route('documents.store'), [
            'type' => 'procedure',
        ])->assertSessionHasErrors('title');
    }

    public function test_document_store_requires_type(): void
    {
        $this->actingAs($this->user);

        $this->post(route('documents.store'), [
            'title' => 'Test Document',
        ])->assertSessionHasErrors('type');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function test_user_can_view_own_tenant_document(): void
    {
        $document = Document::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $this->get(route('documents.show', $document))->assertOk();
    }

    public function test_user_cannot_view_other_tenant_document(): void
    {
        $otherTenant = Tenant::factory()->create();
        $document = Document::factory()->forTenant($otherTenant)->create();

        $this->actingAs($this->user);

        $this->get(route('documents.show', $document))->assertNotFound();
    }

    public function test_show_displays_document_versions(): void
    {
        $document = Document::factory()->create(['tenant_id' => $this->user->tenant_id]);
        DocumentVersion::factory()->create([
            'document_id' => $document->id,
            'version' => '1.0',
            'change_summary' => 'Versión inicial',
        ]);

        $this->actingAs($this->user);

        $this->get(route('documents.show', $document))
            ->assertOk()
            ->assertSee('1.0')
            ->assertSee('Versión inicial');
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    public function test_user_can_view_edit_form(): void
    {
        $document = Document::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $this->get(route('documents.edit', $document))->assertOk();
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function test_user_can_update_a_document(): void
    {
        $document = Document::factory()->draft()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user);

        $response = $this->patch(route('documents.update', $document), [
            'title' => 'Título Actualizado',
            'type' => 'manual',
            'status' => 'review',
        ]);

        $response->assertRedirect(route('documents.show', $document));

        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'title' => 'Título Actualizado',
            'type' => 'manual',
            'status' => 'review',
        ]);
    }

    public function test_user_cannot_update_other_tenant_document(): void
    {
        $otherTenant = Tenant::factory()->create();
        $document = Document::factory()->forTenant($otherTenant)->create();

        $this->actingAs($this->user);

        $this->patch(route('documents.update', $document), [
            'title' => 'Hacked Title',
            'type' => 'manual',
        ])->assertNotFound();
    }

    // ── Tenant isolation ──────────────────────────────────────────────────────

    public function test_tenant_isolation_on_index(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        $userA = User::factory()->create(['tenant_id' => $tenantA->id]);
        $userB = User::factory()->create(['tenant_id' => $tenantB->id]);

        Document::factory()->forTenant($tenantA)->create(['title' => 'Tenant A Doc']);
        Document::factory()->forTenant($tenantB)->create(['title' => 'Tenant B Doc']);

        $this->actingAs($userA);

        $response = $this->get(route('documents.index'));
        $response->assertOk()
            ->assertSee('Tenant A Doc')
            ->assertDontSee('Tenant B Doc');
    }
}
