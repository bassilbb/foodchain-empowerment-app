<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_support_plan_contributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('food_support_plan_id');
            $table->foreign('food_support_plan_id', 'fk_contrib_plan_id')->references('id')->on('food_support_plans')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('contribution_date');
            $table->string('payment_reference')->nullable();
            $table->string('payment_method')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_support_plan_contributions');
    }
};
