<?php

namespace App\Services\Images;

use App\Services\Images\Contracts\ImageSearchProvider;
use Illuminate\Support\Facades\File;

/**
 * Uses a previously synced public product catalogue (WooCommerce Store API)
 * as the image source. This is not a search engine: every result is a real
 * product listing on the supplier's own storefront, with its own name and
 * permalink, so the candidate can be checked against the source page.
 *
 * The provider only returns candidates; the strict name/size/variant matching
 * happens in CatalogueImageMatcher before anything is downloaded.
 */
class CatalogueImageSearchProvider implements ImageSearchProvider
{
    /**
     * @param  array<int, array{name: string, image: string, permalink?: ?string, source_site?: ?string}>|null  $catalogue
     */
    public function __construct(
        private ?array $catalogue = null,
        private ?string $path = null,
        private ?string $sourceSite = null,
    ) {
    }

    public function search(string $query, int $limit = 5): array
    {
        $matcher = app(CatalogueImageMatcher::class);
        $scored = $matcher->rank($query, $this->catalogue());

        $results = [];

        foreach ($scored as $row) {
            if ($row['conflict'] || ! $row['brand_match']) {
                continue;
            }

            $results[] = [
                'image_url' => (string) $row['item']['image'],
                'source_url' => $row['item']['permalink'] ?? null,
                'source_site' => $row['item']['source_site'] ?? $this->sourceSite(),
                'title' => (string) $row['item']['name'],
                'score' => $row['score'],
            ];

            if (count($results) >= $limit) {
                break;
            }
        }

        return $results;
    }

    public function isConfigured(): bool
    {
        return is_file($this->path());
    }

    public function name(): string
    {
        return 'catalogue';
    }

    public function path(): string
    {
        return $this->path ?? (string) config(
            'image_research.catalogue.path',
            storage_path('app/image-catalogue.json')
        );
    }

    public function sourceSite(): string
    {
        return $this->sourceSite ?? (string) config('image_research.catalogue.source_site', 'obaspalacefoods.com.ng');
    }

    /**
     * @return array<int, array{name: string, image: string, permalink?: ?string, source_site?: ?string}>
     */
    public function catalogue(): array
    {
        if ($this->catalogue !== null) {
            return $this->catalogue;
        }

        $path = $this->path();

        if (! is_file($path)) {
            return [];
        }

        $decoded = json_decode(ltrim((string) File::get($path), "\xEF\xBB\xBF"), true);

        if (! is_array($decoded)) {
            return [];
        }

        $this->catalogue = array_values(array_filter($decoded, function ($row): bool {
            return is_array($row) && filled($row['name'] ?? null) && filled($row['image'] ?? null);
        }));

        return $this->catalogue;
    }
}
