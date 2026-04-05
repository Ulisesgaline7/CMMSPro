<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = Auth::user()
            ->notifications()
            ->paginate(30);

        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function markRead(string $id): RedirectResponse
    {
        Auth::user()
            ->notifications()
            ->where('id', $id)
            ->update(['read_at' => now()]);

        return back();
    }

    public function markAllRead(): RedirectResponse
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'Notificaciones marcadas como leídas.');
    }
}
