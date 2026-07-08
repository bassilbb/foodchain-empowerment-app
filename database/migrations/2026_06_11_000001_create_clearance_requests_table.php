<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clearance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_account_id')->constrained('user_accounts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending_payment', 'pending_approval', 'approved'])->default('pending_payment');
            $table->decimal('fee_amount', 14, 2)->default(3000);
            $table->timestamp('fee_paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->unique('user_account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clearance_requests');
    }
};
