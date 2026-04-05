<?php

namespace App\Http\Controllers;

use App\Enums\AuditStatus;
use App\Http\Requests\StoreAuditFindingRequest;
use App\Http\Requests\StoreAuditRequest;
use App\Http\Requests\UpdateAuditRequest;
use App\Models\Audit;
use App\Models\AuditFinding;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $audits = Audit::with([
            'auditor:id,name',
        ])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                });
            })
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->orderByDesc('scheduled_date')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => Audit::count(),
            'planned' => Audit::where('status', AuditStatus::Planned)->count(),
            'in_progress' => Audit::where('status', AuditStatus::InProgress)->count(),
            'completed' => Audit::where('status', AuditStatus::Completed)->count(),
        ];

        return view('audits.index', [
            'audits' => $audits,
            'filters' => $request->only(['search', 'status', 'type']),
            'stats' => $stats,
        ]);
    }

    public function create(): View
    {
        $users = User::select('id', 'name', 'employee_code')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('audits.create', [
            'users' => $users,
        ]);
    }

    public function store(StoreAuditRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['tenant_id'] = Auth::user()->tenant_id;
        $data['created_by'] = Auth::id();
        $data['status'] = AuditStatus::Planned;
        $data['code'] = $this->generateCode();

        $audit = Audit::create($data);

        return redirect()->route('audits.show', $audit)
            ->with('success', 'Auditoría creada correctamente.');
    }

    public function show(Audit $audit): View
    {
        $audit->load([
            'auditor:id,name',
            'createdBy:id,name',
            'findings.assignedTo:id,name',
            'findings.correctiveActions',
        ]);

        $users = User::select('id', 'name')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('audits.show', [
            'audit' => $audit,
            'users' => $users,
        ]);
    }

    public function edit(Audit $audit): View
    {
        $users = User::select('id', 'name', 'employee_code')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('audits.edit', [
            'audit' => $audit,
            'users' => $users,
        ]);
    }

    public function update(UpdateAuditRequest $request, Audit $audit): RedirectResponse
    {
        $audit->update($request->validated());

        return redirect()->route('audits.show', $audit)
            ->with('success', 'Auditoría actualizada correctamente.');
    }

    public function storeFinding(StoreAuditFindingRequest $request, Audit $audit): RedirectResponse
    {
        $count = $audit->findings()->count();
        $code = 'F-'.str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        $audit->findings()->create(array_merge($request->validated(), [
            'code' => $code,
            'status' => 'open',
        ]));

        $audit->increment('findings_count');

        return redirect()->route('audits.show', $audit)
            ->with('success', 'Hallazgo agregado correctamente.');
    }

    private function generateCode(): string
    {
        $last = Audit::withoutGlobalScopes()
            ->where('code', 'like', 'AUD-%')
            ->orderByDesc('id')
            ->value('code');

        $next = $last ? ((int) substr($last, 4)) + 1 : 1;

        return 'AUD-'.str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
