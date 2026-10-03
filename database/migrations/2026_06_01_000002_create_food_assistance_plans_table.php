<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_assistance_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tier_id')->constrained('food_assistance_plan_tiers')->onDelete('cascade');
            $table->string('reference')->unique();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('processing_fee', 10, 2)->default(3500.00);
            $table->decimal('first_installment_percentage', 5, 2)->default(30.00);
            $table->decimal('first_installment_amount', 10, 2);
            $table->decimal('balance_amount', 10, 2);
            $table->decimal('weekly_installment_amount', 10, 2);
            $table->unsignedTinyInteger('number_of_installments')->default(12);
            $table->unsignedTinyInteger('installments_paid')->default(0);
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->enum('status', ['pending', 'processing_fee_paid', 'first_installment_paid', 'active', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_assistance_plans');
    }
};
