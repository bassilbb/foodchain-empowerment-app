<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('target_amount', 14, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('savings_plan_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('savings_plan_id')->constrained()->cascadeOnDelete();
            $table->integer('duration_weeks');
            $table->decimal('weekly_contribution', 14, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['savings_plan_id', 'duration_weeks']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_plan_options');
        Schema::dropIfExists('savings_plans');
    }
};
