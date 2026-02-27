<?php

if (!function_exists('format_currency')) {
    /**
     * Format amount as Nigerian Naira
     */
    function format_currency(float $amount): string
    {
        return '₦' . number_format($amount, 2);
    }
}

if (!function_exists('setting')) {
    /**
     * Get setting value
     */
    function setting(string $key, $default = null)
    {
        return \App\Models\Setting::get($key, $default);
    }
}

if (!function_exists('generate_referral_link')) {
    /**
     * Generate referral link for user
     */
    function generate_referral_link(string $code): string
    {
        return url("/register?ref={$code}");
    }
}

if (!function_exists('admin_can')) {
    /**
     * Check if admin has permission
     */
    function admin_can(string $permission): bool
    {
        $admin = auth()->guard('admin')->user();
        return $admin && $admin->hasPermission($permission);
    }
}

if (!function_exists('tier_badge_color')) {
    /**
     * Get color class for tier badge
     */
    function tier_badge_color(string $tierSlug): string
    {
        return match($tierSlug) {
            'bronze' => 'bg-orange-600',
            'silver' => 'bg-gray-400',
            'gold' => 'bg-yellow-500',
            'emerald' => 'bg-green-600',
            default => 'bg-gray-500',
        };
    }
}