<?php

// routes/admin.php
// Create this new file for admin routes

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\FoodItemController;
use App\Http\Controllers\Admin\DistributionController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\MembershipTierController;
use App\Http\Controllers\Admin\ManualPaymentController;

// Guest routes (not authenticated)
Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
});

// Authenticated admin routes
Route::middleware(['admin'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // User Management
    Route::prefix('users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/referrals', [UserController::class, 'referrals'])->name('referrals');
        Route::post('/{user}/activate', [UserController::class, 'activate'])->name('activate');
        Route::post('/{user}/deactivate', [UserController::class, 'deactivate'])->name('deactivate');
        Route::post('/{user}/upgrade-tier', [UserController::class, 'upgradeTier'])->name('upgrade-tier');
        Route::get('/export/excel', [UserController::class, 'exportExcel'])->name('export.excel');
    });

    // Payment Management
    Route::prefix('payments')->name('admin.payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::post('/{payment}/verify', [PaymentController::class, 'verify'])->name('verify');
    });

    // Inventory Management
    Route::prefix('inventory')->name('admin.inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/transactions', [InventoryController::class, 'transactions'])->name('transactions');
        Route::post('/add-stock', [InventoryController::class, 'addStock'])->name('add-stock');
        Route::post('/remove-stock', [InventoryController::class, 'removeStock'])->name('remove-stock');
        Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('low-stock');
    });

    // Food Items Management
    Route::resource('food-items', FoodItemController::class, ['as' => 'admin']);

    // Distribution Management
    Route::prefix('distributions')->name('admin.distributions.')->group(function () {
        Route::get('/', [DistributionController::class, 'index'])->name('index');
        Route::get('/create', [DistributionController::class, 'create'])->name('create');
        Route::post('/', [DistributionController::class, 'store'])->name('store');
        Route::get('/{distribution}', [DistributionController::class, 'show'])->name('show');
        Route::post('/{distribution}/approve', [DistributionController::class, 'approve'])->name('approve');
        Route::post('/{distribution}/package', [DistributionController::class, 'markAsPackaged'])->name('package');
        Route::post('/{distribution}/dispatch', [DistributionController::class, 'dispatch'])->name('dispatch');
        Route::post('/{distribution}/deliver', [DistributionController::class, 'deliver'])->name('deliver');
        Route::post('/{distribution}/cancel', [DistributionController::class, 'cancel'])->name('cancel');
    });

    // Food Package Management
    Route::resource('packages', PackageController::class, ['as' => 'admin']);

    // Pickup Locations
    Route::resource('locations', LocationController::class, ['as' => 'admin']);

    // Settings
    Route::prefix('settings')->name('admin.settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/', [SettingController::class, 'update'])->name('update');
    });

    // Reports
    Route::prefix('reports')->name('admin.reports.')->group(function () {
        Route::get('/overview', [ReportController::class, 'overview'])->name('overview');
        Route::get('/users', [ReportController::class, 'users'])->name('users');
        Route::get('/payments', [ReportController::class, 'payments'])->name('payments');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/distributions', [ReportController::class, 'distributions'])->name('distributions');
    });

    // Support Tickets
    Route::prefix('support')->name('admin.support.')->group(function () {
        Route::get('/', [SupportController::class, 'index'])->name('index');
        Route::get('/{ticket}', [SupportController::class, 'show'])->name('show');
        Route::post('/{ticket}/reply', [SupportController::class, 'reply'])->name('reply');
        Route::post('/{ticket}/assign', [SupportController::class, 'assign'])->name('assign');
        Route::post('/{ticket}/resolve', [SupportController::class, 'resolve'])->name('resolve');
        Route::post('/{ticket}/close', [SupportController::class, 'close'])->name('close');
    });

    // Admin User Management (Super Admin only)
    Route::middleware(['permission:manage_settings'])->group(function () {
        Route::resource('admin-users', AdminUserController::class, ['as' => 'admin']);
    });

    // Membership Tiers Management
    Route::resource('membership-tiers', MembershipTierController::class, ['as' => 'admin']);
    Route::post('membership-tiers/{membershipTier}/toggle-status', [MembershipTierController::class, 'toggleStatus'])->name('admin.membership-tiers.toggle-status');
    Route::get('membership-tiers/{membershipTier}/members', [MembershipTierController::class, 'members'])->name('admin.membership-tiers.members');
    Route::get('membership-tiers/{membershipTier}/unions', [MembershipTierController::class, 'unions'])->name('admin.membership-tiers.unions');
    Route::get('membership-tiers/pricing', [MembershipTierController::class, 'getPricing'])->name('admin.membership-tiers.pricing');

    // Manual Payments Management
    Route::prefix('manual-payments')->name('admin.manual-payments.')->group(function () {
        Route::get('/', [ManualPaymentController::class, 'index'])->name('index');
        Route::get('/{manualPayment}', [ManualPaymentController::class, 'show'])->name('show');
        Route::post('/{manualPayment}/verify', [ManualPaymentController::class, 'verify'])->name('verify');
    });

    // Bank Accounts Management
    Route::prefix('bank-accounts')->name('admin.bank-accounts.')->group(function () {
        Route::get('/', [ManualPaymentController::class, 'bankAccounts'])->name('index');
        Route::post('/', [ManualPaymentController::class, 'storeBankAccount'])->name('store');
        Route::put('/{bankAccount}', [ManualPaymentController::class, 'updateBankAccount'])->name('update');
        Route::delete('/{bankAccount}', [ManualPaymentController::class, 'deleteBankAccount'])->name('destroy');
    });

    // Vouchers Management
    Route::prefix('vouchers')->name('admin.vouchers.')->group(function () {
        Route::get('/', [ManualPaymentController::class, 'vouchers'])->name('index');
        Route::post('/', [ManualPaymentController::class, 'issueVoucher'])->name('store');
        Route::get('/{voucher}', [ManualPaymentController::class, 'showVoucher'])->name('show');
        Route::post('/{voucher}/issue', [ManualPaymentController::class, 'markAsIssued'])->name('issue');
        Route::post('/{voucher}/collect', [ManualPaymentController::class, 'collectVoucher'])->name('collect');
    });
});

// routes/web.php
// Add this line to include admin routes

// Existing routes...


