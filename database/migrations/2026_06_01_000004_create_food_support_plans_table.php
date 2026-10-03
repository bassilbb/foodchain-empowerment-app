<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_support_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reference')->unique();
            $table->enum('plan_type', ['six_months', 'one_year']);
            $table->decimal('plan_value', 10, 2);
            $table->decimal('top_up_percentage', 5, 2)->default(20.00);
            $table->decimal('top_up_amount', 10, 2)->default(0);
            $table->decimal('total_payout_value', 10, 2)->default(0);
            $table->decimal('total_contributed', 10, 2)->default(0);
            $table->enum('status', ['pending', 'active', 'fully_funded', 'matured', 'withdrawn', 'cancelled'])->default('pending');
            $table->date('start_date')->nullable();
            $table->date('maturity_date')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_support_plans');
    }
};
