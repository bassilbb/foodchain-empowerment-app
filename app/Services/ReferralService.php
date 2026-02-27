<?php

namespace App\Services;

use App\Models\User;
use App\Models\ReferralActivity;
use App\Models\ReferralStat;
use Illuminate\Support\Facades\DB;

class ReferralService
{
    /**
     * Track a new referral when a user registers
     */
    public function trackReferral(User $referredUser, ?string $referralCode): bool
    {
        if (!$referralCode) {
            return false;
        }

        $referrer = User::where('referral_code', $referralCode)->first();

        if (!$referrer || $referrer->id === $referredUser->id) {
            return false;
        }

        DB::transaction(function () use ($referrer, $referredUser) {
            // Update referred_by on the new user
            $referredUser->update(['referred_by' => $referrer->id]);

            // Increment referrer's count
            $referrer->incrementReferralCount();

            // Update referral stats
            $stats = ReferralStat::firstOrCreate(['user_id' => $referrer->id]);
            $stats->incrementDirectReferrals();

            // Log the activity
            ReferralActivity::create([
                'referrer_id' => $referrer->id,
                'referred_user_id' => $referredUser->id,
                'activity_type' => 'registered',
                'details' => [
                    'referred_at' => now(),
                    'referral_code' => $referralCode,
                ],
            ]);
        });

        return true;
    }

    /**
     * Track when a referred user makes a payment
     */
    public function trackReferralPayment(User $user, string $paymentType, float $amount): void
    {
        if (!$user->referred_by) {
            return;
        }

        $referrer = $user->referrer;

        DB::transaction(function () use ($referrer, $user, $paymentType, $amount) {
            // Update stats
            $stats = ReferralStat::firstOrCreate(['user_id' => $referrer->id]);
            $stats->addReferralValue($amount);

            if ($user->is_active) {
                $stats->incrementActiveReferrals();
            }

            // Log activity
            $activityType = $paymentType === 'membership' ? 'paid_membership' : 'paid_starter';
            
            ReferralActivity::create([
                'referrer_id' => $referrer->id,
                'referred_user_id' => $user->id,
                'activity_type' => $activityType,
                'details' => [
                    'amount' => $amount,
                    'payment_type' => $paymentType,
                    'paid_at' => now(),
                ],
            ]);
        });
    }

    /**
     * Get referral tree for a user (genealogy)
     */
    public function getReferralTree(User $user, int $depth = 3): array
    {
        return $this->buildTree($user, $depth, 1);
    }

    private function buildTree(User $user, int $maxDepth, int $currentDepth): array
    {
        if ($currentDepth > $maxDepth) {
            return [];
        }

        $referrals = $user->referrals()->with('currentTier')->get();

        return $referrals->map(function ($referral) use ($maxDepth, $currentDepth) {
            return [
                'id' => $referral->id,
                'name' => $referral->full_name,
                'email' => $referral->email,
                'tier' => $referral->currentTier->name,
                'is_active' => $referral->is_active,
                'referral_count' => $referral->direct_referrals_count,
                'joined_at' => $referral->created_at->format('M d, Y'),
                'children' => $this->buildTree($referral, $maxDepth, $currentDepth + 1),
            ];
        })->toArray();
    }

    /**
     * Get referral statistics for a user
     */
    public function getReferralStats(User $user): array
    {
        $stats = ReferralStat::firstOrCreate(['user_id' => $user->id]);

        return [
            'direct_referrals' => $user->direct_referrals_count,
            'active_referrals' => $user->activeReferrals()->count(),
            'total_referral_value' => $stats->total_referral_value,
            'last_referral_at' => $stats->last_referral_at?->format('M d, Y'),
            'next_tier' => $user->getNextTier(),
            'referrals_needed' => $user->getReferralsNeededForNextTier(),
        ];
    }

    /**
     * Validate referral code
     */
    public function validateReferralCode(string $code): ?User
    {
        return User::where('referral_code', $code)
                   ->where('is_active', true)
                   ->first();
    }
}

