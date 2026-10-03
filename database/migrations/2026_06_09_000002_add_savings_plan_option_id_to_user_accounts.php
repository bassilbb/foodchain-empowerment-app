<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_accounts', function (Blueprint $table) {
            $table->foreignId('savings_plan_option_id')
                ->nullable()
                ->constrained('savings_plan_options')
                ->nullOnDelete()
                ->after('selected_plan_id');

            $table->foreignId('selected_plan_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_accounts', function (Blueprint $table) {
            $table->dropForeign(['savings_plan_option_id']);
            $table->dropColumn('savings_plan_option_id');
        });
    }
};
