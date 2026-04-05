<?php

namespace App\Http\Controllers\Iot;

use App\Http\Controllers\Controller;
use App\Models\SensorAlert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SensorAlertController extends Controller
{
    public function index(): View
    {
        $alerts = SensorAlert::with('sensor.asset')
            ->where('is_active', true)
            ->latest('triggered_at')
            ->paginate(25);

        return view('iot.alerts.index', [
            'alerts' => $alerts,
        ]);
    }

    public function acknowledge(SensorAlert $alert): RedirectResponse
    {
        $alert->update([
            'acknowledged_at' => now(),
            'acknowledged_by' => Auth::id(),
        ]);

        return back()->with('success', 'Alerta reconocida.');
    }

    public function resolve(SensorAlert $alert): RedirectResponse
    {
        $alert->update([
            'resolved_at' => now(),
            'is_active' => false,
            'acknowledged_at' => $alert->acknowledged_at ?? now(),
            'acknowledged_by' => $alert->acknowledged_by ?? Auth::id(),
        ]);

        return back()->with('success', 'Alerta resuelta.');
    }
}
