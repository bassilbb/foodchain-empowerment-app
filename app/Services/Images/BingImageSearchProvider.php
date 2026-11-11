<?php

namespace App\Services\Images;

use App\Services\Images\Contracts\ImageSearchProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Bing Image Search adapter (approved API, no HTML scraping).
 */
class BingImageSearchProvider implements ImageSearchProvider
{
    public function __construct(
        private ?string $key,
        private string $endpoint,
    ) {
    }

    public function search(string $query, int $limit = 5): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        try {
            $response = Http::timeout(20)
                ->withHeaders(['Ocp-Apim-Subscription-Key' => $this->key])
                ->get($this->endpoint, [
                    'q' => $query,
                    'count' => max(1, min($limit, 50)),
                    'safeSearch' => 'Moderate',
                ]);

            if (! $response->successful()) {
                Log::warning('Image search failed', [
                    'provider' => 'bing',
                    'status' => $response->status(),
                ]);

                return [];
            }

            $candidates = [];

            foreach (($response->json()['value'] ?? []) as $image) {
                $url = $image['contentUrl'] ?? null;

                if (! is_string($url) || ! preg_match('#^https?://#i', $url)) {
                    continue;
                }

                $candidates[] = [
                    'image_url' => $url,
                    'source_url' => $image['hostPageUrl'] ?? null,
                    'source_site' => $image['hostPageDisplayUrl'] ?? parse_url($url, PHP_URL_HOST),
                    'title' => $image['name'] ?? null,
                ];

                if (count($candidates) >= $limit) {
                    break;
                }
            }

            return $candidates;
        } catch (Throwable $e) {
            Log::warning('Image search exception: '.$e->getMessage());

            return [];
        }
    }

    public function isConfigured(): bool
    {
        return is_string($this->key) && trim($this->key) !== '';
    }

    public function name(): string
    {
        return 'bing';
    }
}
