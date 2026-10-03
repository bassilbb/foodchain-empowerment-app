<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Rebuild marketplace_products.sold_count from the order-items ledger.
 *
 * Product::decrementStock() is the only writer of sold_count, so any product
 * that reached the catalogue through a seeder, an import or a direct insert
 * kept a count of zero even after real customers bought it. That made the
 * "best sellers" lists on the storefront, the admin dashboard and vendor
 * product pages understate genuine sales.
 *
 * Voided orders are ignored so the counter agrees with Order::earning(),
 * which the sales reports and dashboard revenue use.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('marketplace_products')->where('sold_count', '!=', 0)->update(['sold_count' => 0]);

        $unitsByProduct = DB::table('marketplace_order_items as items')
            ->join('marketplace_orders as orders', 'orders.id', '=', 'items.order_id')
            ->whereNotIn('orders.status', ['cancelled', 'refunded'])
            ->groupBy('items.product_id')
            ->selectRaw('items.product_id, SUM(items.quantity) as units')
            ->get();

        foreach ($unitsByProduct->chunk(200) as $chunk) {
            foreach ($chunk as $row) {
                DB::table('marketplace_products')
                    ->where('id', $row->product_id)
                    ->update(['sold_count' => (int) $row->units]);
            }
        }
    }

    /**
     * Intentionally a no-op. The pre-migration values were derived from an
     * unmaintained counter, so restoring them would reinstate the bug.
     */
    public function down(): void
    {
    }
};
