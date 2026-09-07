<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('order_number')->unique();
            $table->enum('status', ['pending', 'paid', 'confirmed', 'processing', 'packed', 'shipped', 'delivered', 'cancelled', 'refunded'])->default('pending');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('shipping_fee', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->foreignId('coupon_id')->nullable()->constrained('marketplace_coupons')->nullOnDelete();
            $table->string('coupon_code')->nullable();
            $table->decimal('coupon_discount', 14, 2)->default(0);
            $table->string('payment_method')->default('wallet');
            $table->string('payment_status')->default('unpaid');
            $table->decimal('wallet_amount', 14, 2)->default(0);
            $table->decimal('cash_amount', 14, 2)->default(0);
            $table->enum('delivery_type', ['home_delivery', 'pickup', 'community_delivery'])->default('home_delivery');
            $table->text('delivery_address')->nullable();
            $table->string('delivery_city')->nullable();
            $table->string('delivery_state')->nullable();
            $table->string('delivery_lga')->nullable();
            $table->unsignedBigInteger('delivery_zone_id')->nullable();
            $table->date('estimated_delivery_date')->nullable();
            $table->string('pickup_location')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->string('payment_reference')->nullable();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->unsignedBigInteger('bulk_order_id')->nullable();
            $table->decimal('refund_amount', 14, 2)->default(0);
            $table->unsignedInteger('loyalty_points_earned')->default(0);
            $table->text('note')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('order_number');
            $table->index(['status']);
            $table->index(['created_at']);
        });

        Schema::create('marketplace_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('marketplace_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('marketplace_products');
            $table->foreignId('vendor_id')->nullable()->constrained('marketplace_vendors')->nullOnDelete();
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->decimal('unit_price', 14, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('total_price', 14, 2);
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('review_id')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('product_id');
            $table->index('vendor_id');
        });

        Schema::create('marketplace_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('marketplace_products')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('marketplace_orders')->nullOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('marketplace_order_items')->nullOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['product_id']);
            $table->index('user_id');
        });

        Schema::create('marketplace_order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('marketplace_orders')->cascadeOnDelete();
            $table->string('status');
            $table->text('note')->nullable();
            $table->string('changed_by_type')->nullable();
            $table->unsignedBigInteger('changed_by_id')->nullable();
            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_order_status_histories');
        Schema::dropIfExists('marketplace_reviews');
        Schema::dropIfExists('marketplace_order_items');
        Schema::dropIfExists('marketplace_orders');
    }
};
