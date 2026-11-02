<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_orders', function (Blueprint $table) {
            $table->decimal('service_charge_rate', 8, 4)->default(0)->after('coupon_discount');
            $table->decimal('service_charge', 14, 2)->default(0)->after('service_charge_rate');
            $table->decimal('service_charge_amount', 14, 2)->default(0)->after('service_charge');
        });
    }

    public function down(): void
    {
        Schema::table('marketplace_orders', function (Blueprint $table) {
            $table->dropColumn(['service_charge_rate', 'service_charge', 'service_charge_amount']);
        });
    }
};
