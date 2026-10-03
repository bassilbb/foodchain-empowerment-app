<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('marketplace_vendors');
            $table->foreignId('category_id')->constrained('marketplace_categories');
            $table->foreignId('brand_id')->nullable()->constrained('marketplace_brands')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('weight', 12, 2)->nullable();
            $table->string('weight_unit')->nullable();
            $table->decimal('price', 14, 2);
            $table->decimal('discount_price', 14, 2)->nullable();
            $table->decimal('member_price', 14, 2)->nullable();
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(5);
            $table->text('description')->nullable();
            $table->text('ingredients')->nullable();
            $table->text('nutritional_info')->nullable();
            $table->text('storage_instructions')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('manufacturing_date')->nullable();
            $table->string('country_of_origin')->nullable();
            $table->string('video_url')->nullable();
            $table->string('batch_number')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->unsignedInteger('sold_count')->default(0);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id']);
            $table->index(['vendor_id']);
            $table->index(['brand_id']);
            $table->index(['is_active']);
            $table->index(['price']);
            $table->index(['is_featured']);
            $table->index(['created_at']);
            $table->index(['sold_count']);
            $table->index(['average_rating']);
        });

        Schema::create('marketplace_product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('marketplace_products')->cascadeOnDelete();
            $table->string('image_path');
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('product_id');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_product_images');
        Schema::dropIfExists('marketplace_products');
    }
};
