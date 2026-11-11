<?php

namespace App\Services\Images;

use App\Services\Images\Contracts\ImageSearchProvider;

/**
 * Manual mode: no external API is configured. Research leaves products in
 * "Pending Research" rather than inventing images. Administrators can still
 * upload a verified replacement from the dashboard.
 */
class NullImageSearchProvider implements ImageSearchProvider
{
    public function search(string $query, int $limit = 5): array
    {
        return [];
    }

    public function isConfigured(): bool
    {
        return false;
    }

    public function name(): string
    {
        return 'manual';
    }
}
