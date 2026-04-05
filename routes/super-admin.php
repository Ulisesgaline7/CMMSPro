<?php

use App\Http\Controllers\SuperAdmin\AccessController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\NotificationController;
use App\Http\Controllers\SuperAdmin\ProfileController;
use App\Http\Controllers\SuperAdmin\TestNotificationController;
use App\Http\Controllers\SuperAdmin\InvoiceController;
use App\Http\Controllers\SuperAdmin\LoginController;
use App\Http\Controllers\SuperAdmin\ModuleCatalogController;
use App\Http\Controllers\SuperAdmin\PricingController;
use App\Http\Controllers\SuperAdmin\RevenueController;
use App\Http\Controllers\SuperAdmin\SiteSettingController;
use App\Http\Controllers\SuperAdmin\SubscriptionController;
use App\Http\Controllers\SuperAdmin\TenantController;
use App\Http\Controllers\SuperAdmin\TenantModuleController;
use App\Http\Controllers\SuperAdmin\UserController;
use Illuminate\Support\Facades\Route;

// ── Super Admin Authentication (no auth middleware) ─────────────────────────
Route::prefix('super-admin')->name('super-admin.')->group(function (): void {
    Route::get('login', [LoginController::class, 'show'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:login');
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
});

// ── Super Admin Panel (requires super_admin role) ───────────────────────────
Route::middleware(['auth', 'verified', 'super_admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');

        // Profile
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::patch('notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
        Route::patch('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroy-all');
        Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');

        // Test push notifications
        Route::post('notifications/test', [TestNotificationController::class, 'send'])->name('notifications.test');

        // Tenants
        Route::get('tenants', [TenantController::class, 'index'])->name('tenants.index');
        Route::get('tenants/{id}', [TenantController::class, 'show'])->name('tenants.show');
        Route::get('tenants/{id}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
        Route::patch('tenants/{id}', [TenantController::class, 'update'])->name('tenants.update');
        Route::post('tenants/{id}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');
        Route::post('tenants/{id}/activate', [TenantController::class, 'activate'])->name('tenants.activate');

        // Tenant Modules
        Route::get('tenants/{tenantId}/modules', [TenantModuleController::class, 'index'])->name('tenant-modules.index');
        Route::post('tenants/{tenantId}/modules/toggle', [TenantModuleController::class, 'toggle'])->name('tenant-modules.toggle');

        // Users
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::post('users/{userId}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
        Route::post('users/stop-impersonating', [UserController::class, 'stopImpersonating'])->name('users.stop-impersonating');

        // Subscriptions
        Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');

        // Invoices
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');

        // Revenue / MRR
        Route::get('revenue', [RevenueController::class, 'index'])->name('revenue.index');
        Route::get('revenue/report', [RevenueController::class, 'report'])->name('revenue.report');

        // Module Catalog
        Route::get('modules', [ModuleCatalogController::class, 'index'])->name('modules.index');
        Route::get('modules/assignment', [ModuleCatalogController::class, 'assignment'])->name('modules.assignment');

        // Pricing
        Route::get('pricing', [PricingController::class, 'index'])->name('pricing.index');

        // Access / Domains
        Route::get('access', [AccessController::class, 'index'])->name('access.index');
        Route::patch('access/{id}', [AccessController::class, 'update'])->name('access.update');

        // Landing page / site settings
        Route::get('site-settings', [SiteSettingController::class, 'index'])->name('site-settings.index');
        Route::post('site-settings', [SiteSettingController::class, 'update'])->name('site-settings.update');
    });
