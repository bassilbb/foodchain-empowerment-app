<?php

// routes/admin.php
// Create this new file for admin routes

use App\Http\Controllers\Admin\AccountClearanceController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DistributionController;
use App\Http\Controllers\Admin\FoodAssistanceController;
use App\Http\Controllers\Admin\FoodAssistancePlanTierController;
use App\Http\Controllers\Admin\FoodItemController;
use App\Http\Controllers\Admin\FoodLoanController;
use App\Http\Controllers\Admin\FoodSupportController;
use App\Http\Controllers\Admin\IncidentController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\IssuanceController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ManualPaymentController;
use App\Http\Controllers\Admin\MembershipTierController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PageContentController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SavingsPlanController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Guest routes (not authenticated)
Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
});

// Authenticated admin routes
Route::middleware(['admin'])->group(function () {
    // Logout (all roles)
    Route::match(['GET', 'POST'], '/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Dashboard (all roles)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // User Management (all roles)
    Route::prefix('users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/referrals', [UserController::class, 'referrals'])->name('referrals');
        Route::post('/{user}/activate', [UserController::class, 'activate'])->name('activate');
        Route::post('/{user}/deactivate', [UserController::class, 'deactivate'])->name('deactivate');
        Route::post('/{user}/upgrade-tier', [UserController::class, 'upgradeTier'])->name('upgrade-tier');
        Route::post('/{user}/update-tier', [UserController::class, 'updateTier'])->name('update-tier');
        Route::get('/export/excel', [UserController::class, 'exportExcel'])->name('export.excel');
    });

    // --- Operations (super_admin + inventory_manager) ---
    Route::middleware('role:super_admin,inventory_manager')->group(function () {
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
            Route::get('/package-items/{package}', [DistributionController::class, 'packageItems'])->name('package-items');
            Route::get('/{distribution}/edit', [DistributionController::class, 'edit'])->name('edit');
            Route::put('/{distribution}', [DistributionController::class, 'update'])->name('update');
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

        // Issuance Management (voucher-based collection lookup)
        Route::prefix('issuance')->name('admin.issuance.')->group(function () {
            Route::get('/', [IssuanceController::class, 'index'])->name('index');
            Route::match(['GET', 'POST'], '/lookup', [IssuanceController::class, 'lookup'])->name('lookup');
            Route::post('/{voucher}/link-distribution', [IssuanceController::class, 'linkDistribution'])->name('link-distribution');
            Route::get('/{voucher}/link-distribution', function (App\Models\Voucher $voucher) {
                return redirect()->route('admin.issuance.lookup', ['search' => $voucher->collection_code ?? $voucher->voucher_number]);
            })->name('link-distribution.get');
            Route::post('/{voucher}/collect', [IssuanceController::class, 'collect'])->name('collect');
            Route::get('/{voucher}/collect', function (App\Models\Voucher $voucher) {
                return redirect()->route('admin.issuance.lookup', ['search' => $voucher->collection_code ?? $voucher->voucher_number]);
            })->name('collect.get');
        });
    });

    // --- Support (super_admin + customer_support) ---
    Route::middleware('role:super_admin,customer_support')->group(function () {
        // Support Tickets
        Route::prefix('support')->name('admin.support.')->group(function () {
            Route::get('/', [SupportController::class, 'index'])->name('index');
            Route::get('/{ticket}', [SupportController::class, 'show'])->name('show');
            Route::post('/{ticket}/reply', [SupportController::class, 'reply'])->name('reply');
            Route::post('/{ticket}/assign', [SupportController::class, 'assign'])->name('assign');
            Route::post('/{ticket}/resolve', [SupportController::class, 'resolve'])->name('resolve');
            Route::post('/{ticket}/close', [SupportController::class, 'close'])->name('close');
        });

        // Incident Management
        Route::prefix('incidents')->name('admin.incidents.')->group(function () {
            Route::get('/', [IncidentController::class, 'index'])->name('index');
            Route::get('/create', [IncidentController::class, 'create'])->name('create');
            Route::post('/', [IncidentController::class, 'store'])->name('store');
            Route::get('/{incident}', [IncidentController::class, 'show'])->name('show');
            Route::get('/{incident}/edit', [IncidentController::class, 'edit'])->name('edit');
            Route::put('/{incident}', [IncidentController::class, 'update'])->name('update');
            Route::delete('/{incident}', [IncidentController::class, 'destroy'])->name('destroy');
            Route::post('/{incident}/assign', [IncidentController::class, 'assign'])->name('assign');
            Route::post('/{incident}/escalate', [IncidentController::class, 'escalate'])->name('escalate');
            Route::post('/escalations/{escalation}/acknowledge', [IncidentController::class, 'acknowledgeEscalation'])->name('escalation.acknowledge');
            Route::post('/{incident}/resolve', [IncidentController::class, 'resolve'])->name('resolve');
            Route::post('/{incident}/close', [IncidentController::class, 'close'])->name('close');
            Route::post('/{incident}/reopen', [IncidentController::class, 'reopen'])->name('reopen');
            Route::post('/{incident}/comment', [IncidentController::class, 'addComment'])->name('comment');
        });
    });

    // --- Super Admin only ---
    Route::middleware('role:super_admin')->group(function () {
        // Payment Management
        Route::prefix('payments')->name('admin.payments.')->group(function () {
            Route::get('/', [PaymentController::class, 'index'])->name('index');
            Route::get('/export', [PaymentController::class, 'export'])->name('export');
            Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
            Route::post('/{payment}/verify', [PaymentController::class, 'verify'])->name('verify');
        });

        // Settings
        Route::prefix('settings')->name('admin.settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::post('/', [SettingController::class, 'update'])->name('update');
            Route::post('/role-permissions', [SettingController::class, 'updateRolePermissions'])->name('update-role-permissions');
            Route::post('/logo', [SettingController::class, 'updateLogo'])->name('update-logo');
            Route::post('/logo/delete', [SettingController::class, 'deleteLogo'])->name('delete-logo');
        });

        // Page Content Management
        Route::prefix('page-contents')->name('admin.page-contents.')->group(function () {
            Route::get('/', [PageContentController::class, 'index'])->name('index');
            Route::get('/{page}/edit', [PageContentController::class, 'edit'])->name('edit');
            Route::put('/{page}', [PageContentController::class, 'update'])->name('update');
            Route::put('/{page}/toggle', [PageContentController::class, 'toggle'])->name('toggle');
            Route::post('/seed', [PageContentController::class, 'seed'])->name('seed');
        });

        // Reports
        Route::prefix('reports')->name('admin.reports.')->group(function () {
            Route::get('/overview', [ReportController::class, 'overview'])->name('overview');
            Route::get('/users', [ReportController::class, 'users'])->name('users');
            Route::get('/payments', [ReportController::class, 'payments'])->name('payments');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
            Route::get('/distributions', [ReportController::class, 'distributions'])->name('distributions');
            Route::get('/distributions/export', [ReportController::class, 'exportDistributions'])->name('distributions.export');
        });

        // Admin User Management
        Route::resource('admin-users', AdminUserController::class, ['as' => 'admin']);

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
            Route::get('/{manualPayment}/verify', [ManualPaymentController::class, 'verifyForm'])->name('verify.form');
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

        // Branches Management
        Route::resource('branches', BranchController::class, ['as' => 'admin']);

        // Departments Management
        Route::resource('departments', DepartmentController::class, ['as' => 'admin']);
        Route::get('departments-by-branch/{branch}', [DepartmentController::class, 'getByBranch'])->name('admin.departments.by-branch');

        // Staff Management
        Route::resource('staff', StaffController::class, ['as' => 'admin']);

        // Food Loans Management
        Route::prefix('food-loans')->name('admin.food-loans.')->group(function () {
            Route::get('/', [FoodLoanController::class, 'index'])->name('index');
            Route::get('/eligibility-rules', [FoodLoanController::class, 'eligibilityRules'])->name('eligibility-rules');
            Route::post('/eligibility-rules/batch', [FoodLoanController::class, 'updateBatchRules'])->name('eligibility-rules.batch');
            Route::post('/eligibility-rules/{ruleKey}', [FoodLoanController::class, 'updateEligibilityRule'])->name('eligibility-rules.update');
            Route::post('/eligibility-rules/{ruleKey}/toggle', [FoodLoanController::class, 'toggleRule'])->name('eligibility-rules.toggle');
            Route::post('/check-eligibility', [FoodLoanController::class, 'checkEligibility'])->name('check-eligibility');
            Route::post('/{foodLoan}/approve', [FoodLoanController::class, 'approve'])->name('approve');
            Route::post('/{foodLoan}/disburse', [FoodLoanController::class, 'disburse'])->name('disburse');
            Route::post('/{foodLoan}/reject', [FoodLoanController::class, 'reject'])->name('reject');
            Route::patch('/repayments/{repayment}', [FoodLoanController::class, 'updateRepayment'])->name('repayments.update');
            Route::get('/{foodLoan}', [FoodLoanController::class, 'show'])->name('show');
        });

        // Food Assistance Plan Tiers
        Route::prefix('food-assistance-tiers')->name('admin.food-assistance-tiers.')->group(function () {
            Route::get('/', [FoodAssistancePlanTierController::class, 'index'])->name('index');
            Route::get('/create', [FoodAssistancePlanTierController::class, 'create'])->name('create');
            Route::post('/', [FoodAssistancePlanTierController::class, 'store'])->name('store');
            Route::get('/{tier}/edit', [FoodAssistancePlanTierController::class, 'edit'])->name('edit');
            Route::put('/{tier}', [FoodAssistancePlanTierController::class, 'update'])->name('update');
            Route::delete('/{tier}', [FoodAssistancePlanTierController::class, 'destroy'])->name('destroy');
        });

        // Food Assistance Plans
        Route::prefix('food-assistance')->name('admin.food-assistance.')->group(function () {
            Route::get('/', [FoodAssistanceController::class, 'index'])->name('index');
            Route::get('/{assistancePlan}', [FoodAssistanceController::class, 'show'])->name('show');
            Route::post('/{assistancePlan}/approve', [FoodAssistanceController::class, 'approve'])->name('approve');
            Route::post('/{assistancePlan}/mark-collected', [FoodAssistanceController::class, 'markCollected'])->name('mark-collected');
            Route::post('/{assistancePlan}/complete', [FoodAssistanceController::class, 'markCompleted'])->name('complete');
            Route::post('/{assistancePlan}/cancel', [FoodAssistanceController::class, 'cancel'])->name('cancel');
        });

        // Food Support Plans
        Route::prefix('food-support')->name('admin.food-support.')->group(function () {
            Route::get('/', [FoodSupportController::class, 'index'])->name('index');
            Route::get('/{supportPlan}', [FoodSupportController::class, 'show'])->name('show');
            Route::post('/{supportPlan}/mark-matured', [FoodSupportController::class, 'markMatured'])->name('mark-matured');
            Route::post('/{supportPlan}/withdraw', [FoodSupportController::class, 'processWithdrawal'])->name('withdraw');
            Route::post('/{supportPlan}/cancel', [FoodSupportController::class, 'cancel'])->name('cancel');
        });

        // Savings Plans Management
        Route::resource('savings-plans', SavingsPlanController::class, ['as' => 'admin'])->except(['show']);
        Route::post('savings-plans/{option}/toggle', [SavingsPlanController::class, 'toggleOption'])->name('admin.savings-plans.toggle-option');

        // Account Clearance Management
        Route::prefix('clearances')->name('admin.clearances.')->group(function () {
            Route::get('/', [AccountClearanceController::class, 'index'])->name('index');
            Route::post('/batch-approve', [AccountClearanceController::class, 'batchApprove'])->name('batch-approve');
            Route::get('/batch-approve', function () {
                return redirect()->route('admin.clearances.index');
            })->name('batch-approve.get');
            Route::get('/{clearance}', [AccountClearanceController::class, 'show'])->name('show');
            Route::post('/{clearance}/approve', [AccountClearanceController::class, 'approve'])->name('approve');
            Route::get('/{clearance}/approve', function ($clearance) {
                return redirect()->route('admin.clearances.show', $clearance);
            })->name('approve.get');
            Route::post('/{clearance}/reject', [AccountClearanceController::class, 'reject'])->name('reject');
            Route::get('/{clearance}/reject', function ($clearance) {
                return redirect()->route('admin.clearances.show', $clearance);
            })->name('reject.get');
        });
    });
});

// routes/web.php
// Add this line to include admin routes

// Existing routes...
