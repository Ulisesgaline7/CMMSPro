<?php

namespace App\Http\Controllers\Billing;

use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $webhookSecret = config('services.stripe.webhook_secret');

        if (! $webhookSecret) {
            return response('Webhook secret not configured.', 400);
        }

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\Exception $e) {
            return response('Webhook error: '.$e->getMessage(), 400);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event->data->object),
            'invoice.payment_succeeded' => $this->handleInvoicePaymentSucceeded($event->data->object),
            'invoice.payment_failed' => $this->handleInvoicePaymentFailed($event->data->object),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event->data->object),
            default => Log::info('Unhandled Stripe event: '.$event->type),
        };

        return response('OK', 200);
    }

    private function handleCheckoutCompleted(object $session): void
    {
        $tenantId = $session->metadata->tenant_id ?? null;

        if (! $tenantId) {
            return;
        }

        Subscription::updateOrCreate(
            ['tenant_id' => $tenantId],
            [
                'stripe_subscription_id' => $session->subscription,
                'stripe_customer_id' => $session->customer,
                'status' => SubscriptionStatus::Active,
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]
        );
    }

    private function handleInvoicePaymentSucceeded(object $invoice): void
    {
        $tenant = Tenant::where('stripe_customer_id', $invoice->customer)->first();

        if (! $tenant) {
            return;
        }

        Invoice::create([
            'tenant_id' => $tenant->id,
            'stripe_invoice_id' => $invoice->id,
            'amount_due' => $invoice->amount_due,
            'amount_paid' => $invoice->amount_paid,
            'currency' => $invoice->currency,
            'status' => 'paid',
            'invoice_pdf_url' => $invoice->invoice_pdf,
            'paid_at' => now(),
            'period_start' => isset($invoice->period_start) ? \Carbon\Carbon::createFromTimestamp($invoice->period_start) : null,
            'period_end' => isset($invoice->period_end) ? \Carbon\Carbon::createFromTimestamp($invoice->period_end) : null,
        ]);

        Subscription::where('tenant_id', $tenant->id)->update([
            'status' => SubscriptionStatus::Active,
        ]);
    }

    private function handleInvoicePaymentFailed(object $invoice): void
    {
        $tenant = Tenant::where('stripe_customer_id', $invoice->customer)->first();

        if (! $tenant) {
            return;
        }

        Subscription::where('tenant_id', $tenant->id)->update([
            'status' => SubscriptionStatus::PastDue,
        ]);
    }

    private function handleSubscriptionDeleted(object $subscription): void
    {
        Subscription::where('stripe_subscription_id', $subscription->id)->update([
            'status' => SubscriptionStatus::Canceled,
        ]);
    }
}
