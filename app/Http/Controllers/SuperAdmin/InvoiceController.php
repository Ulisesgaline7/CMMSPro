<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $invoices = Invoice::withoutGlobalScopes()
            ->with('tenant')
            ->latest()
            ->paginate(25);

        $totalRevenue = (float) Invoice::withoutGlobalScopes()
            ->where('status', 'paid')
            ->sum('amount_paid');

        $pendingAmount = (float) Invoice::withoutGlobalScopes()
            ->whereIn('status', ['open', 'draft'])
            ->sum('amount_due');

        $overdueCount = Invoice::withoutGlobalScopes()
            ->where('status', 'open')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->count();

        $totalCount = Invoice::withoutGlobalScopes()->count();

        return view('super-admin.invoices', compact(
            'invoices',
            'totalRevenue',
            'pendingAmount',
            'overdueCount',
            'totalCount',
        ));
    }
}
