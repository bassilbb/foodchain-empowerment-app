<?php

namespace App\Services\Images;

use App\Models\AdminUser;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\ProductImage;
use App\Models\ProductImageResearch;
use App\Services\Images\Contracts\ImageSearchProvider;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Drives the "research -> human verification -> download -> attach" workflow.
 *
 * Nothing in here invents an image. A candidate is only stored as metadata
 * until an administrator approves it; only an approved candidate (or a manual
 * upload) is downloaded and attached to the product.
 */
class ImageResearchService
{
    public function __construct(private ImageSearchProvider $provider)
    {
    }

    public function providerName(): string
    {
        return $this->provider->name();
    }

    public function providerConfigured(): bool
    {
        return $this->provider->isConfigured();
    }

    public function vendorId(): int
    {
        return (int) config('image_research.vendor_id', 17);
    }

    /**
     * Products for the target vendor that do not have a real (non-placeholder)
     * image yet. This is the only set the workflow is allowed to touch.
     */
    public function outstandingProducts(int $vendorId = null)
    {
        return Product::query()
            ->where('vendor_id', $vendorId ?? $this->vendorId())
            ->whereDoesntHave('images', function ($query) {
                $query->where('is_placeholder', false);
            })
            ->orderBy('sku');
    }

    /**
     * Create/refresh one research row per outstanding product. Idempotent:
     * safe to run repeatedly and safe to run in production.
     *
     * @return array{created: int, refreshed: int, outstanding: int, with_images: int, total: int}
     */
    public function audit(int $vendorId = null): array
    {
        $vendorId = $vendorId ?? $this->vendorId();

        $total = Product::where('vendor_id', $vendorId)->count();
        $withImages = Product::where('vendor_id', $vendorId)
            ->whereHas('images', fn ($query) => $query->where('is_placeholder', false))
            ->count();

        $created = 0;
        $refreshed = 0;

        $this->outstandingProducts($vendorId)->chunkById(100, function ($products) use ($vendorId, &$created, &$refreshed) {
            foreach ($products as $product) {
                $record = ProductImageResearch::firstOrNew(['product_id' => $product->id]);

                if (! $record->exists) {
                    $record->fill([
                        'vendor_id' => $vendorId,
                        'sku' => $product->sku,
                        'product_name' => $product->name,
                        'status' => ProductImageResearch::STATUS_PENDING_RESEARCH,
                    ])->save();

                    $created++;

                    continue;
                }

                // Never overwrite a completed/verified row just because the
                // catalogue name changed. Only keep the metadata in sync.
                $record->fill([
                    'vendor_id' => $vendorId,
                    'sku' => $product->sku,
                    'product_name' => $product->name,
                ])->save();

                $refreshed++;
            }
        });

        return [
            'created' => $created,
            'refreshed' => $refreshed,
            'outstanding' => $this->outstandingProducts($vendorId)->count(),
            'with_images' => $withImages,
            'total' => $total,
        ];
    }

    /**
     * Ask the approved provider for candidates. A candidate is stored but the
     * product is NOT touched — an admin still has to verify it.
     */
    public function research(ProductImageResearch $record, ?int $limit = null): ProductImageResearch
    {
        $limit = $limit ?? (int) config('image_research.results_per_product', 5);

        $query = trim($record->product_name.' '.$record->sku);
        $candidates = $this->provider->search($query, $limit);

        $record->research_attempts = (int) $record->research_attempts + 1;
        $record->last_researched_at = now();
        $record->candidate_query = $query;

        if (empty($candidates)) {
            $record->status = ProductImageResearch::STATUS_NOT_FOUND;
            $record->save();

            return $record;
        }

        $candidate = $candidates[0];

        $record->fill([
            'status' => ProductImageResearch::STATUS_PENDING_VERIFICATION,
            'candidate_image_url' => $candidate['image_url'],
            'candidate_source_url' => $candidate['source_url'] ?? null,
            'candidate_source_site' => $candidate['source_site'] ?? null,
            'candidate_title' => $candidate['title'] ?? null,
            'rejection_reason' => null,
        ])->save();

        return $record;
    }

    /**
     * An administrator has checked the candidate against the real packaging.
     */
    public function verify(ProductImageResearch $record, int $adminId, ?string $notes = null): ProductImageResearch
    {
        if (blank($record->candidate_image_url)) {
            throw new \DomainException('There is no candidate image to verify. Upload a replacement instead.');
        }

        $record->fill([
            'status' => ProductImageResearch::STATUS_VERIFIED,
            'verification_status' => 'verified',
            'verified_by' => $adminId,
            'verified_at' => now(),
            'reviewer_notes' => $notes,
            'rejection_reason' => null,
        ])->save();

        return $record;
    }

    public function reject(ProductImageResearch $record, int $adminId, string $reason): ProductImageResearch
    {
        if (trim($reason) === '') {
            throw new \DomainException('A rejection reason is required.');
        }

        $record->fill([
            'status' => ProductImageResearch::STATUS_REJECTED,
            'verification_status' => 'rejected',
            'verified_by' => $adminId,
            'verified_at' => now(),
            'rejection_reason' => $reason,
            'reviewer_notes' => $reason,
        ])->save();

        return $record;
    }

