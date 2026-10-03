<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_products', function (Blueprint $table) {
            // Default 'approved' keeps every pre-existing product live; only
            // vendor-created submissions start life as 'pending' (set by the
            // vendor product controller, not the column default).
            $table->string('approval_status', 20)->default('approved');
            $table->timestamp('approved_at')->nullable();
            $table->string('rejection_reason', 500)->nullable();

            $table->index('approval_status');
        });
    }

    public function down(): void
    {
        Schema::table('marketplace_products', function (Blueprint $table) {
            $table->dropIndex(['approval_status']);
            $table->dropColumn(['approval_status', 'approved_at', 'rejection_reason']);
        });
    }
};
