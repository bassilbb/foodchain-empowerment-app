<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Track when users move between tiers
        Schema::create('tier_progression_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_tier_id')->nullable()->constrained('membership_tiers')->nullOnDelete();
            $table->foreignId('to_tier_id')->constrained('membership_tiers')->cascadeOnDelete();
            $table->integer('referral_count_at_upgrade');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tier_progression_logs');
    }
};
