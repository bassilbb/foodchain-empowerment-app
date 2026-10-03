<?php

// routes/vendor.php
// Vendor Centre — require'd from routes/web.php
//
// All routes are behind `auth` + `vendor` middleware. Ownership of every
// product / order item / payout is enforced in the controllers with a 404
// guard, so an id probe cannot confirm another vendor's record exists.

use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\OrderController;
use App\Http\Controllers\Vendor\PayoutController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('delete');
        Route::post('/{product}/toggle', [ProductController::class, 'toggleActive'])->name('toggle');
        Route::post('/{product}/stock', [ProductController::class, 'adjustStock'])->name('stock');
        Route::delete('/{product}/images/{image}', [ProductController::class, 'destroyImage'])->name('images.destroy');
    });

    // Orders (allocated items only)
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/packing-slips', [OrderController::class, 'packingSlips'])->name('packing-slips');
        Route::post('/{item}/status', [OrderController::class, 'updateStatus'])->name('status');
    });

    // Inventory
    Route::get('/inventory', [ProductController::class, 'index'])->name('inventory');

    // Analytics
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');

    // Payouts / earnings
    Route::prefix('payouts')->name('payouts.')->group(function () {
        Route::get('/', [PayoutController::class, 'index'])->name('index');
        Route::post('/', [PayoutController::class, 'store'])->name('store');
        Route::post('/{payout}/cancel', [PayoutController::class, 'cancel'])->name('cancel');
    });

    // Store settings / public profile
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'show'])->name('index');
        Route::put('/', [SettingsController::class, 'update'])->name('update');
    });
});
