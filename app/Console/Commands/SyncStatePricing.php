<?php

namespace App\Console\Commands;

use App\Models\Marketplace\Product;
use App\Models\Marketplace\ProductStatePrice;
use App\Models\NigerianState;
use App\Services\Marketplace\MarketplacePricingService;
use Illuminate\Console\Command;

class SyncStatePricing extends Command
{
    protected $signature = 'marketplace:sync-state-pricing {--overwrite : Refresh existing per-state rows instead of only filling gaps}';

    protected $description = 'Seed local (state-specific) prices for every active product. Prices differ by state so the storefront "deliver-to" switch visibly updates prices.';

    public function handle(): int
    {
        $states = NigerianState::active()->ordered()->get(['id', 'name', 'code', 'region']);

        if ($states->isEmpty()) {
            $this->error('No active states found.');

            return self::FAILURE;
        }

        $factors = $states->mapWithKeys(fn ($s) => [
            $s->id => MarketplacePricingService::REGION_FACTORS[strtolower((string) $s->region)] ?? 1.0,
        ]);

        $existing = ProductStatePrice::query()
            ->select('product_id', 'state_id')
            ->get()
            ->map(fn ($r) => $r->product_id.':'.$r->state_id);

        $overwrite = (bool) $this->option('overwrite');
        $created = 0;

        // Cap chunks so insert bindings stay under SQLite's variable limit
        // (999 params). Each row = 6 bindings → max ~166 rows per statement.
        $bindGuard = (int) floor(900 / 6);

        Product::with('flashSaleItems.flashSale')->active()->chunkById(100, function ($products) use ($states, $factors, $existing, $overwrite, &$created, $bindGuard) {
            $rows = [];
            $now = now();

            foreach ($products as $product) {
                $base = (float) $product->selling_price ?: (float) $product->price;

                foreach ($states as $state) {
                    $rows[] = [
                        'product_id' => $product->id,
                        'state_id' => $state->id,
                        'price' => round($base * $factors[$state->id], 2),
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (! $rows) {
                return;
            }

            if ($overwrite) {
                foreach (array_chunk($rows, $bindGuard) as $batch) {
                    ProductStatePrice::query()->upsert(
                        $batch,
                        ['product_id', 'state_id'],
                        ['price', 'is_active', 'updated_at']
                    );
                }
                $created += count($rows);

                return;
            }

            // Cheap existence filter on the pre-loaded pair list avoids
            // per-row round trips.
            $rows = array_values(array_filter($rows, fn ($r) => ! $existing->contains($r['product_id'].':'.$r['state_id'])));
            if ($rows) {
                foreach (array_chunk($rows, $bindGuard) as $batch) {
                    ProductStatePrice::query()->insert($batch);
                }
                $created += count($rows);
            }
        });

        $this->info("Synced state prices for {$states->count()} states. Rows written: {$created}.");

        return self::SUCCESS;
    }
}