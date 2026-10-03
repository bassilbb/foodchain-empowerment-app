<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('primary_user_id')->constrained('users');
            $table->foreignId('user_account_id')->nullable()->constrained('user_accounts')->nullOnDelete();
            $table->decimal('amount', 14, 2);
            $table->enum('type', ['top_up', 'contribution', 'penalty', 'refund']);
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index('primary_user_id');
            $table->index('user_account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_transactions');
    }
};
