<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add the verification metadata required by the Payment Verification
 * dashboard. Existing rows keep working because every new column has a
 * sensible default or is nullable.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manual_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('manual_payments', 'currency')) {
                $table->string('currency', 3)->default('NGN')->after('amount');
            }

            if (! Schema::hasColumn('manual_payments', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('status');
            }

            if (! Schema::hasColumn('manual_payments', 'verification_status')) {
                $table->string('verification_status')->default('pending')->after('payment_status');
            }

            if (! Schema::hasColumn('manual_payments', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('admin_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('manual_payments', function (Blueprint $table) {
            $table->dropColumn(['currency', 'payment_status', 'verification_status', 'rejection_reason']);
        });
    }
};
