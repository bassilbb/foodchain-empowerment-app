<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ReferralService;
use App\Services\TierProgressionService;
use App\Services\PaymentService;
use App\Services\InventoryService;
use App\Services\DistributionService;
use App\Services\NotificationService;
use App\Services\Images\BingImageSearchProvider;
use App\Services\Images\Contracts\ImageSearchProvider;
use App\Services\Images\NullImageSearchProvider;
use App\Services\Images\SerpApiImageSearchProvider;

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

        // Approved image-search API used by the packaging research workflow.
        // With no key configured it degrades to manual mode instead of scraping.
        $this->app->singleton(ImageSearchProvider::class, function () {
            return match (strtolower((string) config('image_research.provider'))) {
                'serpapi' => new SerpApiImageSearchProvider(
                    config('image_research.serpapi.key'),
                    (string) config('image_research.serpapi.endpoint'),
                    (string) config('image_research.serpapi.engine'),
                ),
                'bing' => new BingImageSearchProvider(
                    config('image_research.bing.key'),
                    (string) config('image_research.bing.endpoint'),
                ),
                default => new NullImageSearchProvider,
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share the signed-in customer's saved product ids with every product
        // card. Resolved once per request rather than per card, which would be
        // an N+1 across product listings (a 40-item grid issued 40 queries).
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
        // regardless of which controller rendered the view. Several listing
        // controllers (shop, category, brand, search, vendors…) never pass
        // $cartCount, so the badge fell back to `{{ $cartCount ?? 0 }}` and
        // only showed the true count after visiting a page whose controller
        // did pass it. Sharing it here keeps the badge accurate everywhere.
        \Illuminate\Support\Facades\View::composer('marketplace.layouts.app', function ($view) {
            $user = auth()->user();

            $view->with(
                'cartCount',
                $user ? app(\App\Services\Marketplace\CartService::class)->getCartCount($user) : 0
            );
        });
    }
}
