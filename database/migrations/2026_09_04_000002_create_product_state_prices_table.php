<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_state_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('marketplace_products')->cascadeOnDelete();
            $table->foreignId('state_id')->constrained('nigerian_states')->cascadeOnDelete();
            $table->decimal('price', 18, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['product_id', 'state_id'], 'uq_product_state');
            $table->index(['state_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_state_prices');
    }
};
