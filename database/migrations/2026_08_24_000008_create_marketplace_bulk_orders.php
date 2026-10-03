<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_bulk_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['open', 'in_progress', 'completed', 'cancelled'])->default('open');
            $table->date('target_date')->nullable();
            $table->enum('delivery_type', ['community_collection', 'community_delivery_point', 'designated_location'])->default('community_collection');
            $table->string('delivery_location')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->unsignedInteger('total_required_qty')->default(0);
            $table->unsignedInteger('total_contributed_qty')->default(0);
            $table->unsignedInteger('total_contributors')->default(0);
            $table->decimal('total_value', 14, 2)->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('created_by');
            $table->index(['status']);
        });

        Schema::create('marketplace_bulk_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulk_order_id')->constrained('marketplace_bulk_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('marketplace_products');
            $table->unsignedInteger('required_quantity');
            $table->unsignedInteger('contributed_quantity')->default(0);
            $table->decimal('unit_price', 14, 2);
            $table->timestamps();

            $table->unique(['bulk_order_id', 'product_id']);
        });

        Schema::create('marketplace_bulk_order_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulk_order_id')->constrained('marketplace_bulk_orders')->cascadeOnDelete();
            $table->foreignId('bulk_order_item_id')->constrained('marketplace_bulk_order_items')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->unsignedInteger('quantity');
            $table->decimal('amount', 14, 2);
            $table->string('status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamps();

            $table->index('bulk_order_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_bulk_order_contributions');
        Schema::dropIfExists('marketplace_bulk_order_items');
        Schema::dropIfExists('marketplace_bulk_orders');
    }
};
