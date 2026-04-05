<?php

namespace App\Http\Controllers\Iot;

use App\Enums\SensorStatus;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Sensor;
use App\Models\SensorAlert;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $activeSensors = Sensor::where('status', SensorStatus::Active)->count();
        $disconnectedSensors = Sensor::where('status', SensorStatus::Disconnected)->count();
        $faultSensors = Sensor::where('status', SensorStatus::Fault)->count();

        $alertingSensors = Sensor::whereHas('alerts', fn ($q) => $q->where('is_active', true))->count();

        $recentAlerts = SensorAlert::with('sensor.asset')
            ->where('is_active', true)
            ->latest('triggered_at')
            ->limit(10)
            ->get();

        $sensors = Sensor::with('asset:id,name,code')
            ->orderByDesc('last_reading_at')
            ->limit(20)
            ->get();

        return view('iot.dashboard', [
            'activeSensors' => $activeSensors,
            'alertingSensors' => $alertingSensors,
            'disconnectedSensors' => $disconnectedSensors,
            'faultSensors' => $faultSensors,
            'recentAlerts' => $recentAlerts,
            'sensors' => $sensors,
        ]);
    }
}
