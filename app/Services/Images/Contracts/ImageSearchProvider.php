<?php

namespace App\Services\Images\Contracts;

/**
 * An approved image-search API. Implementations must never scrape a search
 * engine's HTML; they return structured candidate metadata only.
 */
interface ImageSearchProvider
{
    /**
     * @return array<int, array{
     *     image_url: string,
     *     source_url: ?string,
     *     source_site: ?string,
     *     title: ?string
     * }>
     */
    public function search(string $query, int $limit = 5): array;

    public function isConfigured(): bool;

    public function name(): string;
}
