<?php

namespace App\Http\Controllers;

use App\Enums\CertificationStatus;
use App\Http\Requests\StoreUserCertificationRequest;
use App\Http\Requests\UpdateUserCertificationRequest;
use App\Models\User;
use App\Models\UserCertification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserCertificationController extends Controller
{
    public function index(Request $request): View
    {
        $certifications = UserCertification::with('user:id,name,employee_code')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->user_id, fn ($q, $u) => $q->where('user_id', $u))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => UserCertification::count(),
            'active' => UserCertification::where('status', CertificationStatus::Active)->count(),
            'expired' => UserCertification::where('status', CertificationStatus::Expired)->count(),
            'expiring_soon' => UserCertification::where('status', CertificationStatus::Active)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', now()->addDays(30))
                ->count(),
        ];

        $technicians = User::select('id', 'name', 'employee_code')
            ->orderBy('name')
            ->get();

        return view('certifications.index', [
            'certifications' => $certifications,
            'filters' => $request->only(['status', 'user_id']),
            'stats' => $stats,
            'technicians' => $technicians,
        ]);
    }

    public function create(): View
    {
        $technicians = User::select('id', 'name', 'employee_code')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('certifications.create', [
            'technicians' => $technicians,
        ]);
    }

    public function store(StoreUserCertificationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['tenant_id'] = Auth::user()->tenant_id;

        $certification = UserCertification::create($data);

        return redirect()->route('certifications.show', $certification)
            ->with('success', 'Certificación registrada correctamente.');
    }

    public function show(UserCertification $certification): View
    {
        $certification->load('user:id,name,employee_code,role');

        return view('certifications.show', [
            'certification' => $certification,
        ]);
    }

    public function edit(UserCertification $certification): View
    {
        $technicians = User::select('id', 'name', 'employee_code')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('certifications.edit', [
            'certification' => $certification,
            'technicians' => $technicians,
        ]);
    }

    public function update(UpdateUserCertificationRequest $request, UserCertification $certification): RedirectResponse
    {
        $certification->update($request->validated());

        return redirect()->route('certifications.show', $certification)
            ->with('success', 'Certificación actualizada correctamente.');
    }
}
