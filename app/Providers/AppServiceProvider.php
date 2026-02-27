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
        //
    }
}
