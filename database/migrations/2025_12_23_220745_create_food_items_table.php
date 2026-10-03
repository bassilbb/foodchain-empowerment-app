<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_items', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Rice, Beans, Oil, etc.
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('unit')->default('kg'); // kg, liters, pieces, packets, crate, tin
            $table->decimal('unit_price', 10, 2)->default(0); // For valuation
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_items');
    }
};
