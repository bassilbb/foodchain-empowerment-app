<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Templates for food packages per tier
        Schema::create('food_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tier_id')->constrained('membership_tiers')->cascadeOnDelete();
            $table->string('name'); // e.g., "Silver Annual Package 2024", "Emerald Q1 2024"
            $table->text('description')->nullable();
            $table->decimal('total_value', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false); // Default package for tier
            $table->timestamps();
            
            $table->index(['tier_id', 'is_active']);
        });

        // Items within each package
        Schema::create('food_package_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('food_packages')->cascadeOnDelete();
            $table->foreignId('food_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 10, 2); // e.g., 5 kg of rice
            $table->decimal('unit_value', 10, 2); // Value per unit for this item
            $table->timestamps();
            
            $table->unique(['package_id', 'food_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_package_items');
        Schema::dropIfExists('food_packages');
    }
};
