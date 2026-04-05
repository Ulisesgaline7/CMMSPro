<?php

namespace App\Http\Controllers;

use App\Enums\ShiftStatus;
use App\Http\Requests\StoreShiftRequest;
use App\Http\Requests\UpdateShiftRequest;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function index(Request $request): View
    {
        $shifts = Shift::with('technician:id,name,employee_code')
            ->when($request->date, fn ($q, $d) => $q->whereDate('date', $d))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->user_id, fn ($q, $u) => $q->where('user_id', $u))
            ->orderByDesc('date')
            ->paginate(20)
            ->withQueryString();

        $today = now()->toDateString();

        $stats = [
            'total'     => Shift::count(),
            'scheduled' => Shift::where('status', ShiftStatus::Scheduled)->count(),
            'active'    => Shift::where('status', ShiftStatus::Active)->count(),
            'today'     => Shift::whereDate('date', $today)->count(),
            'absent'    => Shift::where('status', ShiftStatus::Absent)->count(),
        ];

        $technicians = User::select('id', 'name')
            ->whereIn('role', ['admin', 'supervisor', 'technician'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('shifts.index', [
            'shifts'      => $shifts,
            'stats'       => $stats,
            'technicians' => $technicians,
            'filters'     => $request->only(['date', 'type', 'status', 'user_id']),
        ]);
    }

    public function create(): View
    {
        $technicians = User::select('id', 'name', 'employee_code')
            ->whereIn('role', ['admin', 'supervisor', 'technician'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('shifts.create', [
            'technicians' => $technicians,
        ]);
    }

    public function store(StoreShiftRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['tenant_id'] = Auth::user()->tenant_id;
        $data['status'] = ShiftStatus::Scheduled;

        $shift = Shift::create($data);

        return redirect()->route('shifts.show', $shift)
            ->with('success', 'Turno programado correctamente.');
    }

    public function show(Shift $shift): View
    {
        $shift->load('technician');

        return view('shifts.show', [
            'shift' => $shift,
        ]);
    }

    public function edit(Shift $shift): View
    {
        $technicians = User::select('id', 'name', 'employee_code')
            ->whereIn('role', ['admin', 'supervisor', 'technician'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('shifts.edit', [
            'shift'       => $shift,
            'technicians' => $technicians,
        ]);
    }

    public function update(UpdateShiftRequest $request, Shift $shift): RedirectResponse
    {
        $shift->update($request->validated());

        return redirect()->route('shifts.show', $shift)
            ->with('success', 'Turno actualizado correctamente.');
    }
}
