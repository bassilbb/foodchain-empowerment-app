<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductPlaceholder
{
    /**
     * Category slug => [light, dark] gradient stops.
     */
    private const PALETTE = [
        'grains' => ['#f59e0b', '#b45309'],
        'cooking-ingredients' => ['#f87171', '#b91c1c'],
        'seasonings' => ['#eab308', '#a16207'],
        'proteins' => ['#f472b6', '#9d174d'],
        'beverages' => ['#38bdf8', '#0369a1'],
        'breakfast' => ['#a78bfa', '#6d28d9'],
        'pasta' => ['#fbbf24', '#d97706'],
        'snacks' => ['#fb923c', '#c2410c'],
        'baby-food' => ['#f9a8d4', '#db2777'],
        'household-essentials' => ['#2dd4bf', '#0f766e'],
        'default' => ['#34d399', '#047857'],
    ];

    public const RELATIVE_DIRECTORY = 'images/products';

    public static function svg(?string $label, ?string $category = null): string
    {
        $label = trim((string) $label) ?: 'FoodChain Product';
        $key = Str::slug((string) $category);
        [$light, $dark] = self::PALETTE[$key] ?? self::PALETTE['default'];

        $name = htmlspecialchars(Str::limit($label, 26, ''), ENT_QUOTES);
        $cat = htmlspecialchars(Str::headline((string) ($category ?: 'Grocery')), ENT_QUOTES);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400">
<defs>
<linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
<stop offset="0" stop-color="{$light}"/>
<stop offset="1" stop-color="{$dark}"/>
</linearGradient>
<radialGradient id="glow" cx="50%" cy="32%" r="62%">
<stop offset="0" stop-color="#ffffff" stop-opacity="0.30"/>
<stop offset="1" stop-color="#ffffff" stop-opacity="0"/>
</radialGradient>
</defs>
<rect width="400" height="400" fill="url(#bg)"/>
<rect width="400" height="400" fill="url(#glow)"/>
<circle cx="200" cy="150" r="118" fill="#ffffff" fill-opacity="0.10"/>
<g fill="none" stroke="#ffffff" stroke-opacity="0.92" stroke-width="10" stroke-linejoin="round" stroke-linecap="round">
<path d="M158 128h84l-8 80a13 13 0 0 1-13 12h-42a13 13 0 0 1-13-12z"/>
<path d="M182 128v-14a18 18 0 0 1 36 0v14"/>
</g>
<text x="200" y="286" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="27" font-weight="700" fill="#ffffff">{$name}</text>
<text x="200" y="318" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="15" fill="#ffffff" fill-opacity="0.85">{$cat}</text>
<text x="200" y="356" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="12" letter-spacing="3" fill="#ffffff" fill-opacity="0.55">FOODCHAIN</text>
</svg>
SVG;
    }

    /**
     * Write an SVG placeholder to disk and return its public-relative path.
     */
    public static function store(string $slug, ?string $label, ?string $category = null): string
    {
        $slug = Str::slug($slug) ?: 'product-'.Str::random(6);
        $directory = public_path(self::RELATIVE_DIRECTORY);

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        File::put($directory.DIRECTORY_SEPARATOR.$slug.'.svg', self::svg($label, $category));

        return self::RELATIVE_DIRECTORY.'/'.$slug.'.svg';
    }

    /**
     * Convenience data URI (used where a file cannot be written).
     */
    public static function for(?string $label, ?string $category = null): string
    {
        return 'data:image/svg+xml;utf8,'.rawurlencode(self::svg($label, $category));
    }

    public static function isPlaceholder(?string $path): bool
    {
        return is_string($path) && (str_starts_with($path, 'data:image/svg') || str_starts_with($path, self::RELATIVE_DIRECTORY.'/'));
    }
}
