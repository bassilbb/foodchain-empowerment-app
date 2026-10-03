<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('pickup_locations')->nullOnDelete();
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('reserved_quantity', 10, 2)->default(0); // Allocated but not dispatched
            $table->decimal('available_quantity', 10, 2)->default(0); // quantity - reserved
            $table->date('expiry_date')->nullable();
            $table->decimal('cost_per_unit', 10, 2)->default(0);
            $table->timestamps();

            $table->index(['food_item_id', 'location_id']);
            $table->index('expiry_date');

            // Ensure unique combination of food_item and location
            $table->unique(['food_item_id', 'location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
