<?php

namespace App\Http\Controllers;

use App\Enums\CorrectiveActionStatus;
use App\Http\Requests\StoreCorrectiveActionRequest;
use App\Http\Requests\UpdateCorrectiveActionRequest;
use App\Models\CorrectiveAction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CorrectiveActionController extends Controller
{
    public function index(Request $request): View
    {
        $correctiveActions = CorrectiveAction::with([
            'assignedTo:id,name',
            'finding:id,code,description,audit_id',
        ])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                });
            })
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => CorrectiveAction::count(),
            'open' => CorrectiveAction::where('status', CorrectiveActionStatus::Open)->count(),
            'in_progress' => CorrectiveAction::where('status', CorrectiveActionStatus::InProgress)->count(),
            'completed' => CorrectiveAction::whereIn('status', [
                CorrectiveActionStatus::Completed,
                CorrectiveActionStatus::Verified,
            ])->count(),
        ];

        return view('corrective-actions.index', [
            'correctiveActions' => $correctiveActions,
            'filters' => $request->only(['search', 'status', 'type']),
            'stats' => $stats,
        ]);
    }

    public function create(Request $request): View
    {
        $users = User::select('id', 'name', 'employee_code')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('corrective-actions.create', [
            'users' => $users,
            'findingId' => $request->finding_id,
        ]);
    }

    public function store(StoreCorrectiveActionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['tenant_id'] = Auth::user()->tenant_id;
        $data['created_by'] = Auth::id();
        $data['status'] = CorrectiveActionStatus::Open;
        $data['code'] = $this->generateCode();

        $correctiveAction = CorrectiveAction::create($data);

        return redirect()->route('corrective-actions.show', $correctiveAction)
            ->with('success', 'Acción CAPA creada correctamente.');
    }

    public function show(CorrectiveAction $correctiveAction): View
    {
        $correctiveAction->load([
            'assignedTo:id,name',
            'createdBy:id,name',
            'finding.audit:id,code,title',
            'workOrder:id,code,title',
        ]);

        return view('corrective-actions.show', [
            'correctiveAction' => $correctiveAction,
        ]);
    }

    public function edit(CorrectiveAction $correctiveAction): View
    {
        $users = User::select('id', 'name', 'employee_code')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('corrective-actions.edit', [
            'correctiveAction' => $correctiveAction,
            'users' => $users,
        ]);
    }

    public function update(UpdateCorrectiveActionRequest $request, CorrectiveAction $correctiveAction): RedirectResponse
    {
        $data = $request->validated();

        $newStatus = CorrectiveActionStatus::from($data['status']);

        if ($newStatus === CorrectiveActionStatus::Completed && $correctiveAction->completed_at === null) {
            $data['completed_at'] = now();
        }

        if ($newStatus === CorrectiveActionStatus::Verified && $correctiveAction->verified_at === null) {
            $data['verified_at'] = now();
        }

        $correctiveAction->update($data);

        return redirect()->route('corrective-actions.show', $correctiveAction)
            ->with('success', 'Acción CAPA actualizada correctamente.');
    }

    private function generateCode(): string
    {
        $last = CorrectiveAction::withoutGlobalScopes()
            ->where('code', 'like', 'CAP-%')
            ->orderByDesc('id')
            ->value('code');

        $next = $last ? ((int) substr($last, 4)) + 1 : 1;

        return 'CAP-'.str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
