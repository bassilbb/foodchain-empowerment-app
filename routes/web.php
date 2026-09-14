<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisterController;
// use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\AccountController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\DistributionController as UserDistributionController;
use App\Http\Controllers\User\FoodAssistanceController;
// use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\FoodSupportController;
use App\Http\Controllers\User\IncidentReportController;
use App\Http\Controllers\User\ManualPaymentController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\PaymentController as UserPaymentController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\QuarterlyBenefitController;
use App\Http\Controllers\User\ReferralController;
use App\Http\Controllers\User\SupportController as UserSupportController;
use App\Http\Controllers\User\UnionController;
use App\Http\Controllers\User\VoucherController as UserVoucherController;
use Illuminate\Support\Facades\Route;

// use App\Http\Controllers\LandingController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::view('/', 'landing')->name('landing');
Route::view('/gallery', 'gallery')->name('gallery');
Route::view('/team', 'team')->name('team');
Route::view('/sponsor', 'sponsor')->name('sponsor');
Route::view('/supplier', 'supplier')->name('supplier');
Route::view('/rider', 'rider')->name('rider');
Route::view('/listing', 'listing')->name('listing');
Route::view('/blog', 'blog')->name('blog');
Route::view('/blog_details', 'blog_details')->name('blog_details');
Route::view('/csr', 'csr')->name('csr');
Route::view('/franchise', 'franchice')->name('franchise');
Route::view('/franchice', 'franchice');

// Informational / legal pages
foreach ([
    'privacy' => 'pages.privacy',
    'terms' => 'pages.terms',
    'cookies' => 'pages.cookies',
    'compliance' => 'pages.compliance',
    'help' => 'pages.help',
    'faq' => 'pages.faq',
    'community' => 'pages.community',
    'affiliate' => 'pages.affiliate',
] as $path => $name) {
    Route::view('/'.$path, 'pages.info', ['page' => $path])->name($name);
}

// Landing Page Forms
use App\Http\Controllers\LandingController;

Route::post('/sponsor', [LandingController::class, 'sponsor'])->name('landing.sponsor');
Route::post('/supplier', [LandingController::class, 'supplier'])->name('landing.supplier');
Route::post('/rider', [LandingController::class, 'rider'])->name('landing.rider');

