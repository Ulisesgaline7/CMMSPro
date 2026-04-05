<?php

namespace App\Http\Controllers;

use App\Enums\PermitRiskLevel;
use App\Enums\PermitStatus;
use App\Enums\PermitType;
use App\Models\PermitToWork;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PermitToWorkController extends Controller
{
    public function index(Request $request): View
    {
        $query = PermitToWork::with(['requester', 'workOrder'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', "%{$request->search}%")
                  ->orWhere('title', 'like', "%{$request->search}%");
            });
        }

        $permits = $query->paginate(20)->withQueryString();

        $stats = [
            'total'            => PermitToWork::count(),
            'pending_approval' => PermitToWork::where('status', PermitStatus::PendingApproval)->count(),
            'active'           => PermitToWork::where('status', PermitStatus::Active)->count(),
            'expiring_soon'    => PermitToWork::where('status', PermitStatus::Active)
                ->where('expires_at', '<=', now()->addHours(2))
                ->where('expires_at', '>', now())
                ->count(),
        ];

        return view('permits.index', [
            'permits'    => $permits,
            'stats'      => $stats,
            'types'      => PermitType::cases(),
            'statuses'   => PermitStatus::cases(),
            'filters'    => $request->only(['status', 'type', 'search']),
        ]);
    }

    public function create(Request $request): View
    {
        $workOrders = WorkOrder::with('asset')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderByDesc('created_at')
            ->get();

        $selectedWorkOrder = $request->filled('work_order_id')
            ? WorkOrder::find($request->work_order_id)
            : null;

        return view('permits.create', [
            'workOrders'          => $workOrders,
            'selectedWorkOrder'   => $selectedWorkOrder,
            'types'               => PermitType::cases(),
            'riskLevels'          => PermitRiskLevel::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'type'           => 'required|in:' . implode(',', array_column(PermitType::cases(), 'value')),
            'risk_level'     => 'required|in:' . implode(',', array_column(PermitRiskLevel::cases(), 'value')),
            'work_order_id'  => 'nullable|exists:work_orders,id',
            'description'    => 'nullable|string',
            'lockout_points' => 'nullable|string',
            'required_ppe'   => 'nullable|string',
            'precautions'    => 'nullable|string',
            'expires_at'     => 'nullable|date|after:now',
        ]);

        $code = $this->generateCode();

        PermitToWork::create(array_merge($validated, [
            'tenant_id'    => Auth::user()->tenant_id,
            'requested_by' => Auth::id(),
            'status'       => PermitStatus::Draft,
            'code'         => $code,
        ]));

        return redirect()->route('permits.index')
            ->with('success', "Permiso {$code} creado correctamente.");
    }

    public function show(PermitToWork $permit): View
    {
        abort_unless($permit->tenant_id === Auth::user()->tenant_id, 404);

        $permit->load(['requester', 'approver', 'workOrder.asset']);

        return view('permits.show', compact('permit'));
    }

    public function edit(PermitToWork $permit): View
    {
        abort_unless($permit->tenant_id === Auth::user()->tenant_id, 404);
        abort_unless(in_array($permit->status, [PermitStatus::Draft, PermitStatus::PendingApproval]), 403);

        $workOrders = WorkOrder::with('asset')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderByDesc('created_at')
            ->get();

        return view('permits.edit', [
            'permit'     => $permit,
            'workOrders' => $workOrders,
            'types'      => PermitType::cases(),
            'riskLevels' => PermitRiskLevel::cases(),
        ]);
    }

    public function update(Request $request, PermitToWork $permit): RedirectResponse
    {
        abort_unless($permit->tenant_id === Auth::user()->tenant_id, 404);
        abort_unless(in_array($permit->status, [PermitStatus::Draft, PermitStatus::PendingApproval]), 403);

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'type'           => 'required|in:' . implode(',', array_column(PermitType::cases(), 'value')),
            'risk_level'     => 'required|in:' . implode(',', array_column(PermitRiskLevel::cases(), 'value')),
            'work_order_id'  => 'nullable|exists:work_orders,id',
            'description'    => 'nullable|string',
            'lockout_points' => 'nullable|string',
            'required_ppe'   => 'nullable|string',
            'precautions'    => 'nullable|string',
            'expires_at'     => 'nullable|date|after:now',
        ]);

        $permit->update($validated);

        return redirect()->route('permits.show', $permit)
            ->with('success', 'Permiso actualizado correctamente.');
    }

    public function submit(PermitToWork $permit): RedirectResponse
    {
        abort_unless($permit->tenant_id === Auth::user()->tenant_id, 404);
        abort_unless($permit->status === PermitStatus::Draft, 403);

        $permit->update(['status' => PermitStatus::PendingApproval]);

        return redirect()->route('permits.show', $permit)
            ->with('success', 'Permiso enviado para aprobación.');
    }

    public function approve(Request $request, PermitToWork $permit): RedirectResponse
    {
        abort_unless($permit->tenant_id === Auth::user()->tenant_id, 404);
        abort_unless($permit->status === PermitStatus::PendingApproval, 403);

        $permit->update([
            'status'      => PermitStatus::Approved,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('permits.show', $permit)
            ->with('success', 'Permiso aprobado correctamente.');
    }

    public function reject(Request $request, PermitToWork $permit): RedirectResponse
    {
        abort_unless($permit->tenant_id === Auth::user()->tenant_id, 404);
        abort_unless($permit->status === PermitStatus::PendingApproval, 403);

        $request->validate(['rejection_reason' => 'required|string']);

        $permit->update([
            'status'           => PermitStatus::Cancelled,
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('permits.show', $permit)
            ->with('success', 'Permiso rechazado.');
    }

    public function activate(PermitToWork $permit): RedirectResponse
    {
        abort_unless($permit->tenant_id === Auth::user()->tenant_id, 404);
        abort_unless($permit->status === PermitStatus::Approved, 403);

        $permit->update([
            'status'       => PermitStatus::Active,
            'activated_at' => now(),
        ]);

        return redirect()->route('permits.show', $permit)
            ->with('success', 'Permiso activado. Puede proceder con el trabajo.');
    }

    public function close(Request $request, PermitToWork $permit): RedirectResponse
    {
        abort_unless($permit->tenant_id === Auth::user()->tenant_id, 404);
        abort_unless($permit->status === PermitStatus::Active, 403);

        $permit->update([
            'status'        => PermitStatus::Closed,
            'closed_at'     => now(),
            'closure_notes' => $request->closure_notes,
        ]);

        return redirect()->route('permits.show', $permit)
            ->with('success', 'Permiso cerrado correctamente.');
    }

    private function generateCode(): string
    {
        $count = PermitToWork::withoutGlobalScopes()->count() + 1;

        return 'PTW-' . str_pad((string) $count, 6, '0', STR_PAD_LEFT);
    }
}
