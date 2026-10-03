<?php

// routes/account.php
// Customer marketplace account dashboard — require'd from routes/web.php
//
// Every route here is scoped to the authenticated user. Ownership of records
// (orders, addresses, wishlist items) is enforced in the controllers, never in
// the route definition alone.

use App\Http\Controllers\Account\AddressController;
use App\Http\Controllers\Account\BecomeVendorController;
use App\Http\Controllers\Account\DashboardController;
use App\Http\Controllers\Account\NotificationController;
use App\Http\Controllers\Account\OrderController;
use App\Http\Controllers\Account\PaymentController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Account\WalletController;
use App\Http\Controllers\Account\WishlistController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('account')->name('account.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Orders. Ownership of each order is enforced in the controller with a
    // scoped check that returns 404, so id probes cannot enumerate records.
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');

        // The static /invoice segment must be declared before the {order} wildcard.
        Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');

        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::post('/{order}/confirm-delivery', [OrderController::class, 'confirmDelivery'])->name('confirm-delivery');
        Route::post('/{order}/reorder', [OrderController::class, 'reorder'])->name('reorder');
        Route::post('/{order}/request-return', [OrderController::class, 'requestReturn'])->name('request-return');
        Route::post('/{order}/items/{item}/review', [OrderController::class, 'review'])->name('review');
    });

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::delete('/avatar', [ProfileController::class, 'destroyAvatar'])->name('avatar.destroy');
    });

    // Delivery addresses
    Route::prefix('addresses')->name('addresses.')->group(function () {
        Route::get('/', [AddressController::class, 'index'])->name('index');
        Route::get('/create', [AddressController::class, 'create'])->name('create');
        Route::post('/', [AddressController::class, 'store'])->name('store');
        Route::get('/{address}/edit', [AddressController::class, 'edit'])->name('edit');
        Route::put('/{address}', [AddressController::class, 'update'])->name('update');
        Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
        Route::post('/{address}/default', [AddressController::class, 'setDefault'])->name('default');
    });

    // Wishlist
    Route::prefix('wishlist')->name('wishlist.')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('index');
        Route::post('/', [WishlistController::class, 'store'])->name('store');
        Route::delete('/{wishlist}', [WishlistController::class, 'destroy'])->name('destroy');
        Route::post('/remove', [WishlistController::class, 'destroyByProduct'])->name('remove-by-product');
        Route::post('/{wishlist}/move-to-cart', [WishlistController::class, 'moveToCart'])->name('move-to-cart');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/clear-all', [NotificationController::class, 'destroyAll'])->name('clear-all');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');

    // Become a vendor
    Route::prefix('become-vendor')->name('become-vendor.')->group(function () {
        Route::get('/', [BecomeVendorController::class, 'create'])->name('create');
        Route::post('/', [BecomeVendorController::class, 'store'])->name('store');
    });

    // Wallet
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('index');
        Route::get('/transactions', [WalletController::class, 'transactions'])->name('transactions');
    });
});
