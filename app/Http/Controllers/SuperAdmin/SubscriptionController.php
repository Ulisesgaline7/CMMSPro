<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Enums\SubscriptionStatus;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(): View
    {
        $subscriptions = Subscription::withoutGlobalScopes()
            ->with('tenant')
            ->latest()
            ->paginate(20);

        $mrr = (float) Subscription::withoutGlobalScopes()
            ->where('status', SubscriptionStatus::Active)
            ->sum('total_monthly');

        $pastDue = Subscription::withoutGlobalScopes()
            ->where('status', SubscriptionStatus::PastDue)
            ->count();

        $active = Subscription::withoutGlobalScopes()
            ->where('status', SubscriptionStatus::Active)
            ->count();

        return view('super-admin.subscriptions', compact('subscriptions', 'mrr', 'pastDue', 'active'));
    }
}
