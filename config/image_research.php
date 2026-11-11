<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image search provider
    |--------------------------------------------------------------------------
    |
    | The research workflow never scrapes search engines directly. It calls an
    | approved image-search API and stores the results for human verification.
    | Set IMAGE_SEARCH_PROVIDER=serpapi and provide SERPAPI_KEY to enable it.
    | With no provider configured the workflow still runs in manual mode: every
    | product is recorded as "Pending Research" and no images are fabricated.
    |
    */
    'provider' => env('IMAGE_SEARCH_PROVIDER'),

    'serpapi' => [
        'key' => env('SERPAPI_KEY'),
        'endpoint' => env('SERPAPI_ENDPOINT', 'https://serpapi.com/search.json'),
        'engine' => env('SERPAPI_IMAGE_ENGINE', 'google_images'),
    ],

    'bing' => [
        'key' => env('BING_IMAGE_SEARCH_KEY'),
        'endpoint' => env('BING_IMAGE_SEARCH_ENDPOINT', 'https://api.bing.microsoft.com/v7.0/images/search'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Target vendor
    |--------------------------------------------------------------------------
    |
    | Opeyemi Foods is vendor 17 in this installation. The audit command can
    | be pointed at a different vendor with --vendor without changing code.
    |
    */
    'vendor_id' => (int) env('IMAGE_RESEARCH_VENDOR_ID', 17),

    'results_per_product' => (int) env('IMAGE_RESEARCH_RESULTS', 5),

    /*
    |--------------------------------------------------------------------------
    | Download safety limits
    |--------------------------------------------------------------------------
    */
    'max_bytes' => 8 * 1024 * 1024,

    'min_width' => 250,

    'min_height' => 250,

    'allowed_mime_types' => [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ],

    /*
    |--------------------------------------------------------------------------
    | Local storage
    |--------------------------------------------------------------------------
    |
    | Images follow the existing marketplace convention:
    | public/images/products/<unique-file>.webp. The database stores the
    | host-relative path (images/products/...).
    |
    */
    'disk' => 'public',

    'directory' => 'images/products',
];
