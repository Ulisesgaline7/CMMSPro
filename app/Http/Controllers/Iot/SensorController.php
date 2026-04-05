<?php

namespace App\Http\Controllers\Iot;

use App\Enums\SensorStatus;
use App\Enums\SensorType;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Sensor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SensorController extends Controller
{
    public function index(Request $request): View
    {
        $sensors = Sensor::with('asset:id,name,code')
            ->when($request->search, function ($q, $s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%");
            })
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('iot.sensors.index', [
            'sensors' => $sensors,
            'filters' => $request->only(['search', 'status']),
            'statuses' => SensorStatus::cases(),
        ]);
    }

    public function create(): View
    {
        $assets = Asset::select('id', 'name', 'code')->orderBy('name')->get();

        return view('iot.sensors.create', [
            'assets' => $assets,
            'types' => SensorType::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'asset_id' => ['required', 'integer'],
            'code' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string'],
            'unit' => ['required', 'string', 'max:30'],
            'min_threshold' => ['nullable', 'numeric'],
            'max_threshold' => ['nullable', 'numeric'],
            'warning_threshold_low' => ['nullable', 'numeric'],
            'warning_threshold_high' => ['nullable', 'numeric'],
            'sampling_interval_seconds' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['tenant_id'] = Auth::user()->tenant_id;
        $data['status'] = SensorStatus::Active;

        $sensor = Sensor::create($data);

        return redirect()->route('iot.sensors.show', $sensor)
            ->with('success', 'Sensor creado correctamente.');
    }

    public function show(Sensor $sensor): View
    {
        $sensor->load('asset:id,name,code');

        $recentReadings = $sensor->readings()
            ->orderByDesc('read_at')
            ->limit(50)
            ->get();

        $activeAlerts = $sensor->alerts()
            ->where('is_active', true)
            ->latest('triggered_at')
            ->get();

        return view('iot.sensors.show', [
            'sensor' => $sensor,
            'recentReadings' => $recentReadings,
            'activeAlerts' => $activeAlerts,
        ]);
    }

    public function edit(Sensor $sensor): View
    {
        $assets = Asset::select('id', 'name', 'code')->orderBy('name')->get();

        return view('iot.sensors.edit', [
            'sensor' => $sensor,
            'assets' => $assets,
            'types' => SensorType::cases(),
            'statuses' => SensorStatus::cases(),
        ]);
    }

    public function update(Request $request, Sensor $sensor): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string'],
            'unit' => ['required', 'string', 'max:30'],
            'status' => ['required', 'string'],
            'min_threshold' => ['nullable', 'numeric'],
            'max_threshold' => ['nullable', 'numeric'],
            'warning_threshold_low' => ['nullable', 'numeric'],
            'warning_threshold_high' => ['nullable', 'numeric'],
            'sampling_interval_seconds' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $sensor->update($data);

        return redirect()->route('iot.sensors.show', $sensor)
            ->with('success', 'Sensor actualizado correctamente.');
    }
}
