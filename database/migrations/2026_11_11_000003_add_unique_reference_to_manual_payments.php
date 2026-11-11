<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Guarantee payment references are unique at the database level so duplicate
 * customer notifications can never create two financial records. If legacy
 * data already contains duplicates the index is skipped (the application
 * still enforces uniqueness) instead of failing the deployment.
 */
return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('manual_payments')
            ->select('reference')
            ->groupBy('reference')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        if ($duplicates > 0) {
            return;
        }

        Schema::table('manual_payments', function (Blueprint $table) {
            $table->unique('reference');
        });
    }

    public function down(): void
    {
        Schema::table('manual_payments', function (Blueprint $table) {
            $table->dropUnique(['reference']);
        });
    }
};
