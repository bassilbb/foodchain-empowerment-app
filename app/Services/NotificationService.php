<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\FoodDistribution;
use App\Models\MembershipTier;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send welcome notification to new user
     */
    public function sendWelcomeNotification(User $user): void
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'welcome',
            'title' => 'Welcome to FoodChain Community!',
            'message' => "Hi {$user->first_name}, welcome to our community! Your referral code is: {$user->referral_code}",
            'action_url' => route('dashboard'),
        ]);

        // TODO: Send email
        // Mail::to($user->email)->send(new WelcomeEmail($user));
    }

    /**
     * Send payment success notification
     */
    public function sendPaymentSuccessNotification(User $user, Payment $payment): void
    {
        $type = $payment->type === 'membership' ? 'Membership' : 'Starter Pack';
        
        Notification::create([
            'user_id' => $user->id,
            'type' => 'payment_success',
            'title' => 'Payment Successful',
            'message' => "Your {$type} payment of ₦" . number_format($payment->amount, 2) . " was successful!",
            'action_url' => route('transactions'),
        ]);
    }

    /**
     * Send tier upgrade notification
     */
    public function sendTierUpgradeNotification(User $user, MembershipTier $newTier): void
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'tier_upgrade',
            'title' => "Congratulations! You've Reached {$newTier->name} Tier",
            'message' => "You've been upgraded to {$newTier->name} tier with {$user->direct_referrals_count} referrals. You now qualify for ₦" . number_format($newTier->food_package_value, 2) . " food package!",
            'action_url' => route('dashboard'),
        ]);
    }

    /**
     * Send distribution approved notification
     */
    public function sendDistributionApprovedNotification(FoodDistribution $distribution): void
    {
        Notification::create([
            'user_id' => $distribution->user_id,
            'type' => 'distribution_approved',
            'title' => 'Food Distribution Approved',
            'message' => "Your food distribution #{$distribution->distribution_number} has been approved and will be processed soon.",
            'action_url' => route('distributions.show', $distribution->id),
        ]);
    }

    /**
     * Send distribution dispatched notification
     */
    public function sendDistributionDispatchedNotification(FoodDistribution $distribution): void
    {
        $message = "Your food package has been dispatched!";
        if ($distribution->tracking_number) {
            $message .= " Tracking number: {$distribution->tracking_number}";
        }

        Notification::create([
            'user_id' => $distribution->user_id,
            'type' => 'distribution_dispatched',
            'title' => 'Food Package Dispatched',
            'message' => $message,
            'action_url' => route('distributions.show', $distribution->id),
        ]);
    }

    /**
     * Send membership expiry reminder
     */
    public function sendMembershipExpiryReminder(User $user, int $daysRemaining): void
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'membership_expiry',
            'title' => 'Membership Expiring Soon',
            'message' => "Your membership will expire in {$daysRemaining} days. Renew now to continue enjoying benefits.",
            'action_url' => route('membership.renew'),
        ]);
    }

    /**
     * Send referral joined notification
     */
    public function sendReferralJoinedNotification(User $referrer, User $referred): void
    {
        Notification::create([
            'user_id' => $referrer->id,
            'type' => 'referral_joined',
            'title' => 'New Referral Joined',
            'message' => "{$referred->full_name} just joined using your referral code! You now have {$referrer->direct_referrals_count} direct referrals.",
            'action_url' => route('referrals'),
        ]);
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(User $user): void
    {
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }
}