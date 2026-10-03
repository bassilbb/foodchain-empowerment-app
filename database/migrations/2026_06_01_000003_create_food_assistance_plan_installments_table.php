<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_assistance_plan_installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('food_assistance_plan_id');
            $table->foreign('food_assistance_plan_id', 'fk_inst_plan_id')->references('id')->on('food_assistance_plans')->onDelete('cascade');
            $table->unsignedTinyInteger('installment_number');
            $table->enum('type', ['processing_fee', 'first_installment', 'weekly_repayment']);
            $table->decimal('amount_due', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->string('payment_reference')->nullable();
            $table->string('payment_method')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_assistance_plan_installments');
    }
};
