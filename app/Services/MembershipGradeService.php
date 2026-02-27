<?php

namespace App\Services;

use App\Models\User;
use App\Models\MembershipTier;
use App\Models\QuarterlyBenefitEligibility;
use App\Models\Union;
use App\Models\UnionMember;
use App\Models\FoodLoan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MembershipGradeService
{
    private const FOOD_PACK_VALUES = [
        'mini' => 5000,
        'average' => 10000,
        'standard' => 25000,
        'premium' => 50000,
    ];

    public function getGradePricing(MembershipTier $tier): array
    {
        return [
            'annual_subscription' => [
                'amount' => (float) $tier->annual_fee,
                'amount_kobo' => (int) $tier->annual_fee_kobo,
                'currency' => 'NGN',
                'is_free' => $tier->hasFreeMembership(),
            ],
            'instant_food_pack' => [
                'amount' => (float) $tier->instant_food_pack_price,
                'amount_kobo' => (int) $tier->instant_food_pack_price_kobo,
                'currency' => 'NGN',
            ],
        ];
    }

    public function getDeliveryPricing(MembershipTier $tier, float $baseFee = 1500): array
    {
        return [
            'has_delivery' => $tier->has_delivery,
            'base_fee' => $baseFee,
            'calculated_fee' => $tier->calculateDeliveryFee($baseFee),
            'is_free' => $tier->hasFreeDelivery(),
            'subsidy_percentage' => (float) $tier->delivery_subsidy_percentage,
            'subsidy_amount' => $tier->hasSubsidizedDelivery() ? $tier->getDeliverySubsidyAmount($baseFee) : 0,
        ];
    }

    public function getCommunityBenefits(MembershipTier $tier): array
    {
        return [
            'community_building' => [
                'min_members' => $tier->community_min_members,
                'max_members' => $tier->community_max_members,
                'qualifies_for_free_food' => true,
                'food_pack_type' => $tier->free_food_pack_type,
                'food_pack_value' => self::FOOD_PACK_VALUES[$tier->free_food_pack_type] ?? 0,
                'distribution_frequency' => 'quarterly',
            ],
            'union_requirements' => $tier->getUnionRequirements(),
            'target_audience' => $tier->getTargetAudiences(),
            'community_category' => $tier->community_category,
        ];
    }

    public function getFullGradeDetails(MembershipTier $tier): array
    {
        return [
            'id' => $tier->id,
            'name' => $tier->name,
            'slug' => $tier->slug,
            'pricing' => $this->getGradePricing($tier),
            'delivery' => $this->getDeliveryPricing($tier),
            'community_benefits' => $this->getCommunityBenefits($tier),
            'benefits' => $tier->benefits ?? [],
            'extended_benefits' => $tier->extended_benefits ?? [],
            'sort_order' => $tier->sort_order,
            'is_active' => $tier->is_active,
        ];
    }

    public function calculateUserCommunityCount(User $user): int
    {
        $directReferrals = $user->direct_referrals_count;
        
        $downlineCount = $this->getTotalDownlineCount($user);
        
        $totalCommunity = $directReferrals + $downlineCount;
        
        if ($totalCommunity !== $user->total_community_count) {
            $user->update(['total_community_count' => $totalCommunity]);
        }

        return $totalCommunity;
    }

    private function getTotalDownlineCount(User $user, int $depth = 10): int
    {
        $total = 0;
        
        $children = $user->referrals()->get();
        
        foreach ($children as $child) {
            $total += 1 + $this->getTotalDownlineCount($child, $depth - 1);
        }
        
        return $total;
    }

    public function checkBenefitEligibility(User $user): array
    {
        $tier = $user->currentTier;
        
        if (!$tier) {
            return [
                'eligible' => false,
                'reason' => 'No membership tier assigned',
            ];
        }

        $communityCount = $user->total_community_count > 0 
            ? $user->total_community_count 
            : $this->calculateUserCommunityCount($user);

        $qualifies = $tier->qualifiesForFreeFoodPack($communityCount);

        return [
            'eligible' => $qualifies,
            'tier' => $tier->name,
            'community_count' => $communityCount,
            'min_required' => $tier->community_min_members,
            'max_allowed' => $tier->community_max_members,
            'food_pack_type' => $tier->free_food_pack_type,
            'food_pack_value' => self::FOOD_PACK_VALUES[$tier->free_food_pack_type] ?? 0,
        ];
    }

    public function evaluateQuarterlyEligibility(User $user): QuarterlyBenefitEligibility
    {
        $tier = $user->currentTier;
        
        if (!$tier) {
            throw new \Exception('User has no membership tier');
        }

        $communityCount = $this->calculateUserCommunityCount($user);
        
        $eligibility = $this->checkBenefitEligibility($user);

        $quarter = now()->quarter;
        $year = now()->year;

        $record = QuarterlyBenefitEligibility::firstOrNew([
            'user_id' => $user->id,
            'quarter' => $quarter,
            'year' => $year,
        ]);

        $record->membership_tier_id = $tier->id;
        $record->qualification_type = $eligibility['eligible'] ? 'community_based' : 'none';
        $record->food_pack_type = $eligibility['food_pack_type'] ?? null;
        $record->community_count_at_qualification = $communityCount;
        $record->is_qualified = $eligibility['eligible'];
        
        if ($eligibility['eligible'] && !$record->qualification_date) {
            $record->qualification_date = now();
        }

        $record->save();

        Log::info('Quarterly benefit eligibility evaluated', [
            'user_id' => $user->id,
            'quarter' => $quarter,
            'year' => $year,
            'is_qualified' => $eligibility['eligible'],
            'community_count' => $communityCount,
        ]);

        return $record;
    }

    public function processQuarterlyBenefitsForAllUsers(): array
    {
        $results = [
            'total_evaluated' => 0,
            'total_qualified' => 0,
            'errors' => [],
        ];

        User::active()
            ->whereNotNull('current_tier_id')
            ->chunk(100, function ($users) use (&$results) {
                foreach ($users as $user) {
                    try {
                        DB::transaction(function () use ($user, &$results) {
                            $eligibility = $this->evaluateQuarterlyEligibility($user);
                            $results['total_evaluated']++;
                            
                            if ($eligibility->is_qualified) {
                                $results['total_qualified']++;
                            }
                        });
                    } catch (\Exception $e) {
                        $results['errors'][] = [
                            'user_id' => $user->id,
                            'error' => $e->getMessage(),
                        ];
                    }
                }
            });

        Log::info('Quarterly benefits processed', $results);

        return $results;
    }

    public function createUnion(User $leader, string $name, ?MembershipTier $tier = null): Union
    {
        $tier = $tier ?? $leader->currentTier;

        if (!$tier) {
            throw new \Exception('Leader must have a membership tier');
        }

        $existingUnion = Union::byLeader($leader->id)
            ->whereIn('status', ['forming', 'active', 'disbursed', 'repaying'])
            ->first();

        if ($existingUnion) {
            throw new \Exception('User already has an active union');
        }

        return DB::transaction(function () use ($leader, $name, $tier) {
            $union = Union::create([
                'name' => $name,
                'code' => Union::generateUniqueCode(),
                'leader_id' => $leader->id,
                'membership_tier_id' => $tier->id,
                'member_count' => 1,
                'equity_percentage_required' => $tier->union_equity_percentage,
                'status' => 'forming',
            ]);

            $union->members()->create([
                'user_id' => $leader->id,
                'is_leader' => true,
                'joined_at' => now(),
            ]);

            Log::info('Union created', [
                'union_id' => $union->id,
                'leader_id' => $leader->id,
                'name' => $name,
            ]);

            return $union;
        });
    }

    public function joinUnion(User $user, Union $union, float $equityContributed = 0): UnionMember
    {
        if (!$union->canAddMember()) {
            throw new \Exception('Cannot join union in current status');
        }

        $userActiveUnion = Union::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('is_active', true);
        })->whereIn('status', ['forming', 'active', 'disbursed', 'repaying'])
          ->exists();

        if ($userActiveUnion) {
            throw new \Exception('User already belongs to an active union');
        }

        return DB::transaction(function () use ($user, $union, $equityContributed) {
            $member = $union->addMember($user, $equityContributed);

            Log::info('Member joined union', [
                'union_id' => $union->id,
                'user_id' => $user->id,
                'equity_contributed' => $equityContributed,
            ]);

            return $member;
        });
    }

    public function applyForFoodLoan(Union $union, float $amount): FoodLoan
    {
        if (!$union->canApplyForLoan()) {
            throw new \Exception('Union does not meet requirements to apply for loan');
        }

        $equityRequired = $union->getEquityRequired();

        if ($union->total_equity_contributed < $equityRequired) {
            throw new \Exception(
                "Union must contribute at least ₦" . number_format($equityRequired, 2) . 
                " in equity (30% of loan amount). Current: ₦" . number_format($union->total_equity_contributed, 2)
            );
        }

        $tier = $union->tier;

        return DB::transaction(function () use ($union, $amount, $tier) {
            $union->applyForLoan($amount);

            $foodLoan = FoodLoan::create([
                'union_id' => $union->id,
                'reference' => FoodLoan::generateUniqueReference(),
                'principal_amount' => $amount,
                'interest_rate' => 0,
                'total_repayment_amount' => $amount,
                'tenor_months' => $tier->union_loan_tenor_months,
                'repayment_frequency_weeks' => $tier->union_repayment_frequency_weeks,
                'total_installments' => ($tier->union_loan_tenor_months * 4) / $tier->union_repayment_frequency_weeks,
                'remaining_balance' => $amount,
                'status' => 'pending',
            ]);

            Log::info('Food loan application created', [
                'loan_id' => $foodLoan->id,
                'union_id' => $union->id,
                'amount' => $amount,
            ]);

            return $foodLoan;
        });
    }

    public function approveFoodLoan(FoodLoan $foodLoan, float $approvedAmount = null): void
    {
        $approvedAmount = $approvedAmount ?? $foodLoan->principal_amount;

        DB::transaction(function () use ($foodLoan, $approvedAmount) {
            $foodLoan->update([
                'loan_amount_approved' => $approvedAmount,
                'principal_amount' => $approvedAmount,
                'total_repayment_amount' => $approvedAmount,
                'remaining_balance' => $approvedAmount,
                'status' => 'approved',
            ]);

            $foodLoan->union->approveLoan($approvedAmount);

            Log::info('Food loan approved', [
                'loan_id' => $foodLoan->id,
                'approved_amount' => $approvedAmount,
            ]);
        });
    }

    public function disburseFoodLoan(FoodLoan $foodLoan, float $amount = null): void
    {
        $amount = $amount ?? $foodLoan->loan_amount_approved;

        if ($amount > $foodLoan->loan_amount_approved) {
            throw new \Exception('Disbursement amount cannot exceed approved amount');
        }

        DB::transaction(function () use ($foodLoan, $amount) {
            $foodLoan->disburse($amount);

            Log::info('Food loan disbursed', [
                'loan_id' => $foodLoan->id,
                'disbursed_amount' => $amount,
            ]);
        });
    }

    public function recordLoanRepayment(FoodLoan $foodLoan, User $user, float $amount): void
    {
        $member = $foodLoan->union->members()->where('user_id', $user->id)->first();

        if (!$member) {
            throw new \Exception('User is not a member of this union');
        }

        $nextInstallment = $foodLoan->installments_paid + 1;

        DB::transaction(function () use ($foodLoan, $amount, $nextInstallment) {
            $foodLoan->recordPayment($amount, $nextInstallment);

            Log::info('Loan repayment recorded', [
                'loan_id' => $foodLoan->id,
                'amount' => $amount,
                'installment' => $nextInstallment,
            ]);
        });
    }

    public function getUserMembershipSummary(User $user): array
    {
        $tier = $user->currentTier;
        
        if (!$tier) {
            return [
                'error' => 'No membership tier assigned',
            ];
        }

        $communityCount = $this->calculateUserCommunityCount($user);
        $benefitEligibility = $this->checkBenefitEligibility($user);

        $activeUnion = Union::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('is_active', true);
        })->whereIn('status', ['forming', 'active', 'disbursed', 'repaying'])
          ->first();

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'membership_status' => $user->isMembershipActive() ? 'active' : 'expired',
                'membership_expires_at' => $user->membership_expires_at?->format('Y-m-d'),
            ],
            'current_grade' => $this->getFullGradeDetails($tier),
            'community' => [
                'direct_referrals' => $user->direct_referrals_count,
                'total_community' => $communityCount,
                'benefit_eligibility' => $benefitEligibility,
            ],
            'union' => $activeUnion ? [
                'id' => $activeUnion->id,
                'name' => $activeUnion->name,
                'code' => $activeUnion->code,
                'status' => $activeUnion->status,
                'member_count' => $activeUnion->member_count,
                'equity_contributed' => (float) $activeUnion->total_equity_contributed,
                'loan_balance' => $activeUnion->getLoanBalance(),
            ] : null,
            'quarterly_benefits' => [
                'current' => QuarterlyBenefitEligibility::currentQuarter()
                    ->where('user_id', $user->id)
                    ->first(),
            ],
        ];
    }
}
