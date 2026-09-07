<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('amount', 14, 2);
            $table->enum('type', ['debit', 'credit'])->default('credit');
            $table->string('reference')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->decimal('balance_after', 14, 2)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('order_id');
        });

        Schema::create('marketplace_loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->integer('points');
            $table->enum('type', ['earn', 'spend', 'adjust'])->default('earn');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->integer('balance_after')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_loyalty_points');
        Schema::dropIfExists('marketplace_wallet_transactions');
    }
};
