<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manual_payments', function (Blueprint $table) {
            $table->foreignId('marketplace_order_id')
                ->nullable()
                ->after('payment_id')
                ->constrained('marketplace_orders')
                ->nullOnDelete();

            $table->index('marketplace_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('manual_payments', function (Blueprint $table) {
            $table->dropForeign(['marketplace_order_id']);
            $table->dropColumn('marketplace_order_id');
        });
    }
};
