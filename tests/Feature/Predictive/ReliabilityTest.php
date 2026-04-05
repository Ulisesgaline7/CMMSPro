<?php

namespace Tests\Feature\Predictive;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\WorkOrderStatus;
use App\Enums\WorkOrderType;
use App\Jobs\CalculateAssetReliability;
use App\Models\Asset;
use App\Models\AssetReliabilityMetric;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ReliabilityTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->admin = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'role' => UserRole::Admin,
            'status' => UserStatus::Active,
        ]);
    }

    public function test_dashboard_is_accessible(): void
    {
        $response = $this->actingAs($this->admin)->get('/predictive');

        $response->assertStatus(200);
    }

    public function test_asset_analysis_page_loads(): void
    {
        $asset = Asset::factory()->create(['tenant_id' => $this->tenant->id]);

        $response = $this->actingAs($this->admin)->get("/predictive/assets/{$asset->id}");

        $response->assertStatus(200);
    }

    public function test_recalculate_dispatches_job(): void
    {
        Queue::fake();

        $asset = Asset::factory()->create(['tenant_id' => $this->tenant->id]);

        $this->actingAs($this->admin)
            ->post("/predictive/assets/{$asset->id}/recalculate");

        Queue::assertPushed(CalculateAssetReliability::class, function ($job) use ($asset) {
            return $job->asset->id === $asset->id;
        });
    }

    public function test_calculate_reliability_job_creates_metric(): void
    {
        $asset = Asset::factory()->create(['tenant_id' => $this->tenant->id]);

        // Create some corrective work orders
        WorkOrder::factory()->count(3)->create([
            'asset_id' => $asset->id,
            'tenant_id' => $this->tenant->id,
            'type' => WorkOrderType::Corrective,
            'status' => WorkOrderStatus::Completed,
            'actual_duration' => 240, // 4 hours each
            'completed_at' => now()->subDays(rand(1, 300)),
        ]);

        $job = new CalculateAssetReliability($asset);
        $job->handle();

        $this->assertDatabaseHas('asset_reliability_metrics', [
            'asset_id' => $asset->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $metric = AssetReliabilityMetric::where('asset_id', $asset->id)->first();
        $this->assertNotNull($metric);
        $this->assertEquals(3, $metric->corrective_count);
        $this->assertNotNull($metric->mtbf_hours);
        $this->assertNotNull($metric->availability_percent);
    }

    public function test_report_page_is_accessible(): void
    {
        $response = $this->actingAs($this->admin)->get('/predictive/report');

        $response->assertStatus(200);
    }
}
