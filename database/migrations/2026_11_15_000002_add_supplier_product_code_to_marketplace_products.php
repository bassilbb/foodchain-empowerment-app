<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_products', function (Blueprint $table) {
            $table->string('supplier_product_code')->nullable()->after('sku');
            $table->index('supplier_product_code');
        });
    }

    public function down(): void
    {
        Schema::table('marketplace_products', function (Blueprint $table) {
            $table->dropIndex(['supplier_product_code']);
            $table->dropColumn('supplier_product_code');
        });
    }
};
