<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Downloads a supplier storefront's public WooCommerce product catalogue
 * (names, permalinks and image URLs) to a local JSON file.
 *
 * Only the public Store API and product pages are used; robots.txt on the
 * target site allows both and disallows only cart/admin URLs, which are never
 * touched here.
 */
class SyncImageCatalogue extends Command
{
    protected $signature = 'images:sync-catalogue
        {--url= : Store base URL (defaults to config)}
        {--path= : Destination JSON file (defaults to config)}
        {--per-page=100 : Products per request (WooCommerce caps this at 100)}
        {--max-pages=50 : Safety cap on the number of pages}';

    protected $description = 'Sync a supplier storefront catalogue (names + image URLs) for image sourcing';

    public function handle(): int
    {
        $base = rtrim((string) ($this->option('url') ?: config('image_research.catalogue.base_url')), '/');
        $path = (string) ($this->option('path') ?: config('image_research.catalogue.path'));
        $perPage = max(1, min(100, (int) $this->option('per-page')));
        $maxPages = max(1, (int) $this->option('max-pages'));

        if ($base === '') {
            $this->error('No catalogue URL configured. Pass --url or set IMAGE_CATALOGUE_BASE_URL.');

            return self::FAILURE;
        }

        $this->info("Syncing catalogue from {$base}");

        $products = [];
        $page = 1;
        $totalPages = 1;
        $total = null;

        do {
            $endpoint = $base.'/wp-json/wc/store/products';

            try {
                $response = Http::timeout(60)
                    ->withHeaders([
                        'User-Agent' => 'FoodChainImageResearch/1.0 (+https://foodchain.ng)',
                        'Accept' => 'application/json',
                    ])
                    ->get($endpoint, ['per_page' => $perPage, 'page' => $page]);
            } catch (Throwable $e) {
                $this->error("Request failed: {$e->getMessage()}");

                return self::FAILURE;
            }

            if (! $response->successful()) {
                $this->error("Store API returned HTTP {$response->status()} on page {$page}.");

                return self::FAILURE;
            }

            $totalPages = (int) ($response->header('X-WP-TotalPages') ?: 1);
            $total = (int) ($response->header('X-WP-Total') ?: $total);

            $items = $response->json();

            if (! is_array($items)) {
                $this->error('Store API returned an unexpected payload.');

                return self::FAILURE;
            }

            foreach ($items as $item) {
                $name = $this->decode($item['name'] ?? '');
                $image = $item['images'][0]['src'] ?? null;

                if ($name === '' || blank($image)) {
                    continue;
                }

                $products[] = [
                    'id' => $item['id'] ?? null,
                    'name' => $name,
                    'slug' => $item['slug'] ?? null,
                    'permalink' => $item['permalink'] ?? null,
                    'sku' => $item['sku'] ?? null,
                    'image' => $image,
                    'source_site' => parse_url($base, PHP_URL_HOST) ?: $base,
                ];
            }

            $this->line(sprintf(
                '  page %d/%d — %d products (%d with images so far)',
                $page,
                $totalPages,
                count($items),
                count($products)
            ));

            $page++;
        } while ($page <= $totalPages && $page <= $maxPages);

        File::ensureDirectoryExists(dirname($path));

        File::put(
            $path,
            json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );

        $this->info(sprintf(
            'Saved %d products with images to %s (store reports %s products).',
            count($products),
            $path,
            $total ?? 'unknown'
        ));

        if ($total !== null && count($products) < $total) {
            $this->warn('Some products were skipped because they have no image.');
        }

        return self::SUCCESS;
    }

    private function decode(string $value): string
    {
        return trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}
