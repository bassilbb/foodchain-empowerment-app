<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique(); // Paystack reference
            $table->enum('type', ['membership', 'starter_pack']); // Payment type
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'successful', 'failed', 'cancelled'])->default('pending');
            $table->string('payment_method')->default('paystack');
            $table->string('paystack_reference')->nullable();
            $table->text('paystack_response')->nullable(); // JSON response from Paystack
            $table->timestamp('paid_at')->nullable();
            $table->text('meta')->nullable(); // Additional payment metadata
            $table->timestamps();
            
            $table->index(['user_id', 'type']);
            $table->index('status');
            $table->index('reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
