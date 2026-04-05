<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::withoutGlobalScopes()
            ->with('tenant:id,name,slug')
            ->when($request->search, function ($q, $s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('super-admin.users.index', [
            'users' => $users,
            'filters' => $request->only(['search']),
        ]);
    }

    public function impersonate(int $userId): RedirectResponse
    {
        $user = User::withoutGlobalScopes()->findOrFail($userId);

        session(['impersonating_as' => $userId, 'impersonator_id' => auth()->id()]);
        auth()->loginUsingId($userId);

        return redirect()->route('dashboard')
            ->with('success', "Estás actuando como {$user->name}.");
    }

    public function stopImpersonating(): RedirectResponse
    {
        $impersonatorId = session('impersonator_id');

        if ($impersonatorId) {
            session()->forget(['impersonating_as', 'impersonator_id']);
            auth()->loginUsingId($impersonatorId);
        }

        return redirect()->route('super-admin.dashboard')
            ->with('success', 'Has vuelto a tu cuenta de super administrador.');
    }
}
