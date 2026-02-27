<?php

// database/migrations/2024_01_01_000001_create_membership_tiers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Bronze, Silver, Gold, Emerald
            $table->string('slug')->unique(); // bronze, silver, gold, emerald
            $table->integer('min_referrals')->default(0); // 0, 10, 100, 1000
            $table->decimal('annual_fee', 10, 2)->default(5000.00);
            $table->decimal('food_package_value', 10, 2)->default(0);
            $table->string('distribution_frequency')->default('annual'); // annual, quarterly
            $table->text('benefits')->nullable(); // JSON or text
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_tiers');
    }
};