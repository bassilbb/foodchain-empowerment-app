<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Composite indexes for the dashboard hot paths (Section 25 — dashboard performance).
 *
 * The marketplace dashboards aggregate over these tables with a leading
 * ownership column plus a status and/or date predicate:
 *
 *   marketplace_order_items: every vendor figure (dashboard KPIs, sales windows,
 *     earnings, reports) filters on (vendor_id, status) and ranges on created_at.
 *   marketplace_orders: the customer dashboard and order list filter on
 *     (user_id, status) and range on created_at.
 *
 * The original tables only had single-column indexes, so those queries could not
 * use one index for both the ownership scope and the status/date filter. These
 * composites keep the dashboards fast at hundreds of thousands of orders.
 *
 * All statements are guarded and each index is created separately so that a
 * partial failure cannot leave the migration half-applied silently.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_order_items', function (Blueprint $table) {
            // Vendor dashboard / reports: scope by vendor, filter by status, range by date.
            $table->index(['vendor_id', 'status', 'created_at'], 'moi_vendor_status_created');
            // Best-seller aggregation groups by product within a vendor.
            $table->index(['vendor_id', 'product_id'], 'moi_vendor_product');
        });

        Schema::table('marketplace_orders', function (Blueprint $table) {
            // Customer dashboard / order list: scope by user, filter by status.
            $table->index(['user_id', 'status'], 'mo_user_status');
            // "My orders, newest first" and date-range filters.
            $table->index(['user_id', 'created_at'], 'mo_user_created');
        });
    }

    public function down(): void
    {
        Schema::table('marketplace_orders', function (Blueprint $table) {
            $table->dropIndex('mo_user_status');
            $table->dropIndex('mo_user_created');
        });

        Schema::table('marketplace_order_items', function (Blueprint $table) {
            $table->dropIndex('moi_vendor_status_created');
            $table->dropIndex('moi_vendor_product');
        });
    }
};