    /**
     * Download the approved candidate, validate it, optimise it to WebP and
     * attach it to the correct product image relationship.
     *
     * @return array{success: bool, message: string, path?: string}
     */
    public function download(ProductImageResearch $record, bool $replace = false): array
    {
        if (! $record->candidate_image_url) {
            return $this->fail($record, 'No candidate image URL is stored for this product.');
        }

        if (! in_array($record->status, [
            ProductImageResearch::STATUS_VERIFIED,
            ProductImageResearch::STATUS_DOWNLOADED,
        ], true)) {
            return $this->fail($record, 'The candidate must be verified by an administrator before it can be downloaded.');
        }

        $product = Product::with('images')->find($record->product_id);

        if (! $product) {
            return $this->fail($record, 'The product linked to this research row no longer exists.');
        }

        if ($product->hasRealImages() && ! $replace) {
            return $this->fail($record, 'This product already has a real image. Use replace if the new image should supersede it.');
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders(['User-Agent' => 'FoodChainImageResearch/1.0 (+https://foodchain.ng)'])
                ->withOptions(['allow_redirects' => ['max' => 5]])
                ->get($record->candidate_image_url);

            if (! $response->successful()) {
                return $this->fail($record, 'Download failed with HTTP '.$response->status().'.');
            }

            $body = $response->body();

            if (strlen($body) > (int) config('image_research.max_bytes', 8 * 1024 * 1024)) {
                return $this->fail($record, 'The image is larger than the allowed size.');
            }

            $info = @getimagesizefromstring($body);

            if (! $info || empty($info['mime'])) {
                return $this->fail($record, 'The downloaded file is not a valid image.');
            }

            if (! in_array($info['mime'], (array) config('image_research.allowed_mime_types', []), true)) {
                return $this->fail($record, 'Unsupported image type: '.$info['mime'].'.');
            }

            $minWidth = (int) config('image_research.min_width', 250);
            $minHeight = (int) config('image_research.min_height', 250);

            if ($info[0] < $minWidth || $info[1] < $minHeight) {
                return $this->fail($record, "The image is too small ({$info[0]}x{$info[1]}); at least {$minWidth}x{$minHeight} is required.");
            }

            $webp = $this->toWebp($body);

            if ($webp === null) {
                return $this->fail($record, 'The image could not be converted to WebP on this server.');
            }

            $directory = public_path(trim((string) config('image_research.directory', 'images/products'), '/'));
            if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
                return $this->fail($record, 'The product image directory could not be created.');
            }

            $filename = $this->filenameFor($record, $webp);
            $relativePath = trim((string) config('image_research.directory', 'images/products'), '/').'/'.$filename;
            $absolutePath = public_path($relativePath);

            if (file_put_contents($absolutePath, $webp) === false) {
                return $this->fail($record, 'The optimised image could not be written to disk.');
            }

            $this->attach($record, $product, $relativePath, $replace);

            return [
                'success' => true,
                'message' => 'Image downloaded, optimised and attached.',
                'path' => $relativePath,
            ];
        } catch (Throwable $e) {
            Log::error('Image download failed: '.$e->getMessage());

            return $this->fail($record, 'Download failed: '.$e->getMessage());
        }
    }

    /**
     * Administrator uploads a replacement image they have already verified.
     *
     * @return array{success: bool, message: string, path?: string}
     */
    public function attachUpload(ProductImageResearch $record, UploadedFile $file, int $adminId): array
    {
        $product = Product::with('images')->find($record->product_id);

        if (! $product) {
            return $this->fail($record, 'The product linked to this research row no longer exists.');
        }

        $body = file_get_contents($file->getRealPath());

        if ($body === false) {
            return $this->fail($record, 'The uploaded file could not be read.');
        }

        $info = @getimagesizefromstring($body);

        if (! $info || empty($info['mime'])) {
            return $this->fail($record, 'The uploaded file is not a valid image.');
        }

        if (! in_array($info['mime'], (array) config('image_research.allowed_mime_types', []), true)) {
            return $this->fail($record, 'Unsupported image type: '.$info['mime'].'.');
        }

        $minWidth = (int) config('image_research.min_width', 250);
        $minHeight = (int) config('image_research.min_height', 250);

        if ($info[0] < $minWidth || $info[1] < $minHeight) {
            return $this->fail($record, "The image is too small ({$info[0]}x{$info[1]}); at least {$minWidth}x{$minHeight} is required.");
        }

        $webp = $this->toWebp($body);

        if ($webp === null) {
            return $this->fail($record, 'The image could not be converted to WebP on this server.');
        }

        $directory = public_path(trim((string) config('image_research.directory', 'images/products'), '/'));
        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            return $this->fail($record, 'The product image directory could not be created.');
        }

        $filename = $this->filenameFor($record, $webp);
        $relativePath = trim((string) config('image_research.directory', 'images/products'), '/').'/'.$filename;
        $absolutePath = public_path($relativePath);

        if (file_put_contents($absolutePath, $webp) === false) {
            return $this->fail($record, 'The optimised image could not be written to disk.');
        }

        $record->fill([
            'status' => ProductImageResearch::STATUS_VERIFIED,
            'verification_status' => 'verified',
            'verified_by' => $adminId,
            'verified_at' => now(),
        ])->save();

        $this->attach($record, $product, $relativePath, true);

        return [
            'success' => true,
            'message' => 'Replacement image uploaded and attached.',
            'path' => $relativePath,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function report(int $vendorId = null): array
    {
        $vendorId = $vendorId ?? $this->vendorId();

        $total = Product::where('vendor_id', $vendorId)->count();
        $withImages = Product::where('vendor_id', $vendorId)
            ->whereHas('images', fn ($query) => $query->where('is_placeholder', false))
            ->count();

        $counts = ProductImageResearch::where('vendor_id', $vendorId)
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->all();

        foreach (array_keys(ProductImageResearch::STATUSES) as $status) {
            $counts[$status] = (int) ($counts[$status] ?? 0);
        }

        return [
            'vendor_id' => $vendorId,
            'total_products' => $total,
            'with_real_images' => $withImages,
            'outstanding' => max(0, $total - $withImages),
            'status_counts' => $counts,
            'pending_verification' => $counts[ProductImageResearch::STATUS_PENDING_VERIFICATION]
                + $counts[ProductImageResearch::STATUS_CANDIDATE_FOUND],
            'verified' => $counts[ProductImageResearch::STATUS_VERIFIED],
            'downloaded' => $counts[ProductImageResearch::STATUS_DOWNLOADED],
            'not_found' => $counts[ProductImageResearch::STATUS_NOT_FOUND],
            'failed_downloads' => ProductImageResearch::where('vendor_id', $vendorId)
                ->where('download_status', ProductImageResearch::DOWNLOAD_FAILED)
                ->count(),
            'provider' => $this->providerName(),
            'provider_configured' => $this->providerConfigured(),
        ];
    }

    private function attach(ProductImageResearch $record, Product $product, string $relativePath, bool $replace): void
    {
        DB::transaction(function () use ($record, $product, $relativePath, $replace) {
            $previousPath = $record->downloaded_path;

            $hasPrimary = $product->images()
                ->where('is_placeholder', false)
                ->where('is_primary', true)
                ->exists();

            $image = ProductImage::firstOrNew([
                'product_id' => $product->id,
                'image_path' => $relativePath,
            ]);

            $image->fill([
                'is_primary' => $hasPrimary ? (bool) $image->is_primary : true,
                'is_placeholder' => false,
                'sort_order' => $image->sort_order ?? 0,
            ])->save();

            if ($replace && $previousPath && $previousPath !== $relativePath) {
                ProductImage::where('product_id', $product->id)
                    ->where('image_path', $previousPath)
                    ->delete();

                $oldAbsolute = public_path($previousPath);
                if (is_file($oldAbsolute)) {
                    @unlink($oldAbsolute);
                }
            }

            $record->fill([
                'status' => ProductImageResearch::STATUS_DOWNLOADED,
                'downloaded_path' => $relativePath,
                'download_status' => ProductImageResearch::DOWNLOAD_DOWNLOADED,
                'download_error' => null,
                'downloaded_at' => now(),
                'verification_status' => 'verified',
            ])->save();
        });
    }

    private function fail(ProductImageResearch $record, string $message): array
    {
        $record->fill([
            'download_status' => ProductImageResearch::DOWNLOAD_FAILED,
            'download_error' => Str::limit($message, 1000),
        ])->save();

        return ['success' => false, 'message' => $message];
    }

    private function filenameFor(ProductImageResearch $record, string $binary): string
    {
        $sku = Str::slug((string) ($record->sku ?: 'product-'.$record->product_id));
        $hash = substr(sha1($binary), 0, 8);

        return 'opeyemi-'.$sku.'-'.$hash.'.webp';
    }

    /**
     * Convert any supported image to WebP using Imagick when available, with a
     * GD fallback. Returns null when the server cannot encode WebP.
     */
    private function toWebp(string $binary): ?string
    {
        if (class_exists(\Imagick::class)) {
            try {
                $image = new \Imagick;
                $image->readImageBlob($binary);
                $image->setImageFormat('webp');
                $image->setImageCompressionQuality(82);
                $image->stripImage();
                $webp = $image->getImageBlob();
                $image->clear();

                return $webp ?: null;
            } catch (Throwable $e) {
                Log::warning('Imagick WebP conversion failed: '.$e->getMessage());
            }
        }

        if (function_exists('imagewebp')) {
            $source = @imagecreatefromstring($binary);

            if ($source !== false) {
                ob_start();
                $ok = imagewebp($source, null, 82);
                $webp = ob_get_clean();
                imagedestroy($source);

                return $ok ? $webp : null;
            }
        }

        return null;
    }
}
