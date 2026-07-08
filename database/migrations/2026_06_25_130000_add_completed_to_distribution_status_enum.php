<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }
        DB::statement("ALTER TABLE food_distributions MODIFY COLUMN status ENUM('pending','approved','packaged','dispatched','delivered','completed','cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }
        DB::statement("ALTER TABLE food_distributions MODIFY COLUMN status ENUM('pending','approved','packaged','dispatched','delivered','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
