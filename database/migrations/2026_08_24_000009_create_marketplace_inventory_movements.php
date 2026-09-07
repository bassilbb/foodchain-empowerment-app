<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('marketplace_products')->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('marketplace_vendors')->nullOnDelete();
            $table->enum('type', ['stock_in', 'stock_out', 'adjustment', 'damaged', 'returned', 'sale'])->default('stock_in');
            $table->integer('quantity');
            $table->string('batch_number')->nullable();
            $table->string('reason')->nullable();
            $table->string('changed_by_type')->nullable();
            $table->unsignedBigInteger('changed_by_id')->nullable();
            $table->timestamps();

            $table->index('product_id');
            $table->index('vendor_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_inventory_movements');
    }
};
