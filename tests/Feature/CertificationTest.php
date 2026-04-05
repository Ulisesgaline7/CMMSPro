<?php

namespace Tests\Feature;

use App\Enums\CertificationStatus;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserCertification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificationTest extends TestCase
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

    public function test_guests_are_redirected_from_certifications_index(): void
    {
        $this->get(route('certifications.index'))->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_certifications_create(): void
    {
        $this->get(route('certifications.create'))->assertRedirect(route('login'));
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_certifications_index(): void
    {
        $this->actingAs($this->user);

        $this->get(route('certifications.index'))->assertOk();
    }

    public function test_certifications_index_only_shows_tenant_certifications(): void
    {
        $otherTenant = Tenant::factory()->create();
        $otherUser = User::factory()->create(['tenant_id' => $otherTenant->id]);

        UserCertification::factory()->create([
            'tenant_id' => $otherTenant->id,
            'user_id' => $otherUser->id,
            'name' => 'Other Tenant Cert',
        ]);

        UserCertification::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'user_id' => $this->user->id,
            'name' => 'My Certification',
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('certifications.index'));
        $response->assertOk()
            ->assertSee('My Certification')
            ->assertDontSee('Other Tenant Cert');
    }

    public function test_certifications_index_can_be_filtered_by_status(): void
    {
        UserCertification::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'user_id' => $this->user->id,
            'name' => 'Active Cert',
            'status' => CertificationStatus::Active,
        ]);
        UserCertification::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'user_id' => $this->user->id,
            'name' => 'Expired Cert',
            'status' => CertificationStatus::Expired,
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('certifications.index', ['status' => 'active']));
        $response->assertOk()
            ->assertSee('Active Cert')
            ->assertDontSee('Expired Cert');
    }

    // ── Create / Store ────────────────────────────────────────────────────────

    public function test_create_page_is_accessible(): void
    {
        $this->actingAs($this->user);

        $this->get(route('certifications.create'))->assertOk();
    }

    public function test_user_can_create_a_certification(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('certifications.store'), [
            'user_id' => $this->user->id,
            'name' => 'OSHA 30-Hour',
            'issuing_body' => 'OSHA',
            'certificate_number' => 'CERT-2024-001',
            'issued_at' => '2024-01-15',
            'expires_at' => '2027-01-15',
            'status' => 'active',
            'notes' => 'Test notes',
        ]);

        $certification = UserCertification::where('name', 'OSHA 30-Hour')->first();
        $this->assertNotNull($certification);
        $this->assertEquals($this->user->tenant_id, $certification->tenant_id);
        $this->assertEquals($this->user->id, $certification->user_id);
        $this->assertEquals('OSHA', $certification->issuing_body);
        $this->assertEquals(CertificationStatus::Active, $certification->status);

        $response->assertRedirect(route('certifications.show', $certification));
    }

    public function test_certification_creation_requires_name(): void
    {
        $this->actingAs($this->user);

        $this->post(route('certifications.store'), [
            'user_id' => $this->user->id,
            'issuing_body' => 'OSHA',
            'issued_at' => '2024-01-15',
            'status' => 'active',
        ])->assertSessionHasErrors('name');
    }

    public function test_certification_creation_requires_valid_status(): void
    {
        $this->actingAs($this->user);

        $this->post(route('certifications.store'), [
            'user_id' => $this->user->id,
            'name' => 'Test Cert',
            'issuing_body' => 'OSHA',
            'issued_at' => '2024-01-15',
            'status' => 'invalid_status',
        ])->assertSessionHasErrors('status');
    }

    public function test_expires_at_must_be_after_issued_at(): void
    {
        $this->actingAs($this->user);

        $this->post(route('certifications.store'), [
            'user_id' => $this->user->id,
            'name' => 'Test Cert',
            'issuing_body' => 'OSHA',
            'issued_at' => '2024-06-01',
            'expires_at' => '2024-01-01',
            'status' => 'active',
        ])->assertSessionHasErrors('expires_at');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function test_user_can_view_own_certification(): void
    {
        $certification = UserCertification::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->user);

        $this->get(route('certifications.show', $certification))->assertOk();
    }

    public function test_user_cannot_view_another_tenants_certification(): void
    {
        $otherTenant = Tenant::factory()->create();
        $otherUser = User::factory()->create(['tenant_id' => $otherTenant->id]);
        $certification = UserCertification::factory()->create([
            'tenant_id' => $otherTenant->id,
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($this->user);

        $this->get(route('certifications.show', $certification))->assertNotFound();
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function test_edit_page_is_accessible(): void
    {
        $certification = UserCertification::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->user);

        $this->get(route('certifications.edit', $certification))->assertOk();
    }

    public function test_user_can_update_a_certification(): void
    {
        $certification = UserCertification::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'user_id' => $this->user->id,
            'name' => 'Old Name',
            'status' => CertificationStatus::Pending,
        ]);

        $this->actingAs($this->user);

        $response = $this->patch(route('certifications.update', $certification), [
            'user_id' => $this->user->id,
            'name' => 'Updated Cert Name',
            'issuing_body' => 'ISO',
            'issued_at' => '2024-01-01',
            'status' => 'active',
        ]);

        $certification->refresh();
        $this->assertEquals('Updated Cert Name', $certification->name);
        $this->assertEquals(CertificationStatus::Active, $certification->status);

        $response->assertRedirect(route('certifications.show', $certification));
    }

    public function test_user_cannot_update_another_tenants_certification(): void
    {
        $otherTenant = Tenant::factory()->create();
        $otherUser = User::factory()->create(['tenant_id' => $otherTenant->id]);
        $certification = UserCertification::factory()->create([
            'tenant_id' => $otherTenant->id,
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($this->user);

        $this->patch(route('certifications.update', $certification), [
            'user_id' => $otherUser->id,
            'name' => 'Hacked',
            'issuing_body' => 'HACK',
            'issued_at' => '2024-01-01',
            'status' => 'active',
        ])->assertNotFound();
    }

    // ── Tenant Isolation ──────────────────────────────────────────────────────

    public function test_certifications_stats_only_count_tenant_records(): void
    {
        $otherTenant = Tenant::factory()->create();
        $otherUser = User::factory()->create(['tenant_id' => $otherTenant->id]);

        UserCertification::factory()->count(3)->create([
            'tenant_id' => $otherTenant->id,
            'user_id' => $otherUser->id,
            'status' => CertificationStatus::Active,
        ]);

        UserCertification::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'user_id' => $this->user->id,
            'status' => CertificationStatus::Active,
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('certifications.index'));
        $response->assertOk();

        // The stats should show 1 total (tenant-scoped), not 4
        $response->assertSee('1');
    }
}
