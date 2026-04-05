<?php

use App\Http\Controllers\Billing\CheckoutController;
use App\Http\Controllers\Billing\PortalController;
use App\Http\Controllers\Billing\WebhookController;
use Illuminate\Support\Facades\Route;

// Stripe webhook (no auth, CSRF excluded in bootstrap/app.php)
Route::post('/stripe/webhook', WebhookController::class)->name('stripe.webhook');

Route::middleware(['auth', 'verified'])
    ->prefix('billing')
    ->name('billing.')
    ->group(function (): void {
        Route::get('checkout', [CheckoutController::class, 'show'])->name('checkout.show');
        Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout.store');
        Route::get('checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
        Route::get('portal', PortalController::class)->name('portal');
    });
