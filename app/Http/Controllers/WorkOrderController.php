<?php

namespace App\Http\Controllers;

use App\Enums\WorkOrderActivityAction;
use App\Enums\WorkOrderStatus;
use App\Http\Requests\AddWorkOrderNoteRequest;
use App\Http\Requests\CompleteChecklistItemRequest;
use App\Http\Requests\StoreWorkOrderRequest;
use App\Http\Requests\UpdateWorkOrderRequest;
use App\Http\Requests\UpdateWorkOrderStatusRequest;
use App\Models\Asset;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderActivity;
use App\Models\WorkOrderChecklistItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WorkOrderController extends Controller
{
    public function index(Request $request): View
    {
        $workOrders = WorkOrder::with([
            'asset:id,name,code,criticality,location_id',
            'asset.location:id,name,type',
            'assignedTo:id,name',
        ])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                });
            })
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->priority, fn ($q, $p) => $q->where('priority', $p))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => WorkOrder::count(),
            'pending' => WorkOrder::where('status', WorkOrderStatus::Pending)->count(),
            'in_progress' => WorkOrder::where('status', WorkOrderStatus::InProgress)->count(),
            'completed_today' => WorkOrder::where('status', WorkOrderStatus::Completed)
                ->whereDate('completed_at', today())
                ->count(),
            'overdue' => WorkOrder::whereIn('status', [WorkOrderStatus::Pending, WorkOrderStatus::InProgress])
                ->where('due_date', '<', now())
                ->count(),
        ];

        return view('work-orders.index', [
            'workOrders' => $workOrders,
            'filters' => $request->only(['search', 'status', 'type', 'priority']),
            'stats' => $stats,
        ]);
    }

    public function create(): View
    {
        $assets = Asset::select('id', 'name', 'code', 'location_id')
            ->with('location:id,name')
            ->orderBy('name')
            ->get();

        $technicians = User::select('id', 'name', 'employee_code')
            ->whereIn('role', ['admin', 'supervisor', 'technician'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('work-orders.create', [
            'assets'      => $assets,
            'technicians' => $technicians,
        ]);
    }

    public function store(StoreWorkOrderRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['tenant_id'] = Auth::user()->tenant_id;
        $data['requested_by'] = Auth::id();
        $data['status'] = WorkOrderStatus::Pending;
        $data['code'] = $this->generateCode();

        $workOrder = WorkOrder::create($data);

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Orden de trabajo creada correctamente.');
    }

    private function generateCode(): string
    {
        $last = WorkOrder::withoutGlobalScopes()
            ->where('code', 'like', 'WO-%')
            ->orderByDesc('id')
            ->value('code');

        $next = $last ? ((int) substr($last, 3)) + 1 : 1;

        return 'WO-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    public function show(WorkOrder $workOrder): View
    {
        $workOrder->load([
            'asset.location.parent',
            'asset.category:id,name,code',
            'assignedTo:id,name,employee_code',
            'requestedBy:id,name',
            'approvedBy:id,name',
            'checklists.items.completedBy:id,name',
            'parts.part:id,name,unit',
            'activities' => fn ($q) => $q->with('user:id,name')->orderBy('created_at'),
        ]);

        return view('work-orders.show', [
            'workOrder' => $workOrder,
        ]);
    }

    public function edit(WorkOrder $workOrder): View
    {
        $assets = Asset::select('id', 'name', 'code', 'location_id')
            ->with('location:id,name')
            ->orderBy('name')
            ->get();

        $technicians = User::select('id', 'name', 'employee_code')
            ->whereIn('role', ['admin', 'supervisor', 'technician'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('work-orders.edit', [
            'workOrder'   => $workOrder,
            'assets'      => $assets,
            'technicians' => $technicians,
        ]);
    }

    public function update(UpdateWorkOrderRequest $request, WorkOrder $workOrder): RedirectResponse
    {
        $workOrder->update($request->validated());

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'Orden de trabajo actualizada correctamente.');
    }

    public function completeChecklistItem(
        CompleteChecklistItemRequest $request,
        WorkOrder $workOrder,
    ): RedirectResponse {
        $item = WorkOrderChecklistItem::whereHas(
            'checklist',
            fn ($q) => $q->where('work_order_id', $workOrder->id),
        )->findOrFail($request->checklist_item_id);

        abort_if($item->is_completed, 422, 'El ítem ya está completado.');

        $item->update([
            'is_completed' => true,
            'completed_by' => Auth::id(),
            'completed_at' => now(),
        ]);

        WorkOrderActivity::create([
            'work_order_id' => $workOrder->id,
            'user_id' => Auth::id(),
            'action' => WorkOrderActivityAction::ChecklistItemCompleted,
            'metadata' => ['item_description' => $item->description],
            'created_at' => now(),
        ]);

        return back();
    }

    public function addNote(AddWorkOrderNoteRequest $request, WorkOrder $workOrder): RedirectResponse
    {
        WorkOrderActivity::create([
            'work_order_id' => $workOrder->id,
            'user_id' => Auth::id(),
            'action' => WorkOrderActivityAction::NoteAdded,
            'notes' => $request->validated('notes'),
            'created_at' => now(),
        ]);

        return back();
    }

    public function updateStatus(
        UpdateWorkOrderStatusRequest $request,
        WorkOrder $workOrder,
    ): RedirectResponse {
        $newStatus = WorkOrderStatus::from($request->validated('status'));

        abort_unless(
            $workOrder->canTransitionTo($newStatus),
            422,
            "La transición de '{$workOrder->status->label()}' a '{$newStatus->label()}' no está permitida.",
        );

        $updates = ['status' => $newStatus];

        if ($newStatus === WorkOrderStatus::InProgress && $workOrder->started_at === null) {
            $updates['started_at'] = now();
        }

        if ($newStatus === WorkOrderStatus::Completed) {
            $updates['completed_at'] = now();

            if ($workOrder->started_at) {
                $updates['actual_duration'] = (int) $workOrder->started_at->diffInMinutes(now());
            }
        }

        $workOrder->update($updates);

        return back();
    }
}
