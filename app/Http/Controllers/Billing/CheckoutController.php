<?php

namespace App\Http\Controllers\Billing;

use App\Enums\DeploymentType;
use App\Enums\ModuleKey;
use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\TenantModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function show(): View
    {
        $tenant = Auth::user()->tenant;
        $subscription = $tenant?->subscription;

        return view('billing.checkout', [
            'modules' => ModuleKey::cases(),
            'deploymentTypes' => DeploymentType::cases(),
            'subscription' => $subscription,
            'tenant' => $tenant,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'deployment_type' => ['required', 'string'],
            'modules' => ['nullable', 'array'],
            'modules.*' => ['string'],
            'admin_count' => ['required', 'integer', 'min:1'],
            'supervisor_count' => ['required', 'integer', 'min:0'],
            'technician_count' => ['required', 'integer', 'min:0'],
            'reader_count' => ['required', 'integer', 'min:0'],
            'asset_count' => ['required', 'integer', 'min:0'],
        ]);

        $tenant = Auth::user()->tenant;
        $deploymentType = DeploymentType::from($request->deployment_type);
        $selectedModules = $request->modules ?? [];

        $modulesTotal = collect($selectedModules)
            ->map(fn ($key) => ModuleKey::tryFrom($key)?->price() ?? 0)
            ->sum();

        $totalMonthly = $deploymentType->basePrice() + $modulesTotal;

        $stripeSecret = config('services.stripe.secret');

        if ($stripeSecret) {
            // Stripe integration
            \Stripe\Stripe::setApiKey($stripeSecret);

            $session = \Stripe\Checkout\Session::create([
                'mode' => 'subscription',
                'success_url' => route('billing.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('billing.checkout.show'),
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'recurring' => ['interval' => 'month'],
                        'unit_amount' => (int) ($totalMonthly * 100),
                        'product_data' => ['name' => 'CMMS Pro Subscription'],
                    ],
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'tenant_id' => $tenant->id,
                    'deployment_type' => $deploymentType->value,
                    'modules' => implode(',', $selectedModules),
                ],
            ]);

            return redirect()->away($session->url);
        }

        // Demo mode: create subscription locally
        $subscription = Subscription::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'status' => SubscriptionStatus::Active,
                'deployment_type' => $deploymentType->value,
                'base_price_monthly' => $deploymentType->basePrice(),
                'modules_cost' => $modulesTotal,
                'users_cost' => 0,
                'total_monthly' => $totalMonthly,
                'admin_count' => $request->admin_count,
                'supervisor_count' => $request->supervisor_count,
                'technician_count' => $request->technician_count,
                'reader_count' => $request->reader_count,
                'asset_count' => $request->asset_count,
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
                'modules_json' => $selectedModules,
            ]
        );

        // Activate selected modules for tenant
        foreach ($selectedModules as $moduleKey) {
            TenantModule::updateOrCreate(
                ['tenant_id' => $tenant->id, 'module_key' => $moduleKey],
                [
                    'is_active' => true,
                    'activated_at' => now(),
                    'activated_by' => Auth::id(),
                ]
            );
        }

        return redirect()->route('billing.checkout.show')
            ->with('success', 'Suscripción activada correctamente (modo demo).');
    }

    public function success(Request $request): RedirectResponse
    {
        if ($request->session_id && config('services.stripe.secret')) {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $session = \Stripe\Checkout\Session::retrieve($request->session_id);
            $tenant = Auth::user()->tenant;

            $selectedModules = explode(',', $session->metadata['modules'] ?? '');
            $deploymentType = DeploymentType::from($session->metadata['deployment_type'] ?? 'cloud_saas');

            Subscription::updateOrCreate(
                ['tenant_id' => $tenant->id],
                [
                    'stripe_subscription_id' => $session->subscription,
                    'stripe_customer_id' => $session->customer,
                    'status' => SubscriptionStatus::Active,
                    'deployment_type' => $deploymentType->value,
                    'base_price_monthly' => $deploymentType->basePrice(),
                    'current_period_start' => now(),
                    'current_period_end' => now()->addMonth(),
                    'modules_json' => array_filter($selectedModules),
                ]
            );

            foreach (array_filter($selectedModules) as $moduleKey) {
                TenantModule::updateOrCreate(
                    ['tenant_id' => $tenant->id, 'module_key' => $moduleKey],
                    ['is_active' => true, 'activated_at' => now(), 'activated_by' => Auth::id()]
                );
            }
        }

        return redirect()->route('billing.checkout.show')
            ->with('success', '¡Pago exitoso! Tu suscripción está activa.');
    }
}
