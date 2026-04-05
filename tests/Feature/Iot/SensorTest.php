<?php

namespace Tests\Feature\Iot;

use App\Enums\SensorStatus;
use App\Enums\SensorType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Asset;
use App\Models\Sensor;
use App\Models\SensorAlert;
use App\Models\SensorReading;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SensorTest extends TestCase
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

    public function test_admin_can_view_iot_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/iot');

        $response->assertStatus(200);
    }

    public function test_admin_can_list_sensors(): void
    {
        $asset = Asset::factory()->create(['tenant_id' => $this->tenant->id]);
        Sensor::factory()->forAsset($asset)->count(3)->create();

        $response = $this->actingAs($this->admin)->get('/iot/sensors');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_sensor(): void
    {
        $asset = Asset::factory()->create(['tenant_id' => $this->tenant->id]);

        $response = $this->actingAs($this->admin)->post('/iot/sensors', [
            'asset_id' => $asset->id,
            'code' => 'SEN-TEST-001',
            'name' => 'Test Temperature Sensor',
            'type' => SensorType::Temperature->value,
            'unit' => '°C',
            'min_threshold' => -10,
            'max_threshold' => 80,
            'sampling_interval_seconds' => 60,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sensors', [
            'tenant_id' => $this->tenant->id,
            'code' => 'SEN-TEST-001',
        ]);
    }

    public function test_reading_creates_database_record(): void
    {
        $asset = Asset::factory()->create(['tenant_id' => $this->tenant->id]);
        $sensor = Sensor::factory()->forAsset($asset)->withThresholds(0.0, 100.0)->create();

        $response = $this->actingAs($this->admin)
            ->post("/iot/sensors/{$sensor->id}/readings", [
                'value' => 50.0,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sensor_readings', [
            'sensor_id' => $sensor->id,
            'value' => 50.0,
        ]);
    }

    public function test_reading_above_max_threshold_creates_alert(): void
    {
        $asset = Asset::factory()->create(['tenant_id' => $this->tenant->id]);
        $sensor = Sensor::factory()->forAsset($asset)->withThresholds(0.0, 80.0)->create([
            'warning_threshold_high' => 70.0,
        ]);

        $this->actingAs($this->admin)
            ->post("/iot/sensors/{$sensor->id}/readings", [
                'value' => 95.0,
            ]);

        $this->assertDatabaseHas('sensor_alerts', [
            'sensor_id' => $sensor->id,
            'is_active' => 1,
        ]);
    }

    public function test_alert_can_be_acknowledged(): void
    {
        $asset = Asset::factory()->create(['tenant_id' => $this->tenant->id]);
        $sensor = Sensor::factory()->forAsset($asset)->create();
        $alert = SensorAlert::factory()->create([
            'sensor_id' => $sensor->id,
            'tenant_id' => $this->tenant->id,
            'is_active' => true,
            'triggered_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->post("/iot/alerts/{$alert->id}/acknowledge");

        $response->assertRedirect();
        $this->assertDatabaseHas('sensor_alerts', [
            'id' => $alert->id,
            'acknowledged_by' => $this->admin->id,
        ]);
    }

    public function test_alert_can_be_resolved(): void
    {
        $asset = Asset::factory()->create(['tenant_id' => $this->tenant->id]);
        $sensor = Sensor::factory()->forAsset($asset)->create();
        $alert = SensorAlert::factory()->create([
            'sensor_id' => $sensor->id,
            'tenant_id' => $this->tenant->id,
            'is_active' => true,
            'triggered_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->post("/iot/alerts/{$alert->id}/resolve");

        $response->assertRedirect();
        $this->assertDatabaseHas('sensor_alerts', [
            'id' => $alert->id,
            'is_active' => 0,
        ]);
    }
}
