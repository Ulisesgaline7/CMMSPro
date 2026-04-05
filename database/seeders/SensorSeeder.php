<?php

namespace Database\Seeders;

use App\Enums\AlertSeverity;
use App\Enums\SensorStatus;
use App\Enums\SensorType;
use App\Models\Asset;
use App\Models\Sensor;
use App\Models\SensorAlert;
use App\Models\SensorReading;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class SensorSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::withoutGlobalScopes()->where('slug', 'metalurgica-norte')->first();

        if (! $tenant) {
            return;
        }

        $assets = Asset::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->limit(5)
            ->get();

        if ($assets->isEmpty()) {
            return;
        }

        $sensorConfigs = [
            ['type' => SensorType::Temperature, 'unit' => '°C', 'min' => -10.0, 'max' => 85.0, 'warn_low' => 10.0, 'warn_high' => 70.0],
            ['type' => SensorType::Vibration, 'unit' => 'mm/s', 'min' => 0.0, 'max' => 50.0, 'warn_low' => null, 'warn_high' => 30.0],
            ['type' => SensorType::Pressure, 'unit' => 'bar', 'min' => 0.5, 'max' => 10.0, 'warn_low' => 1.0, 'warn_high' => 8.0],
            ['type' => SensorType::Current, 'unit' => 'A', 'min' => 0.0, 'max' => 200.0, 'warn_low' => 5.0, 'warn_high' => 150.0],
            ['type' => SensorType::Humidity, 'unit' => '%', 'min' => 10.0, 'max' => 95.0, 'warn_low' => 20.0, 'warn_high' => 85.0],
        ];

        $sensorIndex = 0;
        foreach ($assets as $asset) {
            $config = $sensorConfigs[$sensorIndex % count($sensorConfigs)];
            $sensorIndex++;

            $sensor = Sensor::create([
                'tenant_id' => $tenant->id,
                'asset_id' => $asset->id,
                'code' => 'SEN-'.str_pad($sensorIndex, 3, '0', STR_PAD_LEFT),
                'name' => $config['type']->label().' — '.$asset->name,
                'type' => $config['type'],
                'unit' => $config['unit'],
                'status' => $sensorIndex === 2 ? SensorStatus::Fault : SensorStatus::Active,
                'min_threshold' => $config['min'],
                'max_threshold' => $config['max'],
                'warning_threshold_low' => $config['warn_low'],
                'warning_threshold_high' => $config['warn_high'],
                'sampling_interval_seconds' => 60,
            ]);

            // Create 48 readings (last 48 hours)
            $midValue = ($config['max'] + ($config['min'] ?? 0)) / 2;
            for ($i = 48; $i >= 0; $i--) {
                $value = $midValue + (rand(-100, 100) / 100) * ($config['max'] - ($config['min'] ?? 0)) * 0.15;
                $value = round(max((float) ($config['min'] ?? 0), min((float) $config['max'], $value)), 4);

                SensorReading::create([
                    'sensor_id' => $sensor->id,
                    'tenant_id' => $tenant->id,
                    'value' => $value,
                    'quality' => 'good',
                    'read_at' => now()->subHours($i),
                ]);
            }

            // Update sensor with latest reading
            $sensor->update([
                'last_reading_value' => $midValue,
                'last_reading_at' => now(),
            ]);

            // Create a sample alert for first sensor
            if ($sensorIndex === 1) {
                SensorAlert::create([
                    'sensor_id' => $sensor->id,
                    'tenant_id' => $tenant->id,
                    'type' => 'warning_high',
                    'severity' => AlertSeverity::Warning,
                    'message' => "Temperatura alta detectada: {$midValue} {$config['unit']} supera umbral de advertencia.",
                    'value' => $midValue,
                    'threshold' => $config['warn_high'],
                    'triggered_at' => now()->subHours(2),
                    'is_active' => true,
                ]);
            }
        }
    }
}