// Guest Routes (Not authenticated)
Route::middleware('guest')->group(function () {
    // Registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    // Password Reset
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// Logout (accessible to authenticated users)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Email Verification (authenticated) — redirected as app doesn't use email verification
Route::middleware('auth')->group(function () {
    Route::get('/verify-email', fn () => redirect()->route('dashboard'))->name('verification.notice');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
});

// Payment Callback (public)
Route::get('/payment/callback', [UserPaymentController::class, 'handleCallback'])->name('payment.callback');

// Pending Payment (after registration)
Route::get('/payment/pending', [UserPaymentController::class, 'pending'])->name('payment.pending');
Route::post('/payment/manual', [UserPaymentController::class, 'submitManualPayment'])->name('payment.manual');
Route::post('/payment/pay-with-card', [UserPaymentController::class, 'payWithCard'])->name('payment.pay-with-card');

// Paystack Webhook (no CSRF protection - exempted in bootstrap/app.php)
Route::post('/paystack/webhook', [UserPaymentController::class, 'handleWebhook'])->name('paystack.webhook');

// Authenticated User Routes
Route::middleware(['auth', 'membership'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Linked Accounts
    Route::prefix('accounts')->name('user.accounts.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::get('/clearances', [AccountController::class, 'clearances'])->name('clearances');
        Route::get('/create', [AccountController::class, 'create'])->name('create');
        Route::post('/', [AccountController::class, 'store'])->name('store');
        Route::get('/wallet', [AccountController::class, 'wallet'])->name('wallet');
        Route::post('/wallet/top-up', [AccountController::class, 'submitTopUp'])->name('top-up');
        Route::get('/transactions', [AccountController::class, 'transactions'])->name('transactions');
        Route::get('/{account}', [AccountController::class, 'show'])->name('show');
        Route::post('/clearances/batch-pay', [AccountController::class, 'payBatchClearanceFee'])->name('clearance-batch-pay');
        Route::post('/{account}/clearance/pay', [AccountController::class, 'payClearanceFee'])->name('clearance-pay');
        Route::get('/{account}/transactions', [AccountController::class, 'transactions'])->name('account-transactions');
    });

    Route::post('/switch-account/{id}', [AccountController::class, 'switchAccount'])->name('switch-account');

    // Referrals
    Route::prefix('referrals')->name('referrals.')->group(function () {
        Route::get('/', [ReferralController::class, 'index'])->name('index');
        Route::get('/tree', [ReferralController::class, 'tree'])->name('tree');
    });

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/update', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::delete('/destroy', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Distributions
    Route::get('/distributions', [UserDistributionController::class, 'index'])->name('distributions.index');
    Route::get('/distributions/{distribution}', [UserDistributionController::class, 'show'])->name('distributions.show');

    // Vouchers
    Route::prefix('vouchers')->name('vouchers.')->group(function () {
        Route::get('/', [UserVoucherController::class, 'index'])->name('index');
        Route::get('/{voucher}', [UserVoucherController::class, 'show'])->name('show');
        Route::post('/{voucher}/acknowledge', [UserVoucherController::class, 'acknowledge'])->name('acknowledge');
    });

    // Payments & Transactions
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [UserPaymentController::class, 'index'])->name('index');
        Route::get('/renew', [UserPaymentController::class, 'showRenewalForm'])->name('renew');
        Route::post('/renew', [UserPaymentController::class, 'initiateRenewal'])->name('renew.post');
        Route::get('/initiate', [UserPaymentController::class, 'initiatePayment'])->name('initiate');
    });

    // Support
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [UserSupportController::class, 'index'])->name('index');
        Route::get('/create', [UserSupportController::class, 'create'])->name('create');
        Route::post('/', [UserSupportController::class, 'store'])->name('store');
        Route::get('/{ticket}', [UserSupportController::class, 'show'])->name('show');
        Route::post('/{ticket}/reply', [UserSupportController::class, 'reply'])->name('reply');
        Route::post('/{ticket}/close', [UserSupportController::class, 'close'])->name('close');
    });

    // User Incidents
    Route::prefix('incidents')->name('user.incidents.')->group(function () {
        Route::get('/', [IncidentReportController::class, 'index'])->name('index');
        Route::get('/create', [IncidentReportController::class, 'create'])->name('create');
        Route::post('/', [IncidentReportController::class, 'store'])->name('store');
        Route::get('/{incident}', [IncidentReportController::class, 'show'])->name('show');
        Route::post('/{incident}/comment', [IncidentReportController::class, 'addComment'])->name('comment');
    });

    // Manual Payments
    Route::prefix('manual-payments')->name('manual-payments.')->group(function () {
        Route::get('/', [ManualPaymentController::class, 'index'])->name('index');
        Route::get('/create', [ManualPaymentController::class, 'create'])->name('create');
        Route::post('/', [ManualPaymentController::class, 'store'])->name('store');
        Route::get('/{manualPayment}', [ManualPaymentController::class, 'show'])->name('show');
    });

    // Unions
    Route::prefix('unions')->name('unions.')->group(function () {
        Route::get('/', [UnionController::class, 'index'])->name('index');
        Route::get('/create', [UnionController::class, 'create'])->name('create');
        Route::post('/', [UnionController::class, 'store'])->name('store');
        Route::get('/{id}', [UnionController::class, 'show'])->name('show');
        Route::post('/join', [UnionController::class, 'join'])->name('join');
        Route::post('/{id}/leave', [UnionController::class, 'leave'])->name('leave');
        Route::post('/{id}/apply-loan', [UnionController::class, 'applyLoan'])->name('apply-loan');
        Route::post('/{id}/repayment', [UnionController::class, 'makeRepayment'])->name('repayment');
        Route::get('/{id}/loan', [UnionController::class, 'getLoanDetails'])->name('loan-details');
        Route::get('/{id}/repayment-schedule', [UnionController::class, 'repaymentSchedule'])->name('repayment-schedule');
    });

    // Quarterly Benefits
    Route::prefix('quarterly-benefits')->name('quarterly-benefits.')->group(function () {
        Route::get('/', [QuarterlyBenefitController::class, 'index'])->name('index');
        Route::get('/{id}', [QuarterlyBenefitController::class, 'show'])->name('show');
        Route::post('/{id}/claim', [QuarterlyBenefitController::class, 'claim'])->name('claim');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // Food Assistance Plan
    Route::prefix('food-assistance')->name('user.food-assistance.')->group(function () {
        Route::get('/', [FoodAssistanceController::class, 'index'])->name('index');
        Route::get('/apply', [FoodAssistanceController::class, 'create'])->name('create');
        Route::post('/', [FoodAssistanceController::class, 'store'])->name('store');
        Route::get('/{assistancePlan}', [FoodAssistanceController::class, 'show'])->name('show');
        Route::post('/{assistancePlan}/pay-processing-fee', [FoodAssistanceController::class, 'payProcessingFee'])->name('pay-processing-fee');
        Route::post('/{assistancePlan}/pay-first-installment', [FoodAssistanceController::class, 'payFirstInstallment'])->name('pay-first-installment');
        Route::post('/{assistancePlan}/pay-weekly-installment', [FoodAssistanceController::class, 'payWeeklyInstallment'])->name('pay-weekly-installment');
    });

    // Food Support Plan
    Route::prefix('food-support')->name('user.food-support.')->group(function () {
        Route::get('/', [FoodSupportController::class, 'index'])->name('index');
        Route::get('/subscribe', [FoodSupportController::class, 'create'])->name('create');
        Route::post('/', [FoodSupportController::class, 'store'])->name('store');
        Route::get('/{supportPlan}', [FoodSupportController::class, 'show'])->name('show');
        Route::post('/{supportPlan}/contribute', [FoodSupportController::class, 'makeContribution'])->name('contribute');
    });
});

// Membership Renewal (allow even if expired)
Route::middleware('auth')->group(function () {
    Route::get('/membership/renew', [UserPaymentController::class, 'showRenewalForm'])->name('membership.renew');
    Route::post('/membership/renew', [UserPaymentController::class, 'initiateRenewal'])->name('membership.renew.post');
});

// Serve logo/favicon files from uploads/ or storage/app/public/ (no symlink needed)
Route::get('/serve-logo/{path}', function (string $path) {
    $file = public_path('uploads/'.$path);
    if (file_exists($file)) {
        return response()->file($file);
    }
    $file = storage_path('app/public/'.$path);
    if (file_exists($file)) {
        return response()->file($file);
    }
    abort(404);
})->where('path', '.*');

// Marketplace routes
require __DIR__.'/marketplace.php';

// Admin routes - prefix with /admin
Route::prefix('admin')->group(base_path('routes/admin.php'));

require __DIR__.'/auth.php';
