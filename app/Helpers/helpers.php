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
    /**
     * Get site logo URL
     */
    function site_logo(): ?string
    {
        $logo = \App\Models\Setting::where('key', 'site_logo')->first();
        if ($logo && $logo->value) {
            if (file_exists(public_path('uploads/'.$logo->value))) {
                return asset('uploads/'.$logo->value);
            }

            return asset('storage/'.$logo->value);
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
            if (file_exists(public_path('uploads/'.$favicon->value))) {
                return asset('uploads/'.$favicon->value);
            }

            return asset('storage/'.$favicon->value);
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
