<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('starter_packs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount_paid', 10, 2)->default(20000.00);
            $table->decimal('package_value', 10, 2)->default(22000.00);
            $table->enum('status', ['pending', 'paid', 'packaged', 'dispatched', 'delivered'])->default('pending');
            $table->date('purchased_at')->nullable();
            $table->date('delivered_at')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('starter_packs');
    }
};