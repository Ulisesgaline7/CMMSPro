<?php

namespace Tests\Feature;

use App\Enums\ShiftStatus;
use App\Enums\ShiftType;
use App\Models\Shift;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiftTest extends TestCase
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

    public function test_guests_are_redirected_from_shifts_index(): void
    {
        $this->get(route('shifts.index'))->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_shifts_create(): void
    {
        $this->get(route('shifts.create'))->assertRedirect(route('login'));
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_shifts_index(): void
    {
        $this->actingAs($this->user)
            ->get(route('shifts.index'))
            ->assertOk();
    }

    public function test_shifts_index_only_shows_tenant_shifts(): void
    {
        $otherTenant = Tenant::factory()->create();
        $otherUser = User::factory()->create(['tenant_id' => $otherTenant->id]);

        Shift::factory()->create([
            'tenant_id' => $otherTenant->id,
            'user_id'   => $otherUser->id,
            'name'      => 'Other Tenant Shift',
        ]);

        Shift::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'user_id'   => $this->user->id,
            'name'      => 'My Shift',
        ]);

        $this->actingAs($this->user)
            ->get(route('shifts.index'))
            ->assertOk()
            ->assertSee('My Shift')
            ->assertDontSee('Other Tenant Shift');
    }

    public function test_shifts_index_shows_stats(): void
    {
        Shift::factory()->scheduled()->create([
            'tenant_id' => $this->user->tenant_id,
            'user_id'   => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('shifts.index'))
            ->assertOk()
            ->assertViewHas('stats');
    }

    // ── Create / Store ────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_create_shift_form(): void
    {
        $this->actingAs($this->user)
            ->get(route('shifts.create'))
            ->assertOk()
            ->assertViewHas('technicians');
    }

    public function test_can_create_a_shift(): void
    {
        $tech = User::factory()->create([
            'tenant_id' => $this->user->tenant_id,
            'role'      => 'technician',
            'status'    => 'active',
        ]);

        $this->actingAs($this->user)
            ->post(route('shifts.store'), [
                'user_id'    => $tech->id,
                'name'       => 'Turno Mañana',
                'type'       => ShiftType::Morning->value,
                'date'       => now()->toDateString(),
                'start_time' => '06:00',
                'end_time'   => '14:00',
                'notes'      => null,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('shifts', [
            'tenant_id' => $this->user->tenant_id,
            'user_id'   => $tech->id,
            'name'      => 'Turno Mañana',
            'status'    => ShiftStatus::Scheduled->value,
        ]);
    }

    public function test_shift_requires_valid_type(): void
    {
        $tech = User::factory()->create(['tenant_id' => $this->user->tenant_id]);

        $this->actingAs($this->user)
            ->post(route('shifts.store'), [
                'user_id'    => $tech->id,
                'name'       => 'Test',
                'type'       => 'invalid_type',
                'date'       => now()->toDateString(),
                'start_time' => '06:00',
                'end_time'   => '14:00',
            ])
            ->assertSessionHasErrors('type');
    }

    public function test_shift_requires_user_id(): void
    {
        $this->actingAs($this->user)
            ->post(route('shifts.store'), [
                'name'       => 'Test',
                'type'       => ShiftType::Morning->value,
                'date'       => now()->toDateString(),
                'start_time' => '06:00',
                'end_time'   => '14:00',
            ])
            ->assertSessionHasErrors('user_id');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function test_can_view_shift_details(): void
    {
        $shift = Shift::factory()->morning()->create([
            'tenant_id' => $this->user->tenant_id,
            'user_id'   => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('shifts.show', $shift))
            ->assertOk()
            ->assertViewHas('shift');
    }

    public function test_cannot_view_another_tenants_shift(): void
    {
        $otherTenant = Tenant::factory()->create();
        $otherUser = User::factory()->create(['tenant_id' => $otherTenant->id]);
        $otherShift = Shift::factory()->create([
            'tenant_id' => $otherTenant->id,
            'user_id'   => $otherUser->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('shifts.show', $otherShift))
            ->assertNotFound();
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function test_can_edit_a_shift(): void
    {
        $shift = Shift::factory()->morning()->create([
            'tenant_id' => $this->user->tenant_id,
            'user_id'   => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('shifts.edit', $shift))
            ->assertOk()
            ->assertViewHas('shift');
    }

    public function test_can_update_a_shift(): void
    {
        $shift = Shift::factory()->morning()->scheduled()->create([
            'tenant_id' => $this->user->tenant_id,
            'user_id'   => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->patch(route('shifts.update', $shift), [
                'user_id'    => $this->user->id,
                'name'       => 'Turno Actualizado',
                'type'       => ShiftType::Night->value,
                'status'     => ShiftStatus::Active->value,
                'date'       => now()->toDateString(),
                'start_time' => '22:00',
                'end_time'   => '06:00',
            ])
            ->assertRedirect(route('shifts.show', $shift));

        $this->assertDatabaseHas('shifts', [
            'id'     => $shift->id,
            'name'   => 'Turno Actualizado',
            'status' => ShiftStatus::Active->value,
        ]);
    }

    // ── Filters ───────────────────────────────────────────────────────────────

    public function test_shifts_filtered_by_type(): void
    {
        Shift::factory()->morning()->create([
            'tenant_id' => $this->user->tenant_id,
            'user_id'   => $this->user->id,
        ]);
        Shift::factory()->night()->create([
            'tenant_id' => $this->user->tenant_id,
            'user_id'   => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('shifts.index', ['type' => 'morning']));

        $response->assertOk();
    }
}
