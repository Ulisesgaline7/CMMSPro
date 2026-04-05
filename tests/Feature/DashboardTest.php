<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_dashboard_shows_reliability_metrics()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('reliability');

        $reliability = $response->viewData('reliability');

        $this->assertArrayHasKey('mtbf', $reliability);
        $this->assertArrayHasKey('mttr', $reliability);
        $this->assertArrayHasKey('oee', $reliability);
        $this->assertArrayHasKey('corrective_count_90d', $reliability);
        $this->assertArrayHasKey('completed_corrective_avg_duration', $reliability);

        $this->assertIsFloat($reliability['mtbf']);
        $this->assertIsFloat($reliability['mttr']);
        $this->assertIsFloat($reliability['oee']);
    }
}
