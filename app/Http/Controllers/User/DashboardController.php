<?php
// app/Http/Controllers/User/DashboardController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\ReferralService;
use App\Services\MembershipGradeService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get referral stats
        $referralService = app(ReferralService::class);
        $referralStats = $referralService->getReferralStats($user);

        // Get membership grade benefits
        $membershipService = app(MembershipGradeService::class);
        $membershipSummary = $membershipService->getUserMembershipSummary($user);
        $benefitEligibility = $membershipService->checkBenefitEligibility($user);

        // Recent referrals
        $recentReferrals = $user->referrals()->latest()->limit(5)->get();

        // Recent payments
        $recentPayments = $user->payments()->latest()->limit(5)->get();

        // Recent distributions
        $recentDistributions = $user->distributions()->latest()->limit(5)->get();

        // Unread notifications
        $unreadNotifications = $user->notifications()->unread()->latest()->limit(5)->get();

        // Check if membership is expiring soon
        $membershipExpiringSoon = false;
        if ($user->membership_expires_at && $user->membership_expires_at->diffInDays(now()) <= 30) {
            $membershipExpiringSoon = true;
        }

        return view('user.dashboard', compact(
            'user',
            'referralStats',
            'recentReferrals',
            'recentPayments',
            'recentDistributions',
            'unreadNotifications',
            'membershipExpiringSoon',
            'membershipSummary',
            'benefitEligibility'
        ));
    }
}
