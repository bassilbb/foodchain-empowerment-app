<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('marketplace_products')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->boolean('saved_for_later')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'product_id']);
            $table->index('user_id');
        });

        Schema::create('marketplace_saved_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('marketplace_products')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'product_id']);
        });

        Schema::create('marketplace_recent_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('marketplace_products')->cascadeOnDelete();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'product_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_recent_views');
        Schema::dropIfExists('marketplace_saved_products');
        Schema::dropIfExists('marketplace_cart_items');
    }
};
