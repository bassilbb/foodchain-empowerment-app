<?php

// app/Http/Controllers/User/DashboardController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FoodAssistancePlan;
use App\Models\FoodSupportPlan;
use App\Models\Incident;
use App\Services\FoodLoanEligibilityService;
use App\Services\MembershipGradeService;
use App\Services\ReferralService;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get referral stats
        $referralService = app(ReferralService::class);
        $referralStats = $referralService->getReferralStats($user);

        // Add open incidents count
        $referralStats['open_incidents'] = Incident::where('reported_by', $user->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        // Get membership grade benefits
        $membershipService = app(MembershipGradeService::class);
        $membershipSummary = $membershipService->getUserMembershipSummary($user);
        $benefitEligibility = $membershipService->checkBenefitEligibility($user);

        // Get loan eligibility
        $eligibilityService = app(FoodLoanEligibilityService::class);
        $loanEligibility = $eligibilityService->setUser($user)->checkEligibility();

        // Recent referrals
        $recentReferrals = $user->referrals()->with('currentTier')->latest()->limit(5)->get();

        // Recent payments
        $recentPayments = $user->payments()->latest()->limit(5)->get();

        // Recent distributions
        $recentDistributions = $user->distributions()->with('package')->latest()->limit(5)->get();

        // Recent vouchers
        $recentVouchers = $user->vouchers()
            ->with('distribution.tier', 'distribution.package')
            ->latest()
            ->limit(5)
            ->get();

        $unacknowledgedVouchersCount = $user->vouchers()
            ->where('status', 'issued')
            ->whereNull('acknowledged_at')
            ->count();

        // Food Assistance Plan stats
        $activeAssistancePlans = FoodAssistancePlan::where('user_id', $user->id)
            ->whereIn('status', ['processing', 'active'])
            ->count();
        $pendingAssistancePayment = FoodAssistancePlan::where('user_id', $user->id)
            ->where('status', 'pending_processing_fee')
            ->count();

        // Food Support Plan stats
        $activeSupportPlans = FoodSupportPlan::where('user_id', $user->id)
            ->where('status', 'active')
            ->count();
        $maturedSupportPlans = FoodSupportPlan::where('user_id', $user->id)
            ->where('status', 'matured')
            ->count();

        // Unread notifications
        $unreadNotifications = $user->notifications()->unread()->latest()->limit(5)->get();

        // Linked Account stats
        $linkedAccounts = $user->linkedAccounts()->with('savingsPlanOption.plan')->get();
        $totalThriftAccounts = $user->linkedAccounts()->count();
        $activeAccountsCount = $user->linkedAccounts()->where('status', 'active')->count();
        $totalAccountBalance = $user->linkedAccounts()->sum('available_balance');
        $totalContributed = $user->linkedAccounts()->sum('total_contributions');
        $walletBalance = $user->wallet_balance;
        $referredAccountsCount = $user->referredAccounts()->count();
        $totalDefaults = $user->linkedAccounts()->sum('defaults');
        $currentThriftWeeks = $user->linkedAccounts()->sum('current_tariff_weeks');
        $weeklyContributionPrice = $user->linkedAccounts()->sum('weekly_contribution_amount');
        $completedClearances = $user->linkedAccounts()->where('status', 'completed')->count();

        // Accounts due for clearance
        $clearanceDueAccounts = $linkedAccounts
            ->where('status', '!=', 'completed')
            ->filter(function ($account) {
                return $account->isMatured();
            });
        $clearanceDueCount = $clearanceDueAccounts->count();

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
            'recentVouchers',
            'unacknowledgedVouchersCount',
            'unreadNotifications',
            'membershipExpiringSoon',
            'membershipSummary',
            'benefitEligibility',
            'loanEligibility',
            'activeAssistancePlans',
            'pendingAssistancePayment',
            'activeSupportPlans',
            'maturedSupportPlans',
            'linkedAccounts',
            'totalThriftAccounts',
            'activeAccountsCount',
            'totalAccountBalance',
            'totalContributed',
            'walletBalance',
            'referredAccountsCount',
            'totalDefaults',
            'currentThriftWeeks',
            'weeklyContributionPrice',
            'completedClearances',
            'clearanceDueCount'
        ));
    }
}
