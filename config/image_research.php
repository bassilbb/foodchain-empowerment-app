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
    | Catalogue provider (public supplier storefront)
    |--------------------------------------------------------------------------
    |
    | Instead of a search-engine API this workflow can read a catalogue synced
    | from a supplier's own public WooCommerce Store API. Every candidate is a
    | real listing with its own name and permalink, so it can be checked.
    | Set IMAGE_SEARCH_PROVIDER=catalogue to use it.
    |
    */
    'catalogue' => [
        'base_url' => env('IMAGE_CATALOGUE_BASE_URL', 'https://obaspalacefoods.com.ng'),
        'path' => env('IMAGE_CATALOGUE_PATH', storage_path('app/image-catalogue.json')),
        'source_site' => env('IMAGE_CATALOGUE_SOURCE_SITE', 'obaspalacefoods.com.ng'),
        'per_page' => (int) env('IMAGE_CATALOGUE_PER_PAGE', 100),
        'max_pages' => (int) env('IMAGE_CATALOGUE_MAX_PAGES', 50),

        // A candidate is auto-approved (and downloaded) only when it clears
        // both bars. Anything weaker is stored for an administrator to review.
        'min_score' => (float) env('IMAGE_CATALOGUE_MIN_SCORE', 0.75),
        'min_margin' => (float) env('IMAGE_CATALOGUE_MIN_MARGIN', 0.05),

        // Below this score a "candidate" is noise and is not even stored for
        // review. Between review_score and min_score it is queued for a human.
        'review_score' => (float) env('IMAGE_CATALOGUE_REVIEW_SCORE', 0.60),

        // Supplier storefronts often publish only a small pack shot. Because the
        // product has already been identified by name, size and variant, a
        // lower resolution floor is acceptable here than for a manual upload.
        'min_width' => (int) env('IMAGE_CATALOGUE_MIN_WIDTH', 100),
        'min_height' => (int) env('IMAGE_CATALOGUE_MIN_HEIGHT', 100),
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
