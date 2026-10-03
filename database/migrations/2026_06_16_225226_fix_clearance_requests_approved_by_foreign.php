<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clearance_requests', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->foreign('approved_by')->references('id')->on('admin_users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('clearance_requests', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }
};
