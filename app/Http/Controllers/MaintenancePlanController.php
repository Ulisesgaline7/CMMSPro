<?php

namespace App\Http\Controllers;

use App\Enums\MaintenancePlanFrequency;
use App\Enums\WorkOrderPriority;
use App\Enums\WorkOrderType;
use App\Http\Requests\StoreMaintenancePlanRequest;
use App\Http\Requests\UpdateMaintenancePlanRequest;
use App\Models\Asset;
use App\Models\MaintenancePlan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MaintenancePlanController extends Controller
{
    public function index(Request $request): View
    {
        $plans = MaintenancePlan::with(['asset:id,name,code', 'assignedTo:id,name'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when($request->status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->when($request->frequency, fn ($q, $f) => $q->where('frequency', $f))
            ->orderBy('next_execution_date')
            ->paginate(20)
            ->withQueryString();

        return view('maintenance-plans.index', [
            'plans' => $plans,
            'filters' => $request->only(['search', 'status', 'frequency']),
            'frequencies' => MaintenancePlanFrequency::cases(),
        ]);
    }

    public function create(): View
    {
        $assets = Asset::select('id', 'name', 'code')->orderBy('name')->get();
        $technicians = User::select('id', 'name', 'employee_code')
            ->whereIn('role', ['admin', 'supervisor', 'technician'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('maintenance-plans.create', [
            'assets' => $assets,
            'technicians' => $technicians,
            'frequencies' => MaintenancePlanFrequency::cases(),
            'priorities' => WorkOrderPriority::cases(),
        ]);
    }

    public function store(StoreMaintenancePlanRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['tenant_id'] = Auth::user()->tenant_id;
        $data['is_active'] = $request->boolean('is_active', true);
        $data['next_execution_date'] ??= $data['start_date'];

        $plan = MaintenancePlan::create($data);

        return redirect()->route('maintenance-plans.show', $plan)
            ->with('success', 'Plan de mantenimiento creado correctamente.');
    }

    public function show(MaintenancePlan $maintenancePlan): View
    {
        $maintenancePlan->load(['asset.location', 'assignedTo:id,name,employee_code']);

        return view('maintenance-plans.show', ['plan' => $maintenancePlan]);
    }

    public function edit(MaintenancePlan $maintenancePlan): View
    {
        $assets = Asset::select('id', 'name', 'code')->orderBy('name')->get();
        $technicians = User::select('id', 'name', 'employee_code')
            ->whereIn('role', ['admin', 'supervisor', 'technician'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('maintenance-plans.edit', [
            'plan' => $maintenancePlan,
            'assets' => $assets,
            'technicians' => $technicians,
            'frequencies' => MaintenancePlanFrequency::cases(),
            'priorities' => WorkOrderPriority::cases(),
        ]);
    }

    public function update(UpdateMaintenancePlanRequest $request, MaintenancePlan $maintenancePlan): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $maintenancePlan->update($data);

        return redirect()->route('maintenance-plans.show', $maintenancePlan)
            ->with('success', 'Plan de mantenimiento actualizado correctamente.');
    }
}
