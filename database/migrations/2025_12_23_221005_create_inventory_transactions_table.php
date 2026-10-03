<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('pickup_locations')->nullOnDelete();
            $table->enum('type', ['stock_in', 'stock_out', 'adjustment', 'expired', 'damaged']);
            $table->decimal('quantity', 10, 2);
            $table->decimal('quantity_before', 10, 2);
            $table->decimal('quantity_after', 10, 2);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->string('reference')->nullable(); // Related distribution, purchase order, etc.
            $table->text('notes')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['food_item_id', 'type']);
            $table->index(['location_id', 'type']);
            $table->index('created_at');
            $table->index('reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
