<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('state');
            $table->string('lga')->nullable();
            $table->decimal('delivery_fee', 14, 2)->default(0);
            $table->decimal('min_order_amount', 14, 2)->nullable();
            $table->unsignedInteger('estimated_days')->default(3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['state']);
            $table->index(['is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_delivery_zones');
    }
};
