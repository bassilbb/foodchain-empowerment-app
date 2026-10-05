<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reconcile the marketplace delivery_type enums with what the application
 * actually accepts.
 *
 * Checkout validates and prices `express` and `same_day` (CheckoutController and
 * MarketplaceDeliveryService), and both appear in the checkout dropdown, but the
 * `marketplace_orders` column only allowed home_delivery / pickup /
 * community_delivery. On a CHECK-constrained driver the insert of the very first
 * row inside the placeOrder transaction therefore failed, so both options were
 * impossible to order with.
 *
 * Bulk orders had the mirror-image problem: the controller accepted
 * community_delivery / pickup while the column only allowed the seeded
 * community_collection / community_delivery_point / designated_location values.
 *
 * The bulk enum keeps every value already in the column so existing rows stay
 * valid; it only widens the column to cover what the form submits.
 *
 * Every `change()` below re-declares the sibling `status` enum as well. Laravel
 * rebuilds the whole table for a SQLite column change and does not carry the
 * untouched CHECK constraints across, so omitting `status` here would silently
 * drop the constraint guarding order statuses.
 */
return new class extends Migration
{
    private const ORDER_STATUSES = [
        'pending', 'paid', 'confirmed', 'processing',
        'packed', 'shipped', 'delivered', 'cancelled', 'refunded',
    ];

    private const BULK_STATUSES = ['open', 'in_progress', 'completed', 'cancelled'];

    private const ORDER_DELIVERY_TYPES = [
        'home_delivery', 'express', 'same_day', 'pickup', 'community_delivery',
    ];

    private const BULK_DELIVERY_TYPES = [
        'community_collection', 'community_delivery_point',
        'designated_location', 'community_delivery', 'pickup',
    ];

    public function up(): void
    {
        $this->setOrderDeliveryTypes(self::ORDER_DELIVERY_TYPES);
        $this->setBulkDeliveryTypes(self::BULK_DELIVERY_TYPES);
    }

    public function down(): void
    {
        // Narrowing only succeeds while no row holds a value the narrower enum
        // would reject, so skip it rather than fail the rollback.
        $orderValues = ['home_delivery', 'pickup', 'community_delivery'];
        $bulkValues = ['community_collection', 'community_delivery_point', 'designated_location'];

        if (! $this->hasUnsupportedValue('marketplace_orders', $orderValues)) {
            $this->setOrderDeliveryTypes($orderValues);
        }

        if (! $this->hasUnsupportedValue('marketplace_bulk_orders', $bulkValues)) {
            $this->setBulkDeliveryTypes($bulkValues);
        }
    }

    private function setOrderDeliveryTypes(array $values): void
    {
        if (! Schema::hasTable('marketplace_orders')) {
            return;
        }

        Schema::table('marketplace_orders', function (Blueprint $table) use ($values) {
            $table->enum('status', self::ORDER_STATUSES)->default('pending')->change();
            $table->enum('delivery_type', $values)->default('home_delivery')->change();
        });
    }

    private function setBulkDeliveryTypes(array $values): void
    {
        if (! Schema::hasTable('marketplace_bulk_orders')) {
            return;
        }

        Schema::table('marketplace_bulk_orders', function (Blueprint $table) use ($values) {
            $table->enum('status', self::BULK_STATUSES)->default('open')->change();
            $table->enum('delivery_type', $values)->default('community_collection')->change();
        });
    }

    private function hasUnsupportedValue(string $table, array $allowed): bool
    {
        if (! Schema::hasTable($table)) {
            return false;
        }

        return DB::table($table)->whereNotIn('delivery_type', $allowed)->exists();
    }
};
