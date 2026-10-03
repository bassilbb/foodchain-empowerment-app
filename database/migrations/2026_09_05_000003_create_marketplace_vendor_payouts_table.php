<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Vendor payout ledger.
 *
 * The marketplace had no record of money actually paid out to vendors:
 * `marketplace_vendors.wallet_balance` exists as a column but is never credited
 * anywhere in the application, and earnings were only ever computed on the fly
 * from delivered order items. This table records payout requests and their
 * settlement so "paid" and "pending" are facts rather than guesses.
 *
 * Amounts are stored as computed at request time; they are never recalculated
 * from current order data, so a later commission-rate change cannot rewrite
 * history (matches the order-integrity rule used for order totals).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_vendor_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('marketplace_vendors')->cascadeOnDelete();

            $table->string('reference')->unique();
            $table->decimal('amount', 14, 2);

            // Snapshot of what the payout was calculated from.
            $table->decimal('gross_sales', 14, 2)->default(0);
            $table->decimal('commission_amount', 14, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->decimal('other_deductions', 14, 2)->default(0);

            $table->enum('status', ['requested', 'approved', 'processing', 'paid', 'rejected', 'cancelled'])
                ->default('requested');

            $table->string('payout_method')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();

            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamp('requested_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Who actioned it (admin_users or users).
            $table->string('processed_by_type')->nullable();
            $table->unsignedBigInteger('processed_by_id')->nullable();

            $table->timestamps();

            $table->index(['vendor_id', 'status']);
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_vendor_payouts');
    }
};
