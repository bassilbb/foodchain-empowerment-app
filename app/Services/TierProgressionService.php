<?php

namespace App\Services;

use App\Models\User;
use App\Models\MembershipTier;
use App\Models\TierProgressionLog;
use Illuminate\Support\Facades\DB;

class TierProgressionService
{
    /**
     * Check and upgrade user tier based on referral count
     */
    public function checkAndUpgradeTier(User $user): bool
    {
        if (!$user->canUpgradeTier()) {
            return false;
        }

        $currentTier = $user->currentTier;
        $newTier = $this->determineEligibleTier($user->direct_referrals_count);

        if (!$newTier || $newTier->id === $currentTier->id) {
            return false;
        }

        DB::transaction(function () use ($user, $currentTier, $newTier) {
            // Update user tier
            $user->update(['current_tier_id' => $newTier->id]);

            // If upgrading to Emerald, set membership to never expire
            if ($newTier->isEmerald()) {
                $user->update(['membership_expires_at' => null]);
            }

            // Log the progression
            TierProgressionLog::create([
                'user_id' => $user->id,
                'from_tier_id' => $currentTier->id,
                'to_tier_id' => $newTier->id,
                'referral_count_at_upgrade' => $user->direct_referrals_count,
                'notes' => "Upgraded from {$currentTier->name} to {$newTier->name}",
            ]);

            // Create notification
            app(NotificationService::class)->sendTierUpgradeNotification($user, $newTier);
        });

        return true;
    }

    /**
     * Determine which tier user is eligible for based on referral count
     */
    public function determineEligibleTier(int $referralCount): ?MembershipTier
    {
        return MembershipTier::where('min_referrals', '<=', $referralCount)
                             ->orderBy('min_referrals', 'desc')
                             ->first();
    }

    /**
     * Check all users and upgrade eligible ones (for scheduled job)
     */
    public function checkAllUsersForUpgrade(): int
    {
        $upgradedCount = 0;

        User::active()
            ->chunk(100, function ($users) use (&$upgradedCount) {
                foreach ($users as $user) {
                    if ($this->checkAndUpgradeTier($user)) {
                        $upgradedCount++;
                    }
                }
            });

        return $upgradedCount;
    }

    /**
     * Get tier progression history for a user
     */
    public function getTierHistory(User $user): array
    {
        return TierProgressionLog::where('user_id', $user->id)
            ->with(['fromTier', 'toTier'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'from' => $log->fromTier?->name ?? 'New User',
                    'to' => $log->toTier->name,
                    'referral_count' => $log->referral_count_at_upgrade,
                    'date' => $log->created_at->format('M d, Y'),
                ];
            })
            ->toArray();
    }
}
