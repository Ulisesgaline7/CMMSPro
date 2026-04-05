<?php

namespace App\Http\Controllers\Iot;

use App\Enums\AlertSeverity;
use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Models\SensorAlert;
use App\Models\SensorReading;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SensorReadingController extends Controller
{
    public function store(Request $request, Sensor $sensor): RedirectResponse
    {
        $data = $request->validate([
            'value' => ['required', 'numeric'],
            'quality' => ['nullable', 'string', 'max:20'],
        ]);

        $reading = SensorReading::create([
            'sensor_id' => $sensor->id,
            'tenant_id' => $sensor->tenant_id,
            'value' => $data['value'],
            'quality' => $data['quality'] ?? 'good',
            'read_at' => now(),
        ]);

        $sensor->update([
            'last_reading_value' => $data['value'],
            'last_reading_at' => now(),
        ]);

        $this->checkThresholds($sensor, (float) $data['value']);

        return back()->with('success', 'Lectura registrada.');
    }

    private function checkThresholds(Sensor $sensor, float $value): void
    {
        $type = null;
        $severity = AlertSeverity::Warning;
        $threshold = null;
        $message = null;

        if ($sensor->max_threshold !== null && $value > (float) $sensor->max_threshold) {
            $type = 'max_exceeded';
            $severity = AlertSeverity::Critical;
            $threshold = (float) $sensor->max_threshold;
            $message = "Valor {$value} {$sensor->unit} supera umbral máximo crítico ({$threshold}).";
        } elseif ($sensor->min_threshold !== null && $value < (float) $sensor->min_threshold) {
            $type = 'min_below';
            $severity = AlertSeverity::Critical;
            $threshold = (float) $sensor->min_threshold;
            $message = "Valor {$value} {$sensor->unit} está por debajo del umbral mínimo crítico ({$threshold}).";
        } elseif ($sensor->warning_threshold_high !== null && $value > (float) $sensor->warning_threshold_high) {
            $type = 'warning_high';
            $severity = AlertSeverity::Warning;
            $threshold = (float) $sensor->warning_threshold_high;
            $message = "Valor {$value} {$sensor->unit} supera umbral de advertencia alto ({$threshold}).";
        } elseif ($sensor->warning_threshold_low !== null && $value < (float) $sensor->warning_threshold_low) {
            $type = 'warning_low';
            $severity = AlertSeverity::Warning;
            $threshold = (float) $sensor->warning_threshold_low;
            $message = "Valor {$value} {$sensor->unit} está por debajo del umbral de advertencia bajo ({$threshold}).";
        }

        if ($type !== null) {
            SensorAlert::create([
                'sensor_id' => $sensor->id,
                'tenant_id' => $sensor->tenant_id,
                'type' => $type,
                'severity' => $severity,
                'message' => $message,
                'value' => $value,
                'threshold' => $threshold,
                'triggered_at' => now(),
                'is_active' => true,
            ]);
        }
    }
}
