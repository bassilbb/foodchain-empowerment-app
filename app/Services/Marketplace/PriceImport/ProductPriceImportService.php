<?php

namespace App\Services\Marketplace\PriceImport;

use App\Models\AdminUser;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\PriceImportBatch;
use App\Models\Marketplace\PriceImportItem;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\ProductPriceHistory;
use App\Models\Marketplace\Vendor;
use App\Services\AuditService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ProductPriceImportService
{
    public const CHUNK_SIZE = 200;

    public function __construct(
        private readonly CsvProductPriceParser $csvParser,
        private readonly XmlProductPriceParser $xmlParser,
        private readonly ProductMatchingService $matcher,
    ) {
    }

    /**
     * Store, parse and match an uploaded price list. Nothing is written to a
     * product until the administrator confirms the preview.
     */
    public function createPreview(UploadedFile $file, Vendor $vendor, AdminUser $admin, array $options = []): PriceImportBatch
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $format = $extension === 'xml' ? 'xml' : 'csv';

        $base = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $base = $base !== '' ? $base : 'supplier-price-list';
        $storedName = $base.'-'.now()->format('YmdHis').'-'.Str::lower(Str::random(6)).'.'.$extension;
        $directory = 'price-imports/'.now()->format('Y/m');

        $path = $file->storeAs($directory, $storedName, 'local');

        if (! is_string($path) || $path === '') {
            throw new \RuntimeException('The uploaded price list could not be stored.');
        }

        $absolute = Storage::disk('local')->path($path);
        $hash = hash_file('sha256', $absolute) ?: '';

        $batch = PriceImportBatch::create([
            'vendor_id' => $vendor->id,
            'admin_user_id' => $admin->id,
            'original_filename' => $file->getClientOriginalName(),
            'stored_path' => $path,
            'file_hash' => $hash,
            'format' => $format,
            'status' => PriceImportBatch::STATUS_UPLOADED,
            'options' => [
                'create_new_products' => (bool) ($options['create_new_products'] ?? false),
                'default_category_id' => $options['default_category_id'] ?? null,
            ],
        ]);

        try {
            $parser = $format === 'xml' ? $this->xmlParser : $this->csvParser;
            $parsed = $parser->parse($absolute, (array) ($options['mapping'] ?? []));

            $analysis = $this->matcher->analyse($vendor, $parsed->rows, [
                'default_category_id' => $options['default_category_id'] ?? null,
                'exclude_batch_id' => $batch->id,
            ]);

            DB::transaction(function () use ($batch, $parsed, $analysis) {
                foreach ($analysis['items'] as $item) {
                    $batch->items()->create($item);
                }

                foreach ($analysis['missing'] as $missing) {
                    $batch->missingProducts()->create($missing + ['action' => 'pending']);
                }

                $batch->update(array_merge($analysis['summary'], [
                    'status' => PriceImportBatch::STATUS_PREVIEWED,
                    'field_mapping' => $parsed->mapping,
                    'previewed_at' => now(),
                    'last_error' => null,
                ]));
            });
        } catch (Throwable $exception) {
            $batch->update([
                'status' => PriceImportBatch::STATUS_FAILED,
                'last_error' => Str::limit($exception->getMessage(), 2000),
            ]);

            throw $exception;
        }

        AuditService::log(
            'price_import.upload',
            $batch,
            ['status' => $batch->status, 'total_records' => $batch->total_records],
            'Supplier price list "'.$batch->original_filename.'" uploaded for '.$vendor->name.'.',
        );

        return $batch->fresh();
    }

    /**
     * Apply a previously generated preview in transactional chunks.
     *
     * @param  array<string, mixed>  $options
     */
    public function apply(PriceImportBatch $batch, AdminUser $admin, array $options = []): PriceImportBatch
    {
        $batch = PriceImportBatch::query()->whereKey($batch->id)->firstOrFail();

        if (! in_array($batch->status, [
            PriceImportBatch::STATUS_PREVIEWED,
            PriceImportBatch::STATUS_QUEUED,
            PriceImportBatch::STATUS_FAILED,
        ], true)) {
            throw new \RuntimeException('This upload can no longer be applied. Current status: '.$batch->statusLabel().'.');
        }

        if ($batch->items()->count() === 0) {
            throw new \RuntimeException('This upload has no preview records to apply.');
        }

        $createNew = (bool) ($options['create_new_products'] ?? ($batch->options['create_new_products'] ?? false));
        $defaultCategoryId = $options['default_category_id'] ?? ($batch->options['default_category_id'] ?? null);
        $deactivateMissing = (bool) ($options['deactivate_missing'] ?? false);
        $completeCatalogue = (bool) ($options['confirm_complete_catalogue'] ?? false);

        $batch->update([
            'status' => PriceImportBatch::STATUS_PROCESSING,
            'options' => array_merge($batch->options ?? [], [
                'create_new_products' => $createNew,
                'default_category_id' => $defaultCategoryId,
                'deactivate_missing' => $deactivateMissing && $completeCatalogue,
            ]),
            'applied_count' => 0,
            'created_count' => 0,
            'failed_count' => 0,
            'deactivated_count' => 0,
            'processed_count' => 0,
            'last_error' => null,
        ]);

        $applied = 0;
        $created = 0;
        $failed = 0;
        $processed = 0;

        $batch->items()->orderBy('id')->chunkById(self::CHUNK_SIZE, function ($items) use (
            $batch, $admin, $createNew, $defaultCategoryId, &$applied, &$created, &$failed, &$processed
        ) {
            DB::transaction(function () use (
                $items, $batch, $admin, $createNew, $defaultCategoryId, &$applied, &$created, &$failed, &$processed
            ) {
                foreach ($items as $item) {
                    $processed++;

                    $result = $this->applyItem($batch, $item, $admin, $createNew, $defaultCategoryId);

                    if ($result['outcome'] === 'applied') {
                        $applied++;
                    } elseif ($result['outcome'] === 'created') {
                        $created++;
                    } elseif ($result['outcome'] === 'failed') {
                        $failed++;
                    }
                }
            });

            $batch->update([
                'applied_count' => $applied,
                'created_count' => $created,
                'failed_count' => $failed,
                'processed_count' => $processed,
            ]);
        });

        $deactivated = $this->reconcileMissingProducts($batch, $deactivateMissing && $completeCatalogue);

        $status = match (true) {
            $failed > 0 && ($applied + $created) > 0 => PriceImportBatch::STATUS_PARTIALLY_APPLIED,
            $failed > 0 => PriceImportBatch::STATUS_FAILED,
            default => PriceImportBatch::STATUS_COMPLETED,
        };

        $batch->update([
            'status' => $status,
            'applied_at' => now(),
            'applied_count' => $applied,
            'created_count' => $created,
            'failed_count' => $failed,
            'deactivated_count' => $deactivated,
            'processed_count' => $processed,
        ]);

        AuditService::log(
            'price_import.apply',
            $batch,
            [
                'applied' => $applied,
                'created' => $created,
                'failed' => $failed,
                'deactivated' => $deactivated,
                'status' => $status,
            ],
            'Bulk price list "'.$batch->original_filename.'" applied for '.($batch->vendor?->name ?? 'supplier').'.',
        );

        return $batch->fresh();
    }

    /**
     * @return array{outcome: string, error?: string}
     */
    private function applyItem(
        PriceImportBatch $batch,
        PriceImportItem $item,
        AdminUser $admin,
        bool $createNew,
        mixed $defaultCategoryId,
    ): array {
        if (in_array($item->status, [PriceImportItem::STATUS_INVALID, PriceImportItem::STATUS_AMBIGUOUS], true)) {
            return ['outcome' => 'skipped'];
        }

        try {
            if ($item->status === PriceImportItem::STATUS_MATCHED) {
                return $this->applyMatchedItem($batch, $item, $admin);
            }

            if ($item->status === PriceImportItem::STATUS_NEW) {
                return $this->applyNewItem($batch, $item, $admin, $createNew, $defaultCategoryId);
            }

            return ['outcome' => 'skipped'];
        } catch (Throwable $exception) {
            $errors = $item->errors ?? [];
            $errors[] = $exception->getMessage();

            $item->forceFill([
                'errors' => array_values(array_unique($errors)),
                'applied' => false,
            ])->save();

            return ['outcome' => 'failed', 'error' => $exception->getMessage()];
        }
    }

    /**
     * @return array{outcome: string}
     */
    private function applyMatchedItem(PriceImportBatch $batch, PriceImportItem $item, AdminUser $admin): array
    {
        if ($item->change_type === PriceImportItem::CHANGE_UNCHANGED) {
            $item->forceFill(['applied' => true, 'applied_at' => now()])->save();

            return ['outcome' => 'skipped'];
        }

        $product = Product::query()->whereKey($item->product_id)->lockForUpdate()->first();

        if ($product === null) {
            throw new \RuntimeException('The matched product no longer exists.');
        }

        $oldPrice = round((float) $product->price, 2);
        $newPrice = round((float) $item->new_price, 2);

        if (abs($oldPrice - $newPrice) < 0.01) {
            $item->forceFill([
                'change_type' => PriceImportItem::CHANGE_UNCHANGED,
                'difference' => 0,
                'percent_change' => 0,
                'applied' => true,
                'applied_at' => now(),
            ])->save();

            return ['outcome' => 'skipped'];
        }

        $product->price = $newPrice;
        $product->save();

        $difference = round($newPrice - $oldPrice, 2);

        ProductPriceHistory::create([
            'product_id' => $product->id,
            'vendor_id' => $product->vendor_id,
            'batch_id' => $batch->id,
            'admin_user_id' => $admin->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'source' => ProductPriceHistory::SOURCE_BULK_IMPORT,
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'difference' => $difference,
            'percent_change' => $oldPrice > 0 ? round(($difference / $oldPrice) * 100, 2) : null,
            'status' => ProductPriceHistory::STATUS_APPLIED,
            'note' => 'Bulk price upload #'.$batch->id.' ('.$batch->original_filename.')',
        ]);

        $item->forceFill([
            'product_id' => $product->id,
            'current_price' => $oldPrice,
            'difference' => $difference,
            'applied' => true,
            'applied_at' => now(),
        ])->save();

        return ['outcome' => 'applied'];
    }

    /**
     * @return array{outcome: string}
     */
    private function applyNewItem(
        PriceImportBatch $batch,
        PriceImportItem $item,
        AdminUser $admin,
        bool $createNew,
        mixed $defaultCategoryId,
    ): array {
        if (! $createNew) {
            return ['outcome' => 'skipped'];
        }

        $raw = $item->raw ?? [];
        $categoryId = $raw['_resolved_category_id'] ?? null;

        if (! is_numeric($categoryId) && is_numeric($defaultCategoryId)) {
            $categoryId = (int) $defaultCategoryId;
        }

        if (! is_numeric($categoryId) && $item->category_name) {
            $category = Category::query()
                ->whereRaw('lower(name) = ?', [mb_strtolower(trim($item->category_name))])
                ->first();

            $categoryId = $category?->id;
        }

        if (! is_numeric($categoryId) || ! Category::whereKey((int) $categoryId)->exists()) {
            throw new \RuntimeException('A valid category is required before this product can be created.');
        }

        $price = round((float) ($item->new_price ?? $item->price), 2);

        if ($price <= 0) {
            throw new \RuntimeException('A valid price is required before this product can be created.');
        }

        $sku = $item->sku;
        if ($sku === null || Product::withTrashed()->where('sku', $sku)->exists()) {
            $sku = Product::generateSku();
        }

        $product = Product::create([
            'vendor_id' => $batch->vendor_id,
            'category_id' => (int) $categoryId,
            'name' => $item->product_name,
            'slug' => $this->uniqueSlug($item->product_name),
            'sku' => $sku,
            'supplier_product_code' => $item->supplier_product_code,
            'barcode' => $item->barcode,
            'unit' => $item->unit,
            'price' => $price,
            'stock_quantity' => 0,
            'low_stock_threshold' => 5,
            'is_active' => true,
            'is_featured' => false,
            'approval_status' => 'approved',
            'approved_at' => now(),
            'meta' => [
                'created_by_price_import_batch_id' => $batch->id,
                'supplier_price_list' => $batch->original_filename,
            ],
        ]);

        ProductPriceHistory::create([
            'product_id' => $product->id,
            'vendor_id' => $product->vendor_id,
            'batch_id' => $batch->id,
            'admin_user_id' => $admin->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'source' => ProductPriceHistory::SOURCE_BULK_IMPORT,
            'old_price' => null,
            'new_price' => $price,
            'difference' => null,
            'percent_change' => null,
            'status' => ProductPriceHistory::STATUS_CREATED,
            'note' => 'Created by bulk price upload #'.$batch->id.' ('.$batch->original_filename.')',
        ]);

        $item->forceFill([
            'product_id' => $product->id,
            'applied' => true,
            'applied_at' => now(),
        ])->save();

        return ['outcome' => 'created'];
    }

    private function reconcileMissingProducts(PriceImportBatch $batch, bool $deactivate): int
    {
        $deactivated = 0;

        foreach ($batch->missingProducts()->where('action', 'pending')->get() as $missing) {
            if (! $deactivate) {
                $missing->update(['action' => 'kept']);

                continue;
            }

            $product = Product::query()->whereKey($missing->product_id)->lockForUpdate()->first();

            if ($product === null) {
                $missing->update(['action' => 'kept']);

                continue;
            }

            $product->is_active = false;
            $product->save();

            $missing->update(['action' => 'deactivated']);
            $deactivated++;

            AuditService::log(
                'price_import.deactivate_missing',
                $product,
                ['is_active' => false],
                'Product "'.$product->name.'" deactivated because it was missing from confirmed complete catalogue '.$batch->original_filename.'.',
            );
        }

        return $deactivated;
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $base = $base !== '' ? $base : 'product';
        $slug = $base;
        $suffix = 2;

        while (Product::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
