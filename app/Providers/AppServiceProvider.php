<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ReferralService;
use App\Services\TierProgressionService;
use App\Services\PaymentService;
use App\Services\InventoryService;
use App\Services\DistributionService;
use App\Services\NotificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register services as singletons
        $this->app->singleton(ReferralService::class);
        $this->app->singleton(TierProgressionService::class);
        $this->app->singleton(PaymentService::class);
        $this->app->singleton(InventoryService::class);
        $this->app->singleton(DistributionService::class);
        $this->app->singleton(NotificationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share the signed-in customer's saved product ids with every product
        // card (resolved once per request to avoid an N+1 across listings).
        \Illuminate\Support\Facades\View::composer('marketplace.partials.product-card', function ($view) {
            static $ids = null;

            if ($ids === null) {
                $ids = auth()->check()
                    ? \App\Models\Marketplace\SavedProduct::where('user_id', auth()->id())
                        ->pluck('product_id')
                        ->all()
                    : [];
            }

            $view->with('savedProductIds', $ids);
        });

        // Always provide a live cart count to the marketplace layout header,
        // regardless of which controller rendered the view.
        \Illuminate\Support\Facades\View::composer('marketplace.layouts.app', function ($view) {
            $user = auth()->user();

            $view->with(
                'cartCount',
                $user ? app(\App\Services\Marketplace\CartService::class)->getCartCount($user) : 0
            );
        });
    }
}
