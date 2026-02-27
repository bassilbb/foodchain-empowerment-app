<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ReferralController;
// use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\DistributionController as UserDistributionController;
use App\Http\Controllers\User\PaymentController as UserPaymentController;
use App\Http\Controllers\User\SupportController as UserSupportController;
use App\Http\Controllers\User\ManualPaymentController;
// use App\Http\Controllers\LandingController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::view('/', 'landing')->name('landing');
Route::view('/gallery', 'gallary')->name('gallery');
Route::view('/team', 'team')->name('team');
Route::view('/sponsor', 'sponsor')->name('sponsor');
Route::view('/supplier', 'supplier')->name('supplier');
Route::view('/rider', 'rider')->name('rider');
Route::view('/blog', 'blog')->name('blog');
Route::view('/blog_details', 'blog_details')->name('blog_details');

// Landing Page
// Route::get('/', [LandingController::class, 'index'])->name('home');

// Guest Routes (Not authenticated)
Route::middleware('guest')->group(function () {
    // Registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// Payment Callback (public)
Route::get('/payment/callback', [UserPaymentController::class, 'handleCallback'])->name('payment.callback');

// Authenticated User Routes
Route::middleware(['auth', 'membership'])->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Referrals
    Route::prefix('referrals')->name('referrals.')->group(function () {
        Route::get('/', [ReferralController::class, 'index'])->name('index');
        Route::get('/tree', [ReferralController::class, 'tree'])->name('tree');
    });

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    });

    // Distributions
    Route::get('/distributions', [UserDistributionController::class, 'index'])->name('distributions.index');
    Route::get('/distributions/{distribution}', [UserDistributionController::class, 'show'])->name('distributions.show');

    // Payments & Transactions
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [UserPaymentController::class, 'index'])->name('index');
        Route::get('/renew', [UserPaymentController::class, 'showRenewalForm'])->name('renew');
        Route::post('/renew', [UserPaymentController::class, 'initiateRenewal'])->name('renew.post');
    });

    // Support
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [UserSupportController::class, 'index'])->name('index');
        Route::get('/create', [UserSupportController::class, 'create'])->name('create');
        Route::post('/', [UserSupportController::class, 'store'])->name('store');
        Route::get('/{ticket}', [UserSupportController::class, 'show'])->name('show');
        Route::post('/{ticket}/reply', [UserSupportController::class, 'reply'])->name('reply');
    });

    // Manual Payments
    Route::prefix('manual-payments')->name('manual-payments.')->group(function () {
        Route::get('/', [ManualPaymentController::class, 'index'])->name('index');
        Route::get('/create', [ManualPaymentController::class, 'create'])->name('create');
        Route::post('/', [ManualPaymentController::class, 'store'])->name('store');
        Route::get('/{manualPayment}', [ManualPaymentController::class, 'show'])->name('show');
    });

    // Notifications
    Route::post('/notifications/{notification}/read', function($id) {
        auth()->user()->notifications()->where('id', $id)->update(['is_read' => true, 'read_at' => now()]);
        return back();
    })->name('notifications.read');
});

// Membership Renewal (allow even if expired)
Route::middleware('auth')->group(function () {
    Route::get('/membership/renew', [UserPaymentController::class, 'showRenewalForm'])->name('membership.renew');
    Route::post('/membership/renew', [UserPaymentController::class, 'initiateRenewal'])->name('membership.renew.post');
});


// Admin routes - prefix with /admin
Route::prefix('admin')->group(base_path('routes/admin.php'));

require __DIR__.'/auth.php';
