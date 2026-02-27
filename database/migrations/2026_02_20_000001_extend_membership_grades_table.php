<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_tiers', function (Blueprint $table) {
            $table->decimal('annual_fee_kobo', 12, 2)->default(500000)->after('annual_fee');
            $table->decimal('instant_food_pack_price', 10, 2)->default(20000)->after('annual_fee_kobo');
            $table->decimal('instant_food_pack_price_kobo', 12, 2)->default(2000000)->after('instant_food_pack_price');
            $table->integer('community_min_members')->default(10)->after('min_referrals');
            $table->integer('community_max_members')->default(40)->after('community_min_members');
            $table->string('free_food_pack_type')->default('mini')->after('community_max_members');
            $table->boolean('has_delivery')->default(true)->after('is_active');
            $table->decimal('delivery_fee', 10, 2)->default(0)->after('has_delivery');
            $table->decimal('delivery_subsidy_percentage', 5, 2)->default(0)->after('delivery_fee');
            $table->boolean('free_delivery')->default(false)->after('delivery_subsidy_percentage');
            $table->string('target_audience')->default('low_income_households,micro_business_owners,working_class_individuals,students')->after('is_active');
            $table->string('community_category')->default('all_categories')->after('target_audience');
            $table->integer('union_min_members')->default(10)->after('community_category');
            $table->decimal('union_equity_percentage', 5, 2)->default(30)->after('union_min_members');
            $table->integer('union_loan_tenor_months')->default(3)->after('union_equity_percentage');
            $table->integer('union_repayment_frequency_weeks')->default(2)->after('union_loan_tenor_months');
            $table->json('extended_benefits')->nullable()->after('benefits');
            $table->index('slug');
            $table->index('min_referrals');
            $table->index('community_min_members');
            $table->index('community_max_members');
        });

        Schema::create('unions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->foreignId('leader_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('membership_tier_id')->constrained();
            $table->integer('member_count')->default(1);
            $table->decimal('total_equity_contributed', 14, 2)->default(0);
            $table->decimal('loan_amount_applied', 14, 2)->default(0);
            $table->decimal('loan_amount_approved', 14, 2)->default(0);
            $table->decimal('loan_amount_disbursed', 14, 2)->default(0);
            $table->decimal('loan_amount_repaid', 14, 2)->default(0);
            $table->decimal('equity_percentage_required', 5, 2)->default(30);
            $table->enum('status', ['forming', 'active', 'disbursed', 'repaying', 'completed', 'defaulted'])->default('forming');
            $table->date('loan_start_date')->nullable();
            $table->date('loan_end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('leader_id');
            $table->index('status');
            $table->index('code');
        });

        Schema::create('union_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('union_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('equity_contributed', 14, 2)->default(0);
            $table->decimal('equity_percentage', 5, 2)->default(0);
            $table->boolean('is_leader')->default(false);
            $table->boolean('is_active')->default(true);
            $table->date('joined_at')->useCurrent();
            $table->timestamps();
            $table->unique(['union_id', 'user_id']);
            $table->index('user_id');
        });

        Schema::create('food_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('union_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->decimal('principal_amount', 14, 2);
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->decimal('total_repayment_amount', 14, 2);
            $table->decimal('amount_disbursed', 14, 2)->default(0);
            $table->decimal('amount_repaid', 14, 2)->default(0);
            $table->decimal('remaining_balance', 14, 2)->default(0);
            $table->integer('tenor_months')->default(3);
            $table->integer('repayment_frequency_weeks')->default(2);
            $table->integer('total_installments')->default(6);
            $table->integer('installments_paid')->default(0);
            $table->enum('status', ['pending', 'approved', 'disbursed', 'repaying', 'completed', 'defaulted'])->default('pending');
            $table->date('disbursement_date')->nullable();
            $table->date('first_repayment_date')->nullable();
            $table->date('final_repayment_date')->nullable();
            $table->json('repayment_schedule')->nullable();
            $table->timestamps();
            $table->index('union_id');
            $table->index('status');
            $table->index('reference');
        });

        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_loan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('union_member_id')->constrained('union_members')->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->integer('installment_number');
            $table->decimal('amount_due', 14, 2);
            $table->decimal('amount_paid', 14, 2)->default(0);
            $table->decimal('principal_portion', 14, 2)->default(0);
            $table->decimal('interest_portion', 14, 2)->default(0);
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->enum('status', ['pending', 'paid', 'partial', 'overdue', 'waived'])->default('pending');
            $table->json('payment_details')->nullable();
            $table->timestamps();
            $table->index('food_loan_id');
            $table->index('union_member_id');
            $table->index('status');
            $table->index('due_date');
        });

        Schema::create('quarterly_benefit_eligibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('membership_tier_id')->constrained();
            $table->string('qualification_type');
            $table->string('food_pack_type');
            $table->integer('community_count_at_qualification');
            $table->boolean('is_qualified')->default(false);
            $table->boolean('benefit_claimed')->default(false);
            $table->date('qualification_date')->nullable();
            $table->date('claim_date')->nullable();
            $table->integer('quarter')->default(1);
            $table->integer('year')->default(date('Y'));
            $table->timestamps();
            $table->index(['user_id', 'quarter', 'year']);
            $table->index('is_qualified');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('total_community_count')->default(0)->after('direct_referrals_count');
            $table->date('last_benefit_qualification_date')->nullable()->after('total_community_count');
            $table->index('total_community_count');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('union_id')->nullable()->constrained('unions')->nullOnDelete();
            $table->foreignId('food_loan_id')->nullable()->constrained('food_loans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['union_id']);
            $table->dropForeign(['food_loan_id']);
            $table->dropColumn(['union_id', 'food_loan_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['total_community_count', 'last_benefit_qualification_date']);
        });

        Schema::dropIfExists('quarterly_benefit_eligibilities');
        Schema::dropIfExists('loan_repayments');
        Schema::dropIfExists('food_loans');
        Schema::dropIfExists('union_members');
        Schema::dropIfExists('unions');

        Schema::table('membership_tiers', function (Blueprint $table) {
            $table->dropColumn([
                'annual_fee_kobo',
                'instant_food_pack_price',
                'instant_food_pack_price_kobo',
                'community_min_members',
                'community_max_members',
                'free_food_pack_type',
                'has_delivery',
                'delivery_fee',
                'delivery_subsidy_percentage',
                'free_delivery',
                'target_audience',
                'community_category',
                'union_min_members',
                'union_equity_percentage',
                'union_loan_tenor_months',
                'union_repayment_frequency_weeks',
                'extended_benefits',
            ]);
        });
    }
};
