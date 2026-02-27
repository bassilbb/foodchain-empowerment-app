<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Denormalized table for faster referral queries
        Schema::create('referral_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('direct_referrals')->default(0);
            $table->integer('active_referrals')->default(0); // Paid members only
            $table->decimal('total_referral_value', 10, 2)->default(0); // Total payments from referrals
            $table->timestamp('last_referral_at')->nullable();
            $table->timestamps();
            
            $table->unique('user_id');
        });

        // Track each referral activity for history
        Schema::create('referral_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('activity_type', ['registered', 'paid_membership', 'paid_starter', 'tier_upgraded']);
            $table->text('details')->nullable();
            $table->timestamps();
            
            $table->index(['referrer_id', 'activity_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_activities');
        Schema::dropIfExists('referral_stats');
    }
};
