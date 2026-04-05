<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'all');

        $query = $request->user()->notifications()->latest();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->paginate(20);
        $unreadCount   = $request->user()->unreadNotifications()->count();
        $totalCount    = $request->user()->notifications()->count();

        return view('super-admin.notifications', compact('notifications', 'unreadCount', 'totalCount', 'filter'));
    }

    public function markRead(Request $request, string $id): RedirectResponse
    {
        $request->user()->notifications()->where('id', $id)->first()?->markAsRead();

        return back();
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        $request->user()->notifications()->where('id', $id)->delete();

        return back()->with('success', 'Notificación eliminada.');
    }

    public function destroyAll(Request $request): RedirectResponse
    {
        $request->user()->notifications()->delete();

        return back()->with('success', 'Todas las notificaciones eliminadas.');
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'count' => $request->user()->unreadNotifications()->count(),
        ]);
    }
}
