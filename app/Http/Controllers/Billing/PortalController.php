<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $stripeSecret = config('services.stripe.secret');
        $tenant = Auth::user()->tenant;

        if (! $stripeSecret || ! $tenant?->stripe_customer_id) {
            return redirect()->route('billing.checkout.show')
                ->with('error', 'No hay portal de facturación disponible. Contacta soporte.');
        }

        \Stripe\Stripe::setApiKey($stripeSecret);

        $session = \Stripe\BillingPortal\Session::create([
            'customer' => $tenant->stripe_customer_id,
            'return_url' => route('billing.checkout.show'),
        ]);

        return redirect()->away($session->url);
    }
}
