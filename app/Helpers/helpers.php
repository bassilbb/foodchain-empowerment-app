<?php

if (! function_exists('format_currency')) {
    /**
     * Format amount as Nigerian Naira
     */
    function format_currency($amount): string
    {
        return '₦'.number_format($amount ?? 0, 2);
    }
}

if (! function_exists('setting')) {
    /**
     * Get setting value
     */
    function setting(string $key, $default = null)
    {
        return \App\Models\Setting::get($key, $default);
    }
}

if (! function_exists('site_logo')) {
    function site_logo(): ?string
    {
        $logo = \App\Models\Setting::where('key', 'site_logo')->first();
        if ($logo && $logo->value) {
            return url('serve-logo/'.$logo->value);
        }

        return null;
    }
}

if (! function_exists('site_favicon')) {
    function site_favicon(): ?string
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('settings')) {
            return null;
        }
        $favicon = \App\Models\Setting::where('key', 'site_favicon')->first();
        if ($favicon && $favicon->value) {
            return url('serve-logo/'.$favicon->value);
        }

        return null;
    }
}

if (! function_exists('app_name')) {
    /**
     * Get application name
     */
    function app_name(): string
    {
        return \App\Models\Setting::get('app_name', 'FoodChain');
    }
}

if (! function_exists('generate_referral_link')) {
    /**
     * Generate referral link for user
     */
    function generate_referral_link(string $code): string
    {
        return url("/register?ref={$code}");
    }
}

if (! function_exists('admin_can')) {
    /**
     * Check if admin has permission
     */
    function admin_can($permission): bool
    {
        try {
            $admin = auth()->guard('admin')->user();

            return $admin && method_exists($admin, 'hasPermission') ? $admin->hasPermission($permission) : false;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (! function_exists('module_enabled')) {
    /**
     * Check if a user-facing module is enabled
     */
    function module_enabled(string $module): bool
    {
        return filter_var(\App\Models\Setting::get('module_'.$module, 'true'), FILTER_VALIDATE_BOOLEAN);
    }
}

if (! function_exists('tier_badge_color')) {
    /**
     * Get color class for tier badge
     */
    function tier_badge_color($tierSlug): string
    {
        return match ($tierSlug ?? 'bronze') {
            'bronze' => 'bg-orange-600',
            'silver' => 'bg-gray-400',
            'gold' => 'bg-yellow-500',
            'emerald' => 'bg-green-600',
            default => 'bg-gray-500',
        };
    }
}

if (! function_exists('vite_built_asset_url')) {
    /**
     * Resolve the URL of a Vite-managed asset as a host-relative path
     * (e.g. "/build/assets/app-abc123.css") so it renders correctly under
     * any host/scheme (localhost, preview domains behind a reverse proxy,
     * https mixed-content avoidance). Falls back to the framework Vite URL
     * when the hot/dev server is running.
     */
    function vite_built_asset_url(string $path): ?string
    {
        if (is_file(public_path('hot'))) {
            return \Illuminate\Support\Facades\Vite::asset($path);
        }

        $manifestPath = public_path('build/manifest.json');
        if (! is_file($manifestPath)) {
            return null;
        }

        $manifest = json_decode((string) file_get_contents($manifestPath), true);
        $entry = $manifest[$path] ?? null;

        return $entry ? '/build/'.$entry['file'] : null;
    }
}

if (! function_exists('mp_pricing')) {
    function mp_pricing(): \App\Services\Marketplace\MarketplacePricingService
    {
        return app(\App\Services\Marketplace\MarketplacePricingService::class);
    }
}

if (! function_exists('mp_state')) {
    /** Current storefront pricing state for the visitor. */
    function mp_state(): string
    {
        return mp_pricing()->currentState();
    }
}

if (! function_exists('mp_price')) {
    /** Effective price of a product in the current (or given) storefront state. */
    function mp_price($product, ?string $state = null): float
    {
        return mp_pricing()->priceFor($product, $state);
    }
}

if (! function_exists('mp_compare')) {
    /** Struck-through comparison price, if the state price is below list price. */
    function mp_compare($product, ?string $state = null): ?float
    {
        return mp_pricing()->compareFor($product, $state);
    }
}

if (! function_exists('public_storage_url')) {
    /** Root-relative URL for a file on the public disk (safe for proxies/HTTPS). */
    function public_storage_url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $url = \Illuminate\Support\Facades\Storage::disk('public')->url($path);

        return parse_url($url, PHP_URL_PATH) ?: $url;
    }
}
