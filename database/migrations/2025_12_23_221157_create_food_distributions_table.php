<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_distributions', function (Blueprint $table) {
            $table->id();
            $table->string('distribution_number')->unique(); // e.g., DIST-2024-00001
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tier_id')->constrained('membership_tiers');
            $table->foreignId('package_id')->nullable()->constrained('food_packages')->nullOnDelete();
            $table->enum('distribution_type', ['tier_reward', 'discretionary', 'starter_pack'])->default('tier_reward');
            $table->decimal('package_value', 10, 2);
            
            // Distribution details
            $table->enum('status', [
                'pending',      // Waiting for approval
                'approved',     // Approved by admin
                'packaged',     // Items packaged
                'dispatched',   // Sent for delivery
                'delivered',    // Successfully delivered
                'cancelled'     // Cancelled
            ])->default('pending');
            
            $table->timestamp('approved_at')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->timestamp('packaged_at')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            
            // Delivery information
            $table->foreignId('pickup_location_id')->nullable()->constrained('pickup_locations')->nullOnDelete();
            $table->text('delivery_address')->nullable();
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->enum('delivery_method', ['pickup', 'doorstep'])->default('pickup');
            
            // Admin tracking
            $table->foreignId('approved_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->foreignId('packaged_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->text('user_notes')->nullable();
            
            // Delivery tracking
            $table->string('tracking_number')->nullable();
            $table->string('courier_name')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'status']);
            $table->index(['tier_id', 'status']);
            $table->index('distribution_type');
            $table->index('scheduled_date');
            $table->index('status');
        });

        // Individual items in each distribution
        Schema::create('food_distribution_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribution_id')->constrained('food_distributions')->cascadeOnDelete();
            $table->foreignId('food_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_value', 10, 2); // Value per unit
            $table->decimal('total_value', 10, 2); // quantity * unit_value
            $table->timestamps();
            
            $table->index('distribution_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_distribution_items');
        Schema::dropIfExists('food_distributions');
    }
};