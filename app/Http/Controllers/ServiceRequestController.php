<?php

namespace App\Http\Controllers;

use App\Enums\ServiceRequestStatus;
use App\Http\Requests\StoreServiceRequestRequest;
use App\Http\Requests\UpdateServiceRequestRequest;
use App\Models\Asset;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ServiceRequestController extends Controller
{
    public function index(Request $request): View
    {
        $serviceRequests = ServiceRequest::with([
            'requestedBy:id,name',
            'assignedTo:id,name',
            'asset:id,name,code',
        ])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                });
            })
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->category, fn ($q, $c) => $q->where('category', $c))
            ->when($request->priority, fn ($q, $p) => $q->where('priority', $p))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total'       => ServiceRequest::count(),
            'open'        => ServiceRequest::where('status', ServiceRequestStatus::Open)->count(),
            'in_progress' => ServiceRequest::where('status', ServiceRequestStatus::InProgress)->count(),
            'sla_at_risk' => ServiceRequest::whereNotIn('status', [ServiceRequestStatus::Resolved->value, ServiceRequestStatus::Closed->value])
                ->where('sla_deadline', '<', now()->addHours(2))
                ->whereNotNull('sla_deadline')
                ->count(),
        ];

        return view('service-requests.index', [
            'serviceRequests' => $serviceRequests,
            'stats'           => $stats,
            'filters'         => $request->only(['search', 'status', 'category', 'priority']),
        ]);
    }

    public function create(): View
    {
        $technicians = User::select('id', 'name', 'employee_code')
            ->whereIn('role', ['admin', 'supervisor', 'technician'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $assets = Asset::select('id', 'name', 'code')
            ->orderBy('name')
            ->get();

        return view('service-requests.create', [
            'technicians' => $technicians,
            'assets'      => $assets,
        ]);
    }

    public function store(StoreServiceRequestRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['tenant_id']    = Auth::user()->tenant_id;
        $data['requested_by'] = Auth::id();
        $data['status']       = ServiceRequestStatus::Open;
        $data['code']         = $this->generateCode();

        $priority = \App\Enums\ServiceRequestPriority::from($data['priority']);
        $data['sla_deadline'] = now()->addHours($priority->slaHours());

        $sr = ServiceRequest::create($data);

        return redirect()->route('service-requests.show', $sr)
            ->with('success', 'Solicitud de servicio creada correctamente.');
    }

    public function show(ServiceRequest $serviceRequest): View
    {
        $serviceRequest->load(['requestedBy', 'assignedTo', 'asset']);

        return view('service-requests.show', [
            'sr' => $serviceRequest,
        ]);
    }

    public function edit(ServiceRequest $serviceRequest): View
    {
        $technicians = User::select('id', 'name', 'employee_code')
            ->whereIn('role', ['admin', 'supervisor', 'technician'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $assets = Asset::select('id', 'name', 'code')
            ->orderBy('name')
            ->get();

        return view('service-requests.edit', [
            'sr'          => $serviceRequest,
            'technicians' => $technicians,
            'assets'      => $assets,
        ]);
    }

    public function update(UpdateServiceRequestRequest $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $data      = $request->validated();
        $newStatus = ServiceRequestStatus::from($data['status']);

        if ($newStatus === ServiceRequestStatus::Resolved && $serviceRequest->resolved_at === null) {
            $data['resolved_at']     = now();
            $data['resolution_time'] = (int) $serviceRequest->created_at->diffInMinutes(now());
            $data['sla_met']         = $serviceRequest->sla_deadline
                ? now()->lte($serviceRequest->sla_deadline)
                : null;
        }

        if ($newStatus === ServiceRequestStatus::Closed && $serviceRequest->closed_at === null) {
            $data['closed_at'] = now();
        }

        $serviceRequest->update($data);

        return redirect()->route('service-requests.show', $serviceRequest)
            ->with('success', 'Solicitud actualizada correctamente.');
    }

    private function generateCode(): string
    {
        $last = ServiceRequest::withoutGlobalScopes()
            ->where('code', 'like', 'SR-%')
            ->orderByDesc('id')
            ->value('code');

        $next = $last ? ((int) substr($last, 3)) + 1 : 1;

        return 'SR-'.str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
