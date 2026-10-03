<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_name');
            $table->string('account_type')->default('current');
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->index('is_active');
        });

        Schema::create('manual_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('bank_account_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->decimal('amount', 14, 2);
            $table->string('reference');
            $table->string('sender_name');
            $table->string('sender_account_number')->nullable();
            $table->string('sender_bank_name')->nullable();
            $table->dateTime('transfer_date');
            $table->string('proof_image_path');
            $table->string('status')->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('status');
            $table->index('reference');
        });

        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('food_distribution_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('manual_payment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('voucher_number')->unique();
            $table->string('status')->default('pending');
            $table->dateTime('issued_at')->nullable();
            $table->dateTime('collected_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->string('collection_code')->nullable();
            $table->foreignId('collected_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('voucher_number');
            $table->index('status');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('manual_payment_id')->nullable()->constrained('manual_payments')->nullOnDelete();
        });

        Schema::table('food_distributions', function (Blueprint $table) {
            $table->foreignId('voucher_id')->nullable()->after('tier_id')->constrained('vouchers')->nullOnDelete();
            $table->string('collection_status')->default('not_ready')->after('status');
            $table->string('collection_code')->nullable()->after('collection_status');
            $table->dateTime('collection_date')->nullable()->after('collection_code');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->string('payment_manual_enabled')->default('yes')->nullable()->after('value');
            $table->integer('voucher_validity_hours')->default(72)->nullable()->after('payment_manual_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['payment_manual_enabled', 'voucher_validity_hours']);
        });

        Schema::table('food_distributions', function (Blueprint $table) {
            $table->dropColumn(['voucher_id', 'collection_status', 'collection_code', 'collection_date']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['manual_payment_id']);
            $table->dropColumn('manual_payment_id');
        });

        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('manual_payments');
        Schema::dropIfExists('bank_accounts');
    }
};
