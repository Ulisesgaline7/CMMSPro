<?php

use App\Http\Controllers\Auth\RoleLoginController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\CorrectiveActionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Iot\DashboardController as IotDashboardController;
use App\Http\Controllers\Iot\SensorAlertController;
use App\Http\Controllers\Iot\SensorController;
use App\Http\Controllers\Iot\SensorReadingController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MaintenancePlanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PermitToWorkController;
use App\Http\Controllers\Predictive\AssetAnalysisController;
use App\Http\Controllers\Predictive\DashboardController as PredictiveDashboardController;
use App\Http\Controllers\Predictive\ReportController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\Settings\BrandingController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UserCertificationController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Middleware\ResolveTenant;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// ── Role-specific login pages ──────────────────────────────────────────────
Route::get('/login/{role}', [RoleLoginController::class, 'show'])
    ->middleware('guest')
    ->name('login.role');

// ── Public landing (multi-page) ────────────────────────────────────────────
Route::get('/',          [LandingController::class, 'home'])->name('home');
Route::get('/producto',  [LandingController::class, 'producto'])->name('landing.producto');
Route::get('/modulos',   [LandingController::class, 'modulos'])->name('landing.modulos');
Route::get('/precios',   [LandingController::class, 'precios'])->name('landing.precios');
Route::get('/clientes',  [LandingController::class, 'clientes'])->name('landing.clientes');
Route::get('/contacto',  [LandingController::class, 'contacto'])->name('landing.contacto');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Assets
    Route::get('assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('assets/create', [AssetController::class, 'create'])->name('assets.create');
    Route::post('assets', [AssetController::class, 'store'])->name('assets.store');
    Route::get('assets/{asset}/qr', [AssetController::class, 'qr'])->name('assets.qr');
    Route::get('assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
    Route::get('assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
    Route::patch('assets/{asset}', [AssetController::class, 'update'])->name('assets.update');

    // Asset Categories
    Route::get('asset-categories', [AssetCategoryController::class, 'index'])->name('asset-categories.index');
    Route::get('asset-categories/create', [AssetCategoryController::class, 'create'])->name('asset-categories.create');
    Route::post('asset-categories', [AssetCategoryController::class, 'store'])->name('asset-categories.store');
    Route::get('asset-categories/{assetCategory}/edit', [AssetCategoryController::class, 'edit'])->name('asset-categories.edit');
    Route::patch('asset-categories/{assetCategory}', [AssetCategoryController::class, 'update'])->name('asset-categories.update');

    // Inventory
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
    Route::post('inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('inventory/{part}', [InventoryController::class, 'show'])->name('inventory.show');
    Route::get('inventory/{part}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
    Route::patch('inventory/{part}', [InventoryController::class, 'update'])->name('inventory.update');

    // Locations
    Route::get('locations', [LocationController::class, 'index'])->name('locations.index');
    Route::get('locations/create', [LocationController::class, 'create'])->name('locations.create');
    Route::post('locations', [LocationController::class, 'store'])->name('locations.store');
    Route::get('locations/{location}/edit', [LocationController::class, 'edit'])->name('locations.edit');
    Route::patch('locations/{location}', [LocationController::class, 'update'])->name('locations.update');

    // Work Orders
    Route::get('work-orders', [WorkOrderController::class, 'index'])->name('work-orders.index');
    Route::get('work-orders/create', [WorkOrderController::class, 'create'])->name('work-orders.create');
    Route::post('work-orders', [WorkOrderController::class, 'store'])->name('work-orders.store');
    Route::get('work-orders/{workOrder}', [WorkOrderController::class, 'show'])->name('work-orders.show');
    Route::get('work-orders/{workOrder}/edit', [WorkOrderController::class, 'edit'])->name('work-orders.edit');
    Route::patch('work-orders/{workOrder}', [WorkOrderController::class, 'update'])->name('work-orders.update');
    Route::post('work-orders/{workOrder}/complete-item', [WorkOrderController::class, 'completeChecklistItem'])->name('work-orders.complete-item');
    Route::post('work-orders/{workOrder}/notes', [WorkOrderController::class, 'addNote'])->name('work-orders.notes.store');
    Route::patch('work-orders/{workOrder}/status', [WorkOrderController::class, 'updateStatus'])->name('work-orders.status.update');
    Route::get('work-orders/{workOrder}/status', fn ($workOrder) => redirect()->route('work-orders.show', $workOrder));

    // User Certifications
    Route::get('certifications', [UserCertificationController::class, 'index'])->name('certifications.index');
    Route::get('certifications/create', [UserCertificationController::class, 'create'])->name('certifications.create');
    Route::post('certifications', [UserCertificationController::class, 'store'])->name('certifications.store');
    Route::get('certifications/{certification}', [UserCertificationController::class, 'show'])->name('certifications.show');
    Route::get('certifications/{certification}/edit', [UserCertificationController::class, 'edit'])->name('certifications.edit');
    Route::patch('certifications/{certification}', [UserCertificationController::class, 'update'])->name('certifications.update');

    // Documents
    Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('documents/create', [DocumentController::class, 'create'])->name('documents.create');
    Route::post('documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
    Route::patch('documents/{document}', [DocumentController::class, 'update'])->name('documents.update');

    // Purchase Orders
    Route::get('purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    Route::get('purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
    Route::post('purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
    Route::get('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
    Route::get('purchase-orders/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])->name('purchase-orders.edit');
    Route::patch('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('purchase-orders.update');

    // Audits
    Route::get('audits', [AuditController::class, 'index'])->name('audits.index');
    Route::get('audits/create', [AuditController::class, 'create'])->name('audits.create');
    Route::post('audits', [AuditController::class, 'store'])->name('audits.store');
    Route::get('audits/{audit}', [AuditController::class, 'show'])->name('audits.show');
    Route::get('audits/{audit}/edit', [AuditController::class, 'edit'])->name('audits.edit');
    Route::patch('audits/{audit}', [AuditController::class, 'update'])->name('audits.update');
    Route::post('audits/{audit}/findings', [AuditController::class, 'storeFinding'])->name('audits.findings.store');

    // Corrective Actions (CAPA)
    Route::get('corrective-actions', [CorrectiveActionController::class, 'index'])->name('corrective-actions.index');
    Route::get('corrective-actions/create', [CorrectiveActionController::class, 'create'])->name('corrective-actions.create');
    Route::post('corrective-actions', [CorrectiveActionController::class, 'store'])->name('corrective-actions.store');
    Route::get('corrective-actions/{correctiveAction}', [CorrectiveActionController::class, 'show'])->name('corrective-actions.show');
    Route::get('corrective-actions/{correctiveAction}/edit', [CorrectiveActionController::class, 'edit'])->name('corrective-actions.edit');
    Route::patch('corrective-actions/{correctiveAction}', [CorrectiveActionController::class, 'update'])->name('corrective-actions.update');

    // Shifts (Turnos)
    Route::get('shifts', [ShiftController::class, 'index'])->name('shifts.index');
    Route::get('shifts/create', [ShiftController::class, 'create'])->name('shifts.create');
    Route::post('shifts', [ShiftController::class, 'store'])->name('shifts.store');
    Route::get('shifts/{shift}', [ShiftController::class, 'show'])->name('shifts.show');
    Route::get('shifts/{shift}/edit', [ShiftController::class, 'edit'])->name('shifts.edit');
    Route::patch('shifts/{shift}', [ShiftController::class, 'update'])->name('shifts.update');

    // Service Requests (Facilities)
    Route::get('service-requests', [ServiceRequestController::class, 'index'])->name('service-requests.index');
    Route::get('service-requests/create', [ServiceRequestController::class, 'create'])->name('service-requests.create');
    Route::post('service-requests', [ServiceRequestController::class, 'store'])->name('service-requests.store');
    Route::get('service-requests/{serviceRequest}', [ServiceRequestController::class, 'show'])->name('service-requests.show');
    Route::get('service-requests/{serviceRequest}/edit', [ServiceRequestController::class, 'edit'])->name('service-requests.edit');
    Route::patch('service-requests/{serviceRequest}', [ServiceRequestController::class, 'update'])->name('service-requests.update');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Permits to Work (LOTO)
    Route::get('permits', [PermitToWorkController::class, 'index'])->name('permits.index');
    Route::get('permits/create', [PermitToWorkController::class, 'create'])->name('permits.create');
    Route::post('permits', [PermitToWorkController::class, 'store'])->name('permits.store');
    Route::get('permits/{permit}', [PermitToWorkController::class, 'show'])->name('permits.show');
    Route::get('permits/{permit}/edit', [PermitToWorkController::class, 'edit'])->name('permits.edit');
    Route::patch('permits/{permit}', [PermitToWorkController::class, 'update'])->name('permits.update');
    Route::post('permits/{permit}/submit', [PermitToWorkController::class, 'submit'])->name('permits.submit');
    Route::post('permits/{permit}/approve', [PermitToWorkController::class, 'approve'])->name('permits.approve');
    Route::post('permits/{permit}/reject', [PermitToWorkController::class, 'reject'])->name('permits.reject');
    Route::post('permits/{permit}/activate', [PermitToWorkController::class, 'activate'])->name('permits.activate');
    Route::post('permits/{permit}/close', [PermitToWorkController::class, 'close'])->name('permits.close');

    // Maintenance Plans
    Route::get('maintenance-plans', [MaintenancePlanController::class, 'index'])->name('maintenance-plans.index');
    Route::get('maintenance-plans/create', [MaintenancePlanController::class, 'create'])->name('maintenance-plans.create');
    Route::post('maintenance-plans', [MaintenancePlanController::class, 'store'])->name('maintenance-plans.store');
    Route::get('maintenance-plans/{maintenancePlan}', [MaintenancePlanController::class, 'show'])->name('maintenance-plans.show');
    Route::get('maintenance-plans/{maintenancePlan}/edit', [MaintenancePlanController::class, 'edit'])->name('maintenance-plans.edit');
    Route::patch('maintenance-plans/{maintenancePlan}', [MaintenancePlanController::class, 'update'])->name('maintenance-plans.update');

    // IoT Sensors
    Route::prefix('iot')->name('iot.')->group(function (): void {
        Route::get('/', IotDashboardController::class)->name('dashboard');
        Route::resource('sensors', SensorController::class)->except(['destroy']);
        Route::post('sensors/{sensor}/readings', [SensorReadingController::class, 'store'])->name('sensors.readings.store');
        Route::get('alerts', [SensorAlertController::class, 'index'])->name('alerts.index');
        Route::post('alerts/{alert}/acknowledge', [SensorAlertController::class, 'acknowledge'])->name('alerts.acknowledge');
        Route::post('alerts/{alert}/resolve', [SensorAlertController::class, 'resolve'])->name('alerts.resolve');
    });

    // Predictive Analytics
    Route::prefix('predictive')->name('predictive.')->group(function (): void {
        Route::get('/', PredictiveDashboardController::class)->name('dashboard');
        Route::get('assets/{asset}', [AssetAnalysisController::class, 'show'])->name('assets.show');
        Route::post('assets/{asset}/recalculate', [AssetAnalysisController::class, 'recalculate'])->name('assets.recalculate');
        Route::get('report', [ReportController::class, 'index'])->name('report');
    });

    // Settings - Branding
    Route::get('settings/branding', [BrandingController::class, 'edit'])->name('settings.branding.edit');
    Route::patch('settings/branding', [BrandingController::class, 'update'])->name('settings.branding.update');
});

// ── Subdomain routes for tenants (production) ─────────────────────────────
// These mirror the authenticated routes above but are scoped to {subdomain}.{APP_DOMAIN}.
// In local dev (APP_DOMAIN not set) this group is skipped; the plain routes above are used.
if ($appDomain = config('app.domain')) {
    Route::domain('{subdomain}.' . $appDomain)
        ->middleware(['auth', 'verified', ResolveTenant::class])
        ->group(function () {
            Route::get('dashboard', DashboardController::class)->name('tenant.dashboard');

            Route::get('assets', [AssetController::class, 'index']);
            Route::get('assets/create', [AssetController::class, 'create']);
            Route::post('assets', [AssetController::class, 'store']);
            Route::get('assets/{asset}/qr', [AssetController::class, 'qr']);
            Route::get('assets/{asset}', [AssetController::class, 'show']);
            Route::get('assets/{asset}/edit', [AssetController::class, 'edit']);
            Route::patch('assets/{asset}', [AssetController::class, 'update']);

            Route::get('asset-categories', [AssetCategoryController::class, 'index']);
            Route::get('asset-categories/create', [AssetCategoryController::class, 'create']);
            Route::post('asset-categories', [AssetCategoryController::class, 'store']);
            Route::get('asset-categories/{assetCategory}/edit', [AssetCategoryController::class, 'edit']);
            Route::patch('asset-categories/{assetCategory}', [AssetCategoryController::class, 'update']);

            Route::get('inventory', [InventoryController::class, 'index']);
            Route::get('inventory/create', [InventoryController::class, 'create']);
            Route::post('inventory', [InventoryController::class, 'store']);
            Route::get('inventory/{part}', [InventoryController::class, 'show']);
            Route::get('inventory/{part}/edit', [InventoryController::class, 'edit']);
            Route::patch('inventory/{part}', [InventoryController::class, 'update']);

            Route::get('locations', [LocationController::class, 'index']);
            Route::get('locations/create', [LocationController::class, 'create']);
            Route::post('locations', [LocationController::class, 'store']);
            Route::get('locations/{location}/edit', [LocationController::class, 'edit']);
            Route::patch('locations/{location}', [LocationController::class, 'update']);

            Route::get('work-orders', [WorkOrderController::class, 'index']);
            Route::get('work-orders/create', [WorkOrderController::class, 'create']);
            Route::post('work-orders', [WorkOrderController::class, 'store']);
            Route::get('work-orders/{workOrder}', [WorkOrderController::class, 'show']);
            Route::get('work-orders/{workOrder}/edit', [WorkOrderController::class, 'edit']);
            Route::patch('work-orders/{workOrder}', [WorkOrderController::class, 'update']);
            Route::post('work-orders/{workOrder}/complete-item', [WorkOrderController::class, 'completeChecklistItem']);
            Route::post('work-orders/{workOrder}/notes', [WorkOrderController::class, 'addNote']);
            Route::patch('work-orders/{workOrder}/status', [WorkOrderController::class, 'updateStatus']);

            Route::get('certifications', [UserCertificationController::class, 'index']);
            Route::get('certifications/create', [UserCertificationController::class, 'create']);
            Route::post('certifications', [UserCertificationController::class, 'store']);
            Route::get('certifications/{certification}', [UserCertificationController::class, 'show']);
            Route::get('certifications/{certification}/edit', [UserCertificationController::class, 'edit']);
            Route::patch('certifications/{certification}', [UserCertificationController::class, 'update']);

            Route::get('documents', [DocumentController::class, 'index']);
            Route::get('documents/create', [DocumentController::class, 'create']);
            Route::post('documents', [DocumentController::class, 'store']);
            Route::get('documents/{document}', [DocumentController::class, 'show']);
            Route::get('documents/{document}/edit', [DocumentController::class, 'edit']);
            Route::patch('documents/{document}', [DocumentController::class, 'update']);

            Route::get('purchase-orders', [PurchaseOrderController::class, 'index']);
            Route::get('purchase-orders/create', [PurchaseOrderController::class, 'create']);
            Route::post('purchase-orders', [PurchaseOrderController::class, 'store']);
            Route::get('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show']);
            Route::get('purchase-orders/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit']);
            Route::patch('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update']);

            Route::get('audits', [AuditController::class, 'index']);
            Route::get('audits/create', [AuditController::class, 'create']);
            Route::post('audits', [AuditController::class, 'store']);
            Route::get('audits/{audit}', [AuditController::class, 'show']);
            Route::get('audits/{audit}/edit', [AuditController::class, 'edit']);
            Route::patch('audits/{audit}', [AuditController::class, 'update']);
            Route::post('audits/{audit}/findings', [AuditController::class, 'storeFinding']);

            Route::get('corrective-actions', [CorrectiveActionController::class, 'index']);
            Route::get('corrective-actions/create', [CorrectiveActionController::class, 'create']);
            Route::post('corrective-actions', [CorrectiveActionController::class, 'store']);
            Route::get('corrective-actions/{correctiveAction}', [CorrectiveActionController::class, 'show']);
            Route::get('corrective-actions/{correctiveAction}/edit', [CorrectiveActionController::class, 'edit']);
            Route::patch('corrective-actions/{correctiveAction}', [CorrectiveActionController::class, 'update']);

            Route::get('shifts', [ShiftController::class, 'index']);
            Route::get('shifts/create', [ShiftController::class, 'create']);
            Route::post('shifts', [ShiftController::class, 'store']);
            Route::get('shifts/{shift}', [ShiftController::class, 'show']);
            Route::get('shifts/{shift}/edit', [ShiftController::class, 'edit']);
            Route::patch('shifts/{shift}', [ShiftController::class, 'update']);

            Route::get('service-requests', [ServiceRequestController::class, 'index']);
            Route::get('service-requests/create', [ServiceRequestController::class, 'create']);
            Route::post('service-requests', [ServiceRequestController::class, 'store']);
            Route::get('service-requests/{serviceRequest}', [ServiceRequestController::class, 'show']);
            Route::get('service-requests/{serviceRequest}/edit', [ServiceRequestController::class, 'edit']);
            Route::patch('service-requests/{serviceRequest}', [ServiceRequestController::class, 'update']);

            Route::get('notifications', [NotificationController::class, 'index']);
            Route::post('notifications/{id}/read', [NotificationController::class, 'markRead']);
            Route::post('notifications/read-all', [NotificationController::class, 'markAllRead']);

            Route::get('permits', [PermitToWorkController::class, 'index']);
            Route::get('permits/create', [PermitToWorkController::class, 'create']);
            Route::post('permits', [PermitToWorkController::class, 'store']);
            Route::get('permits/{permit}', [PermitToWorkController::class, 'show']);
            Route::get('permits/{permit}/edit', [PermitToWorkController::class, 'edit']);
            Route::patch('permits/{permit}', [PermitToWorkController::class, 'update']);
            Route::post('permits/{permit}/submit', [PermitToWorkController::class, 'submit']);
            Route::post('permits/{permit}/approve', [PermitToWorkController::class, 'approve']);
            Route::post('permits/{permit}/reject', [PermitToWorkController::class, 'reject']);
            Route::post('permits/{permit}/activate', [PermitToWorkController::class, 'activate']);
            Route::post('permits/{permit}/close', [PermitToWorkController::class, 'close']);

            Route::get('maintenance-plans', [MaintenancePlanController::class, 'index']);
            Route::get('maintenance-plans/create', [MaintenancePlanController::class, 'create']);
            Route::post('maintenance-plans', [MaintenancePlanController::class, 'store']);
            Route::get('maintenance-plans/{maintenancePlan}', [MaintenancePlanController::class, 'show']);
            Route::get('maintenance-plans/{maintenancePlan}/edit', [MaintenancePlanController::class, 'edit']);
            Route::patch('maintenance-plans/{maintenancePlan}', [MaintenancePlanController::class, 'update']);

            Route::prefix('iot')->name('iot.')->group(function (): void {
                Route::get('/', IotDashboardController::class)->name('dashboard');
                Route::resource('sensors', SensorController::class)->except(['destroy']);
                Route::post('sensors/{sensor}/readings', [SensorReadingController::class, 'store'])->name('sensors.readings.store');
                Route::get('alerts', [SensorAlertController::class, 'index'])->name('alerts.index');
                Route::post('alerts/{alert}/acknowledge', [SensorAlertController::class, 'acknowledge'])->name('alerts.acknowledge');
                Route::post('alerts/{alert}/resolve', [SensorAlertController::class, 'resolve'])->name('alerts.resolve');
            });

            Route::prefix('predictive')->name('predictive.')->group(function (): void {
                Route::get('/', PredictiveDashboardController::class)->name('dashboard');
                Route::get('assets/{asset}', [AssetAnalysisController::class, 'show'])->name('assets.show');
                Route::post('assets/{asset}/recalculate', [AssetAnalysisController::class, 'recalculate'])->name('assets.recalculate');
                Route::get('report', [ReportController::class, 'index'])->name('report');
            });

            Route::get('settings/branding', [BrandingController::class, 'edit']);
            Route::patch('settings/branding', [BrandingController::class, 'update']);
        });
}

require __DIR__.'/settings.php';
