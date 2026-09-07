<?php

// routes/marketplace.php
// Marketplace routes — require'd from routes/web.php

use App\Http\Controllers\Marketplace\BulkOrderController;
use App\Http\Controllers\Marketplace\CartController;
use App\Http\Controllers\Marketplace\CheckoutController;
use App\Http\Controllers\Marketplace\LoyaltyController;
use App\Http\Controllers\Marketplace\OrderController;
use App\Http\Controllers\Marketplace\ReviewController;
use App\Http\Controllers\Marketplace\ShopController;
use App\Http\Controllers\Marketplace\VendorController;
use App\Http\Controllers\Marketplace\WalletController;
use Illuminate\Support\Facades\Route;

// Public browsing (no auth required)
Route::prefix('shop')->name('marketplace.')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('home');
    Route::get('/all', [ShopController::class, 'shop'])->name('shop');
    Route::get('/flash-sale', [ShopController::class, 'flashSale'])->name('flash-sale');
    Route::get('/search', [ShopController::class, 'search'])->name('search');
    Route::get('/autocomplete', [ShopController::class, 'autocomplete'])->name('autocomplete');
    Route::get('/state/{state}', [ShopController::class, 'setState'])->name('set-state');
    Route::get('/vendors', [ShopController::class, 'vendors'])->name('vendors');
    Route::get('/vendors/{vendor:slug}', [ShopController::class, 'vendor'])->name('vendor');
    Route::get('/brand/{brand:slug}', [ShopController::class, 'brand'])->name('brand');
    Route::get('/category/{category:slug}', [ShopController::class, 'category'])->name('category');
    Route::get('/product/{product:slug}', [ShopController::class, 'show'])->name('product');
    Route::get('/browse', fn () => redirect()->route('marketplace.shop'))->name('browse');
});

// Authenticated marketplace routes (active membership required to buy)
Route::middleware(['auth', 'membership'])->prefix('marketplace')->name('marketplace.')->group(function () {
    // Cart
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::post('/update', [CartController::class, 'update'])->name('update');
        Route::post('/remove', [CartController::class, 'remove'])->name('remove');
        Route::post('/save-for-later', [CartController::class, 'saveForLater'])->name('save-for-later');
        Route::post('/move-to-cart', [CartController::class, 'moveToCart'])->name('move-to-cart');
        Route::get('/count', [CartController::class, 'count'])->name('count');
    });

    // Checkout
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/', [CheckoutController::class, 'store'])->name('store');
        Route::post('/estimate', [CheckoutController::class, 'estimate'])->name('estimate');
        Route::post('/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('apply-coupon');
    });

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::post('/{order}/confirm-delivery', [OrderController::class, 'confirmDelivery'])->name('confirm-delivery');
        Route::post('/{order}/reorder', [OrderController::class, 'reorder'])->name('reorder');
        Route::post('/{order}/request-return', [OrderController::class, 'requestReturn'])->name('request-return');
        Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');
        Route::post('/{order}/items/{item}/review', [OrderController::class, 'review'])->name('review');
    });

    // Wallet
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('index');
        Route::get('/transactions', [WalletController::class, 'transactions'])->name('transactions');
        Route::post('/redeem-points', [WalletController::class, 'redeemPoints'])->name('redeem-points');
    });

    // Loyalty
    Route::prefix('loyalty')->name('loyalty.')->group(function () {
        Route::get('/', [LoyaltyController::class, 'index'])->name('index');
        Route::post('/redeem', [LoyaltyController::class, 'redeem'])->name('redeem');
    });

    // Reviews
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::post('/product/{product}', [ReviewController::class, 'store'])->name('store');
    });

    // Community Bulk Orders
    Route::prefix('bulk')->name('bulk.')->group(function () {
        Route::get('/', [BulkOrderController::class, 'index'])->name('index');
        Route::get('/create', [BulkOrderController::class, 'create'])->name('create');
        Route::post('/', [BulkOrderController::class, 'store'])->name('store');
        Route::get('/{bulkOrder}', [BulkOrderController::class, 'show'])->name('show');
        Route::post('/{bulkOrder}/contribute', [BulkOrderController::class, 'contribute'])->name('contribute');
    });

    // Vendor dashboard
    Route::prefix('vendor')->name('vendor.')->group(function () {
        Route::get('/', [VendorController::class, 'dashboard'])->name('dashboard');
        Route::get('/products', [VendorController::class, 'products'])->name('products');
        Route::get('/products/create', [VendorController::class, 'productCreate'])->name('products.create');
        Route::post('/products', [VendorController::class, 'productStore'])->name('products.store');
        Route::get('/products/{product}/edit', [VendorController::class, 'productEdit'])->name('products.edit');
        Route::put('/products/{product}', [VendorController::class, 'productUpdate'])->name('products.update');
        Route::delete('/products/{product}', [VendorController::class, 'deleteProduct'])->name('products.delete');
        Route::get('/inventory', [VendorController::class, 'inventory'])->name('inventory');
        Route::post('/inventory/{product}/adjust', [VendorController::class, 'adjustStock'])->name('inventory.adjust');
        Route::get('/orders', [VendorController::class, 'orders'])->name('orders');
        Route::post('/orders/{item}/status', [VendorController::class, 'updateItemStatus'])->name('orders.status');
        Route::get('/packing-slips', [VendorController::class, 'packingSlips'])->name('packing-slips');
        Route::get('/reports', [VendorController::class, 'reports'])->name('reports');
    });
});
