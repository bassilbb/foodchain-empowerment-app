<?php

namespace App\Services\Images;

use App\Services\Images\Contracts\ImageSearchProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * SerpApi Google Images adapter. This is an approved, paid image-search API;
 * we never parse Google's HTML. Results are candidates only and still require
 * an administrator to verify brand, variant and pack size.
 */
class SerpApiImageSearchProvider implements ImageSearchProvider
{
    public function __construct(
        private ?string $key,
        private string $endpoint,
        private string $engine = 'google_images',
    ) {
    }

    public function search(string $query, int $limit = 5): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        try {
            $response = Http::timeout(20)->get($this->endpoint, [
                'engine' => $this->engine,
                'q' => $query,
                'num' => max(1, min($limit, 20)),
                'api_key' => $this->key,
            ]);

            if (! $response->successful()) {
                Log::warning('Image search failed', [
                    'provider' => 'serpapi',
                    'status' => $response->status(),
                ]);

                return [];
            }

            $payload = $response->json();
            $images = $payload['images_results'] ?? [];

            if (! is_array($images)) {
                return [];
            }

            $candidates = [];

            foreach ($images as $image) {
                $url = $image['original'] ?? $image['thumbnail'] ?? null;

                if (! is_string($url) || ! preg_match('#^https?://#i', $url)) {
                    continue;
                }

                $candidates[] = [
                    'image_url' => $url,
                    'source_url' => $image['link'] ?? null,
                    'source_site' => $image['source'] ?? parse_url($url, PHP_URL_HOST),
                    'title' => $image['title'] ?? null,
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
        return 'serpapi';
    }
}
