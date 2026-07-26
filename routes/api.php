<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TreatmentController;
use Illuminate\Support\Facades\Route;

// ── API v1 ─────────────────────────────────────────────────────────────
Route::prefix('v1')->group(function () {

    // ── Public: Branches ──────────────────────────────────────────────
    Route::get('branches',        [BranchController::class, 'index']);

    // ── Public: Auth ──────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('register',    [AuthController::class, 'register']);
        Route::post('login',       [AuthController::class, 'login']);
        Route::post('verify-2fa',  [AuthController::class, 'verify2fa']);
    });

    // ── Public: Treatments ────────────────────────────────────────────
    Route::get('treatments',      [TreatmentController::class, 'index']);
    Route::get('treatments/{treatment}', [TreatmentController::class, 'show']);

    // ── Public: Products ──────────────────────────────────────────────
    Route::get('products',        [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);
    Route::get('products/{product}/reviews', [\App\Http\Controllers\ReviewController::class, 'index']);

    // ── Public: Appointment slots ─────────────────────────────────────
    Route::get('appointments/available-slots', [AppointmentController::class, 'availableSlots']);

    // ── Public: Book appointment (can be anonymous) ───────────────────
    Route::post('appointments',   [AppointmentController::class, 'store']);

    // ── Public: Create order (can be anonymous) ───────────────────────
    Route::post('orders',         [OrderController::class, 'store']);

    // ── Authenticated: Customer ───────────────────────────────────────
    Route::middleware('auth:api')->group(function () {
        Route::post('auth/logout',  [AuthController::class, 'logout']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);
        Route::get('auth/me',       [AuthController::class, 'me']);

        // Customer profile & data
        Route::prefix('user')->group(function () {
            Route::get('appointments',   [AppointmentController::class, 'myAppointments']);
            Route::get('orders',         [OrderController::class, 'myOrders']);
            Route::get('loyalty-points', [AppointmentController::class, 'myLoyaltyPoints']);
        });

        // Appointment management (customer)
        Route::get('appointments/{appointment}',    [AppointmentController::class, 'show']);
        Route::put('appointments/{appointment}',    [AppointmentController::class, 'update']);
        Route::delete('appointments/{appointment}', [AppointmentController::class, 'destroy']);

        // Order detail (customer)
        Route::get('orders/{order}', [OrderController::class, 'show']);

        // Reviews (authenticated customer)
        Route::post('reviews', [\App\Http\Controllers\ReviewController::class, 'store']);

        // Loyalty points redemption
        Route::post('loyalty/redeem', [\App\Http\Controllers\LoyaltyController::class, 'redeem']);
    });

    // ── Authenticated: Admin / Staff ──────────────────────────────────
    Route::middleware('auth:api_staff')->prefix('admin')->group(function () {
        Route::post('auth/logout',  [AuthController::class, 'logout']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);
        Route::get('auth/me',       [AuthController::class, 'me']);

        // Dashboard (all admin roles)
        Route::get('dashboard', [AdminController::class, 'dashboard']);

        // 2FA Setup
        Route::get('2fa/setup',    [AdminController::class, 'setup2FA']);
        Route::post('2fa/toggle',  [AdminController::class, 'toggle2FA']);

        // Notifications
        Route::get('notifications',              [AdminController::class, 'notifications']);
        Route::put('notifications/{notification}/read', [AdminController::class, 'markRead']);

        // Appointments management (owner + admin_klinik)
        Route::middleware('role:owner,branch_manager,admin_klinik')->group(function () {
            Route::get('appointments',                   [AppointmentController::class, 'adminIndex']);
            Route::put('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus']);
        });

        // Products management (admin_produk)
        Route::middleware('role:owner,branch_manager,admin_produk')->group(function () {
            Route::post('products',               [ProductController::class, 'store']);
            Route::put('products/{product}',      [ProductController::class, 'update']);
            Route::delete('products/{product}',   [ProductController::class, 'destroy']);
            Route::put('products/{product}/stock',[ProductController::class, 'updateStock']);
        });

        // Orders management (owner + admin_produk)
        Route::middleware('role:owner,branch_manager,admin_produk')->group(function () {
            Route::get('orders',                         [OrderController::class, 'adminIndex']);
            Route::get('orders/{order}',                 [OrderController::class, 'show']);
            Route::put('orders/{order}/status',          [OrderController::class, 'updateStatus']);
            Route::post('orders/{order}/send-whatsapp',  [OrderController::class, 'sendWhatsApp']);
        });

        // Treatments management (owner + admin_klinik)
        Route::middleware('role:owner,branch_manager,admin_klinik')->group(function () {
            Route::post('treatments',             [TreatmentController::class, 'store']);
            Route::put('treatments/{treatment}',  [TreatmentController::class, 'update']);
            Route::delete('treatments/{treatment}',[TreatmentController::class, 'destroy']);
        });

        // Staff management (owner + branch_manager)
        Route::middleware('role:owner,branch_manager')->group(function () {
            Route::get('staff',          [AdminController::class, 'staffIndex']);
            Route::post('staff',         [AdminController::class, 'staffStore']);
            Route::put('staff/{staff}',  [AdminController::class, 'staffUpdate']);
            Route::delete('staff/{staff}', [AdminController::class, 'staffDestroy']);
        });

        // Branch management
        Route::get('branches',           [\App\Http\Controllers\Admin\BranchController::class, 'index']); // All staff can list branches
        Route::middleware('role:owner')->group(function () {
            Route::post('branches',          [\App\Http\Controllers\Admin\BranchController::class, 'store']);
            Route::put('branches/{branch}',  [\App\Http\Controllers\Admin\BranchController::class, 'update']);
            Route::delete('branches/{branch}', [\App\Http\Controllers\Admin\BranchController::class, 'destroy']);
        });

        // POS / Kasir (all admin roles can access)
        Route::prefix('pos')->group(function () {
            Route::get('appointments',          [TransactionController::class, 'todayAppointments']);
            Route::get('transactions/summary',  [TransactionController::class, 'summary']);
            Route::get('transactions',          [TransactionController::class, 'index']);
            Route::get('transactions/{transaction}', [TransactionController::class, 'show']);
            Route::post('checkout',             [TransactionController::class, 'checkout']);
        });

        // Receipt settings (owner + branch_manager)
        Route::middleware('role:owner,branch_manager')->group(function () {
            Route::get('receipt-settings',  [\App\Http\Controllers\Admin\ReceiptSettingController::class, 'show']);
            Route::put('receipt-settings',  [\App\Http\Controllers\Admin\ReceiptSettingController::class, 'update']);
        });

        // Settings (owner only)
        Route::middleware('role:owner')->put('settings', [\App\Http\Controllers\AdminController::class, 'updateSettings']);

        // Reports (owner + branch_manager)
        Route::middleware('role:owner,branch_manager')->prefix('reports')->group(function () {
            Route::get('sales',          [ReportController::class, 'sales']);
            Route::get('top-performing', [ReportController::class, 'topPerforming']);
            Route::get('demographics',   [ReportController::class, 'demographics']);
        });
        // Public read for POS (all staff)
        Route::get('receipt-settings/view', [\App\Http\Controllers\Admin\ReceiptSettingController::class, 'show']);
    });
});
