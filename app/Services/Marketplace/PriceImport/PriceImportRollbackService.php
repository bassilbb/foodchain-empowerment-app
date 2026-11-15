<?php

namespace App\Services\Marketplace\PriceImport;

use App\Models\AdminUser;
use App\Models\Marketplace\PriceImportBatch;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\ProductPriceHistory;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;

/**
 * Restores the prices recorded by one completed bulk upload.
 *
 * Only product prices are reversed. Orders, inventory movements, stock levels
 * and products created by the batch are deliberately left untouched.
 */
class PriceImportRollbackService
{
    /**
     * @return array{restored: int, skipped: int, created_products: int}
     */
    public function rollback(PriceImportBatch $batch, AdminUser $admin, string $reason): array
    {
        $batch = PriceImportBatch::query()->whereKey($batch->id)->firstOrFail();

        if (! $batch->canRollback()) {
            throw new \RuntimeException('This upload cannot be rolled back.');
        }

        if ($batch->status === PriceImportBatch::STATUS_ROLLED_BACK) {
            throw new \RuntimeException('This upload has already been rolled back.');
        }

        $restored = 0;
        $skipped = 0;
        $createdProducts = 0;

        $histories = $batch->priceHistories()
            ->where('source', ProductPriceHistory::SOURCE_BULK_IMPORT)
            ->where('status', ProductPriceHistory::STATUS_APPLIED)
            ->orderByDesc('id')
            ->get();

        foreach ($histories as $history) {
            if ($history->old_price === null) {
                // A product created by this batch has no previous price to restore.
                $createdProducts++;

                continue;
            }

            DB::transaction(function () use ($history, $batch, $admin, $reason, &$restored, &$skipped) {
                $product = Product::query()->whereKey($history->product_id)->lockForUpdate()->first();

                if ($product === null) {
                    $skipped++;

                    return;
                }

                if (abs((float) $product->price - (float) $history->new_price) >= 0.01) {
                    // A later change superseded this batch; do not clobber it.
                    $skipped++;

                    return;
                }

                $oldPrice = round((float) $history->new_price, 2);
                $restoredPrice = round((float) $history->old_price, 2);

                $product->price = $restoredPrice;
                $product->save();

                $history->forceFill([
                    'status' => ProductPriceHistory::STATUS_ROLLED_BACK,
                    'rolled_back_at' => now(),
                    'note' => trim(($history->note ?? '').' | Rolled back: '.$reason),
                ])->save();

                ProductPriceHistory::create([
                    'product_id' => $product->id,
                    'vendor_id' => $product->vendor_id,
                    'batch_id' => $batch->id,
                    'admin_user_id' => $admin->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'source' => ProductPriceHistory::SOURCE_ROLLBACK,
                    'old_price' => $oldPrice,
                    'new_price' => $restoredPrice,
                    'difference' => round($restoredPrice - $oldPrice, 2),
                    'percent_change' => $oldPrice > 0 ? round((($restoredPrice - $oldPrice) / $oldPrice) * 100, 2) : null,
                    'status' => ProductPriceHistory::STATUS_APPLIED,
                    'note' => 'Rollback of bulk price upload #'.$batch->id.': '.$reason,
                ]);

                $restored++;
            });
        }

        $batch->update([
            'status' => PriceImportBatch::STATUS_ROLLED_BACK,
            'rolled_back_at' => now(),
            'rollback_admin_user_id' => $admin->id,
            'rollback_reason' => $reason,
            'rolled_back_count' => $restored,
        ]);

        AuditService::log(
            'price_import.rollback',
            $batch,
            ['restored' => $restored, 'skipped' => $skipped, 'reason' => $reason],
            'Bulk price upload "'.$batch->original_filename.'" rolled back by administrator.',
        );

        return [
            'restored' => $restored,
            'skipped' => $skipped,
            'created_products' => $createdProducts,
        ];
    }
}
