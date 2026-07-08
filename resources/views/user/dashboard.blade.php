{{-- resources/views/user/dashboard.blade.php --}}
@extends('layouts.user')

@section('title', 'Dashboard')
@section('header', 'Dashboard')
@section('subheader', 'Welcome back!')

@section('content')

<!-- Welcome Section -->
<div class="page-card" style="padding: 1.5rem; margin-bottom: 1.5rem; background: linear-gradient(135deg, #f0fdf4 0%, #ecfeff 100%);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div class="user-avatar" style="width: 56px; height: 56px; font-size: 1.25rem;">
                {{ substr(auth()->user()?->first_name ?? 'M', 0, 1) }}
            </div>
            <div>
                <h1 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0;">
                    Welcome back, {{ auth()->user()?->first_name ?? 'Member' }}! 👋
                </h1>
                <p style="color: #64748b; margin: 0.25rem 0 0 0;">Here's your FoodChain membership overview</p>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            @if(module_enabled('referrals'))
            <a href="{{ route('referrals.index') }}" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                My Referrals
            </a>
            @endif
            @if(module_enabled('incidents'))
            <a href="{{ route('user.incidents.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Report Incident
            </a>
            @endif
        </div>
    </div>
</div>

@if(isset($clearanceDueCount) && $clearanceDueCount > 0)
<div class="page-card" style="padding: 1rem 1.5rem; margin-bottom: 1.5rem; background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%); border: 1px solid #fde68a;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #fbbf24; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg style="width: 20px; height: 20px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <p style="font-weight: 600; color: #92400e; margin: 0; font-size: 0.9rem;">
                    <strong>{{ $clearanceDueCount }}</strong> {{ Str::plural('account', $clearanceDueCount) }} {{ $clearanceDueCount === 1 ? 'is' : 'are' }} due for clearance
                </p>
                <p style="font-size: 0.8rem; color: #b45309; margin: 0.15rem 0 0;">Pay the ₦3,000 clearance fee to complete the process.</p>
            </div>
        </div>
        <a href="{{ route('user.accounts.clearances') }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem; white-space: nowrap;">
            View Clearances
        </a>
    </div>
</div>
@endif

<!-- Stats Grid -->
<div class="stats-grid">
    <!-- My Tier -->
    <div class="stat-card orange">
        <div class="stat-icon orange">
            <svg style="width: 24px; height: 24px; color: #ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
        </div>
        <p class="stat-label">My Tier</p>
        <p class="stat-value">{{ $user->currentTier?->name ?? 'Bronze' }}</p>
        <span class="badge" style="background: linear-gradient(135deg, #f97316, #ea580c); color: white; margin-top: 0.5rem;">Member</span>
    </div>

    <!-- Wallet Balance -->
    <div class="stat-card" style="background: linear-gradient(145deg, #ffffff 0%, #f0fdf4 100%);">
        <div class="stat-icon" style="background: #d1fae5;">
            <svg style="width: 24px; height: 24px; color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
        </div>
        <p class="stat-label">Wallet Balance</p>
        <p class="stat-value">₦{{ number_format($walletBalance ?? 0, 0) }}</p>
        <a href="{{ route('user.accounts.wallet') }}" class="badge badge-success" style="margin-top: 0.5rem; text-decoration: none;">Top Up →</a>
    </div>

    <!-- Total Thrift Accounts -->
    <div class="stat-card" style="background: linear-gradient(145deg, #ffffff 0%, #f0f9ff 100%);">
        <div class="stat-icon" style="background: #dbeafe;">
            <svg style="width: 24px; height: 24px; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
        </div>
        <p class="stat-label">Thrift Accounts</p>
        <p class="stat-value">{{ number_format($totalThriftAccounts ?? 0) }}</p>
        <span class="badge badge-info" style="margin-top: 0.5rem;">{{ number_format($activeAccountsCount) }} active</span>
    </div>

    <!-- Available Balance (thrift) -->
    <div class="stat-card" style="background: linear-gradient(145deg, #ffffff 0%, #ecfdf5 100%);">
        <div class="stat-icon" style="background: #a7f3d0;">
            <svg style="width: 24px; height: 24px; color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <p class="stat-label">Available Balance</p>
        <p class="stat-value">₦{{ number_format($totalAccountBalance ?? 0, 0) }}</p>
        <span class="badge badge-success" style="margin-top: 0.5rem;">In thrift accounts</span>
    </div>

    <!-- Total Contributions -->
    <div class="stat-card" style="background: linear-gradient(145deg, #ffffff 0%, #fefce8 100%);">
        <div class="stat-icon" style="background: #fef3c7;">
            <svg style="width: 24px; height: 24px; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Total Contributed</p>
        <p class="stat-value">₦{{ number_format($totalContributed ?? 0, 0) }}</p>
        <span class="badge badge-warning" style="margin-top: 0.5rem;">All time</span>
    </div>

    <!-- Current Thrift Weeks -->
    <div class="stat-card" style="background: linear-gradient(145deg, #ffffff 0%, #f5f3ff 100%);">
        <div class="stat-icon" style="background: #f3e8ff;">
            <svg style="width: 24px; height: 24px; color: #9333ea;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        <p class="stat-label">Current Thrift Weeks</p>
        <p class="stat-value">{{ number_format($currentThriftWeeks ?? 0) }}</p>
        <span class="badge badge-purple" style="margin-top: 0.5rem;">Total weeks saved</span>
    </div>

    <!-- Weekly Contribution -->
    <div class="stat-card" style="background: linear-gradient(145deg, #ffffff 0%, #fff7ed 100%);">
        <div class="stat-icon" style="background: #ffedd5;">
            <svg style="width: 24px; height: 24px; color: #ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Weekly Contribution</p>
        <p class="stat-value">₦{{ number_format($weeklyContributionPrice ?? 0, 0) }}</p>
        <span class="badge" style="margin-top: 0.5rem; background: #ffedd5; color: #c2410c;">Across all accounts</span>
    </div>

    @if(module_enabled('referrals'))
    <!-- Direct Referrals -->
    <div class="stat-card cyan">
        <div class="stat-icon cyan">
            <svg style="width: 24px; height: 24px; color: #0891b2;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <p class="stat-label">Direct Referrals</p>
        <p class="stat-value">{{ number_format($referralStats['direct_referrals'] ?? 0) }}</p>
        <span class="badge badge-info" style="margin-top: 0.5rem;">{{ $referralStats['referrals_needed'] ?? 0 }} to next tier</span>
    </div>

    <!-- Referred Accounts -->
    <div class="stat-card purple">
        <div class="stat-icon purple">
            <svg style="width: 24px; height: 24px; color: #9333ea;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
        </div>
        <p class="stat-label">Referred Accounts</p>
        <p class="stat-value">{{ number_format($referredAccountsCount ?? 0) }}</p>
        <span class="badge" style="background: #f1f5f9; color: #475569; margin-top: 0.5rem;">Linked via referral</span>
    </div>
    @endif

    <!-- Total Defaults -->
    <div class="stat-card" style="background: linear-gradient(145deg, #ffffff 0%, #fef2f2 100%);">
        <div class="stat-icon" style="background: #fee2e2;">
            <svg style="width: 24px; height: 24px; color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <p class="stat-label">Total Defaults</p>
        <p class="stat-value">{{ number_format($totalDefaults ?? 0) }}</p>
        <span class="badge badge-error" style="margin-top: 0.5rem;">Missed contributions</span>
    </div>

    <!-- Completed Clearances -->
    <div class="stat-card" style="background: linear-gradient(145deg, #ffffff 0%, #f0fdf4 100%);">
        <div class="stat-icon" style="background: #bbf7d0;">
            <svg style="width: 24px; height: 24px; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Completed Clearances</p>
        <p class="stat-value">{{ number_format($completedClearances ?? 0) }}</p>
        <span class="badge badge-success" style="margin-top: 0.5rem;">Accounts cleared</span>
    </div>

    @if(module_enabled('incidents'))
    <!-- My Incidents -->
    <div class="stat-card rose">
        <div class="stat-icon rose">
            <svg style="width: 24px; height: 24px; color: #e11d48;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <p class="stat-label">My Incidents</p>
        <p class="stat-value">{{ number_format($referralStats['open_incidents'] ?? 0) }}</p>
        <a href="{{ route('user.incidents.index') }}" class="badge badge-error" style="margin-top: 0.5rem; text-decoration: none;">View All →</a>
    </div>
    @endif
</div>

<!-- New Plans Stats Grid -->
<div class="stats-grid">
    @if(module_enabled('food_assistance'))
    <!-- Active Assistance Plans -->
    <div class="stat-card" style="background: linear-gradient(145deg, #ffffff 0%, #f0fdf4 100%);">
        <div class="stat-icon" style="background: #d1fae5;">
            <svg style="width: 24px; height: 24px; color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <p class="stat-label">Active Assistance Plans</p>
        <p class="stat-value">{{ number_format($activeAssistancePlans) }}</p>
        <a href="{{ route('user.food-assistance.index') }}" class="badge badge-success" style="margin-top: 0.5rem; text-decoration: none;">View Plans →</a>
    </div>

    <!-- Pending Assistance Payment -->
    <div class="stat-card" style="background: linear-gradient(145deg, #ffffff 0%, #fefce8 100%);">
        <div class="stat-icon" style="background: #fef3c7;">
            <svg style="width: 24px; height: 24px; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Pending Fee Payment</p>
        <p class="stat-value">{{ number_format($pendingAssistancePayment) }}</p>
        <a href="{{ route('user.food-assistance.index') }}" class="badge badge-warning" style="margin-top: 0.5rem; text-decoration: none;">Complete →</a>
    </div>
    @endif

    @if(module_enabled('food_support'))
    <!-- Active Support Plans -->
    <div class="stat-card" style="background: linear-gradient(145deg, #ffffff 0%, #f0f9ff 100%);">
        <div class="stat-icon" style="background: #dbeafe;">
            <svg style="width: 24px; height: 24px; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Active Support Plans</p>
        <p class="stat-value">{{ number_format($activeSupportPlans) }}</p>
        <a href="{{ route('user.food-support.index') }}" class="badge badge-info" style="margin-top: 0.5rem; text-decoration: none;">View Plans →</a>
    </div>

    <!-- Matured Support Plans -->
    <div class="stat-card" style="background: linear-gradient(145deg, #ffffff 0%, #f5f3ff 100%);">
        <div class="stat-icon" style="background: #f3e8ff;">
            <svg style="width: 24px; height: 24px; color: #9333ea;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Matured Support Plans</p>
        <p class="stat-value">{{ number_format($maturedSupportPlans) }}</p>
        <a href="{{ route('user.food-support.index') }}" class="badge badge-purple" style="margin-top: 0.5rem; text-decoration: none;">Claim →</a>
    </div>
    @endif
</div>

<!-- Accounts Summary -->
<div class="page-card" style="padding: 1.5rem; margin-bottom: 1.5rem; background: linear-gradient(135deg, #f0fdf4 0%, #ecfeff 100%);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                <svg style="width: 28px; height: 28px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <p style="font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Linked Accounts</p>
                <p style="font-size: 1.5rem; font-weight: 700; color: #065f46; margin: 0;">{{ $activeAccountsCount }} Active Account{{ $activeAccountsCount !== 1 ? 's' : '' }}</p>
            </div>
        </div>
        <div style="display: flex; gap: 1.5rem; align-items: center;">
            <div style="text-align: right;">
                <p style="font-size: 0.75rem; color: #64748b; margin: 0;">Wallet Balance</p>
                <p style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">₦{{ number_format($walletBalance ?? 0, 2) }}</p>
            </div>
            <div style="text-align: right;">
                <p style="font-size: 0.75rem; color: #64748b; margin: 0;">Total Contributed</p>
                <p style="font-size: 1.25rem; font-weight: 700; color: #065f46; margin: 0;">₦{{ number_format($totalContributed ?? 0, 2) }}</p>
            </div>
            <div style="text-align: right;">
                <p style="font-size: 0.75rem; color: #64748b; margin: 0;">Available Balance</p>
                <p style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">₦{{ number_format($totalAccountBalance ?? 0, 2) }}</p>
            </div>
            <a href="{{ route('user.accounts.index') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Manage Accounts
            </a>
        </div>
    </div>
</div>

@if(module_enabled('food_loans'))
<!-- Loan Eligibility Status -->
<div class="page-card" style="padding: 1.5rem; margin-bottom: 1.5rem; @if($loanEligibility['eligible']) background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border: 2px solid #10b981; @else background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 2px solid #f59e0b; @endif">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: @if($loanEligibility['eligible']) linear-gradient(135deg, #10b981, #059669); @else linear-gradient(135deg, #f59e0b, #d97706); @endif display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px @if($loanEligibility['eligible']) rgba(16, 185, 129, 0.3); @else rgba(245, 158, 11, 0.3); @endif">
                <svg style="width: 28px; height: 28px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <p style="font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Food Loan Eligibility</p>
                <p style="font-size: 1.25rem; font-weight: 700; color: @if($loanEligibility['eligible']) #065f46; @else #92400e; @endif margin: 0;">
                    @if($loanEligibility['eligible'])
                    You're Eligible! ✓
                    @else
                    Not Yet Eligible
                    @endif
                </p>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            @if($loanEligibility['eligible'])
            <a href="{{ route('unions.index') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #10b981, #059669);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Apply for Loan
            </a>
            @endif
            <button onclick="toggleEligibilityDetails()" class="btn btn-secondary" style="@if(!$loanEligibility['eligible']) border-color: #f59e0b; color: #92400e; @endif">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                View Details
            </button>
        </div>
    </div>
    
    <div id="eligibilityDetails" style="display: none; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid @if($loanEligibility['eligible']) rgba(16, 185, 129, 0.2); @else rgba(245, 158, 11, 0.2); @endif">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            @foreach($loanEligibility['passed'] as $passed)
            <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(16, 185, 129, 0.1); border-radius: 8px;">
                <svg style="width: 16px; height: 16px; color: #10b981; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span style="font-size: 0.875rem; color: #065f46;">{{ $passed['message'] }}</span>
            </div>
            @endforeach
            @foreach($loanEligibility['failed'] as $failed)
            <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border-radius: 8px;">
                <svg style="width: 16px; height: 16px; color: #f59e0b; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <span style="font-size: 0.875rem; color: #92400e;">{{ $failed['message'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@if(module_enabled('referrals'))
<!-- Referral Code -->
<div class="page-card" style="padding: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                <svg style="width: 28px; height: 28px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            </div>
            <div>
                <p style="font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Your Referral Code</p>
                <p style="font-size: 1.75rem; font-weight: 700; color: #1e293b; font-family: monospace;">{{ $user->referral_code ?? 'N/A' }}</p>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button onclick="copyReferralLink()" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                Copy Link
            </button>
            <a href="{{ route('referrals.index') }}" class="btn btn-secondary">View All Referrals</a>
        </div>
    </div>
</div>
@endif

<div class="activity-grid" style="display: grid; gap: 1.5rem; margin-bottom: 1.5rem;">
    @if(module_enabled('referrals'))
    <!-- Recent Referrals -->
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Recent Referrals</h3>
                <p class="page-card-subtitle">Your latest referrals</p>
            </div>
            <a href="{{ route('referrals.index') }}" style="color: #10b981; font-weight: 600; font-size: 0.875rem; text-decoration: none;">View all →</a>
        </div>
        <div style="padding: 0.5rem 0;">
            @forelse($recentReferrals as $referral)
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #34d399, #38bdf8); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">{{ substr($referral->first_name ?? 'U', 0, 1) }}{{ substr($referral->last_name ?? '', 0, 1) }}</div>
                <div style="flex: 1;">
                    <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $referral->full_name ?? 'Unknown' }}</p>
                    <p style="font-size: 0.875rem; color: #64748b; margin: 0;">{{ $referral->created_at?->diffForHumans() ?? 'Recently' }}</p>
                    <span style="display: inline-block; margin-top: 0.25rem; padding: 0.125rem 0.5rem; background: linear-gradient(135deg, #f97316, #ea580c); color: white; border-radius: 4px; font-size: 0.625rem; font-weight: 600;">{{ $referral->currentTier?->name ?? 'Bronze' }}</span>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <p class="empty-title">No referrals yet.</p>
                <p class="empty-text">Share your referral code to get started!</p>
            </div>
            @endforelse
        </div>
    </div>
    @endif

    <!-- Recent Payments -->
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Recent Payments</h3>
                <p class="page-card-subtitle">Your latest transactions</p>
            </div>
            <a href="{{ route('payments.index') }}" style="color: #10b981; font-weight: 600; font-size: 0.875rem; text-decoration: none;">View all →</a>
        </div>
        <div style="padding: 0.5rem 0;">
            @forelse($recentPayments as $payment)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
                        @if(($payment->status ?? '') === 'successful') background: #dcfce7; @endif
                        @if(($payment->status ?? '') === 'pending') background: #fef3c7; @endif
                        @if(($payment->status ?? '') === 'failed') background: #fee2e2; @endif">
                        <svg style="width: 24px; height: 24px;
                            @if(($payment->status ?? '') === 'successful') color: #16a34a; @endif
                            @if(($payment->status ?? '') === 'pending') color: #d97706; @endif
                            @if(($payment->status ?? '') === 'failed') color: #dc2626; @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ ucfirst(str_replace('_', ' ', $payment->type ?? 'payment')) }}</p>
                        <p style="font-size: 0.875rem; color: #64748b; margin: 0;">{{ $payment->created_at?->format('M d, Y') ?? 'N/A' }}</p>
                        <span class="badge 
                            @if(($payment->status ?? '') === 'successful') badge-success @endif
                            @if(($payment->status ?? '') === 'pending') badge-warning @endif
                            @if(($payment->status ?? '') === 'failed') badge-error @endif" style="margin-top: 0.25rem;">
                            {{ ucfirst($payment->status ?? 'pending') }}
                        </span>
                    </div>
                </div>
                <p style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0;">{{ format_currency($payment->amount ?? 0) }}</p>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
                <p class="empty-title">No payments yet.</p>
                <a href="{{ route('payments.index') }}" style="color: #10b981; font-size: 0.875rem;">Make a payment →</a>
            </div>
            @endforelse
        </div>
    </div>
</div>

@if(module_enabled('distributions'))
<!-- Distributions Table -->
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Recent Distributions</h3>
            <p class="page-card-subtitle">Your latest package deliveries</p>
        </div>
        <a href="{{ route('distributions.index') }}" style="color: #10b981; font-weight: 600; font-size: 0.875rem; text-decoration: none;">View all →</a>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Package</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th style="text-align: right;">Value</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentDistributions as $distribution)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; border-radius: 8px; background: #f3e8ff; display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 20px; height: 20px; color: #9333ea;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <div>
                                <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $distribution->distribution_number ?? 'N/A' }}</p>
                                <p style="font-size: 0.875rem; color: #64748b; margin: 0;">{{ $distribution->package?->name ?? 'Standard Package' }}</p>
                            </div>
                        </div>
                    </td>
                    <td style="color: #475569;">{{ $distribution->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                    <td>
                        <span class="badge 
                            @if(($distribution->status ?? '') === 'delivered') badge-success @endif
                            @if(($distribution->status ?? '') === 'pending') badge-warning @endif
                            @if(($distribution->status ?? '') === 'processing') badge-info @endif
                            @if(($distribution->status ?? '') === 'cancelled') badge-error @endif">
                            {{ ucfirst($distribution->status ?? 'pending') }}
                        </span>
                    </td>
                    <td style="text-align: right; font-weight: 600; color: #1e293b;">{{ format_currency($distribution->package_value ?? 0) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 3rem;">
                        <div class="empty-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <p class="empty-title">No distributions yet.</p>
                        <p class="empty-text">Distributions will appear here once you receive your packages.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- My Vouchers -->
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">My Vouchers</h3>
            <p class="page-card-subtitle">
                @if($unacknowledgedVouchersCount > 0)
                <span style="color: #d97706; font-weight: 600;">{{ $unacknowledgedVouchersCount }} unacknowledged</span>
                @else
                All vouchers acknowledged
                @endif
            </p>
        </div>
        <a href="{{ route('vouchers.index') }}" style="color: #10b981; font-weight: 600; font-size: 0.875rem; text-decoration: none;">View all →</a>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Voucher</th>
                    <th>Status</th>
                    <th>Acknowledged</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentVouchers as $voucher)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; border-radius: 8px; background: #fef3c7; display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 20px; height: 20px; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                            </div>
                            <div>
                                <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $voucher->voucher_number }}</p>
                                <p style="font-size: 0.875rem; color: #64748b; margin: 0;">{{ $voucher->distribution?->package?->name ?? $voucher->distribution?->tier?->name ?? 'Package' }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge
                            @if($voucher->status === 'issued') badge-info @endif
                            @if($voucher->status === 'collected') badge-success @endif
                            @if($voucher->status === 'expired') badge-error @endif
                            @if($voucher->status === 'pending') badge-warning @endif">
                            {{ ucfirst($voucher->status) }}
                        </span>
                    </td>
                    <td>
                        @if($voucher->isAcknowledged())
                        <span class="badge badge-success">Yes</span>
                        @elseif($voucher->isIssued())
                        <span class="badge badge-warning">No</span>
                        @else
                        <span style="color: #94a3b8;">N/A</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('vouchers.show', $voucher) }}" style="color: #10b981; font-weight: 600; font-size: 0.875rem; text-decoration: none; margin-right: 0.75rem;">View</a>
                        @if($voucher->isIssued() && !$voucher->isAcknowledged())
                        <form method="POST" action="{{ route('vouchers.acknowledge', $voucher) }}" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: #10b981; color: white; border: none; padding: 6px 14px; border-radius: 8px; font-weight: 600; font-size: 0.75rem; cursor: pointer;">
                                Acknowledge
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 3rem;">
                        <div class="empty-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                        </div>
                        <p class="empty-title">No vouchers yet.</p>
                        <p class="empty-text">Vouchers will appear here once your clearance request is approved.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Quick Actions -->
<div>
    <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">Quick Actions</h3>
    <div class="quick-actions" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;"></div>
</div>

<style>
.quick-actions {
    grid-template-columns: repeat(2, 1fr);
}
@media (min-width: 768px) {
    .quick-actions { grid-template-columns: repeat(3, 1fr); }
}
@media (min-width: 1024px) {
    .quick-actions { grid-template-columns: repeat(6, 1fr); }
}
</style>

<style>
.quick-actions a {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08), 0 0 0 1px rgba(255,255,255,0.5) inset;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.quick-actions a:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 15px 35px rgba(16, 185, 129, 0.2), 0 0 20px rgba(16, 185, 129, 0.1);
}
</style>

<div class="quick-actions" style="display: grid; gap: 1rem;">
    <a href="{{ route('profile.index') }}" style="display: flex; flex-direction: column; align-items: center; padding: 1.25rem; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-decoration: none; transition: all 0.2s;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #dbeafe; display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
            <svg style="width: 24px; height: 24px; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
        <span style="font-size: 0.875rem; font-weight: 500; color: #334155;">Profile</span>
    </a>
    <a href="{{ route('payments.index') }}" style="display: flex; flex-direction: column; align-items: center; padding: 1.25rem; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-decoration: none; transition: all 0.2s;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #dcfce7; display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
            <svg style="width: 24px; height: 24px; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
        </div>
        <span style="font-size: 0.875rem; font-weight: 500; color: #334155;">Payments</span>
    </a>
    @if(module_enabled('distributions'))
    <a href="{{ route('distributions.index') }}" style="display: flex; flex-direction: column; align-items: center; padding: 1.25rem; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-decoration: none; transition: all 0.2s;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #f3e8ff; display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
            <svg style="width: 24px; height: 24px; color: #9333ea;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
        </div>
        <span style="font-size: 0.875rem; font-weight: 500; color: #334155;">Distributions</span>
    </a>
    @endif
    @if(module_enabled('quarterly_benefits'))
    <a href="{{ route('quarterly-benefits.index') }}" style="display: flex; flex-direction: column; align-items: center; padding: 1.25rem; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-decoration: none; transition: all 0.2s;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef3c7; display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
            <svg style="width: 24px; height: 24px; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a4 4 0 00-4-4H6.5a2.5 2.5 0 110 0 2.5 2.5 0 010 5H12m0 0V6a4 4 0 00-4-4h1.5a2.5 2.5 0 110 5H12m0 0V6a4 4 0 014-4h1.5a2.5 2.5 0 110 5H12"></path></svg>
        </div>
        <span style="font-size: 0.875rem; font-weight: 500; color: #334155;">Benefits</span>
    </a>
    @endif
    <a href="{{ route('support.index') }}" style="display: flex; flex-direction: column; align-items: center; padding: 1.25rem; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-decoration: none; transition: all 0.2s;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #fce7f3; display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
            <svg style="width: 24px; height: 24px; color: #db2777;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <span style="font-size: 0.875rem; font-weight: 500; color: #334155;">Support</span>
    </a>
    @if(module_enabled('incidents'))
    <a href="{{ route('user.incidents.create') }}" style="display: flex; flex-direction: column; align-items: center; padding: 1.25rem; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-decoration: none; transition: all 0.2s;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #fee2e2; display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
            <svg style="width: 24px; height: 24px; color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <span style="font-size: 0.875rem; font-weight: 500; color: #334155;">Report Issue</span>
    </a>
    @endif
    @if(module_enabled('food_assistance'))
    <a href="{{ route('user.food-assistance.index') }}" style="display: flex; flex-direction: column; align-items: center; padding: 1.25rem; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-decoration: none; transition: all 0.2s;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #d1fae5; display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
            <svg style="width: 24px; height: 24px; color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <span style="font-size: 0.875rem; font-weight: 500; color: #334155;">Food Assistance</span>
    </a>
    @endif
    @if(module_enabled('food_support'))
    <a href="{{ route('user.food-support.create') }}" style="display: flex; flex-direction: column; align-items: center; padding: 1.25rem; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-decoration: none; transition: all 0.2s;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #dbeafe; display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
            <svg style="width: 24px; height: 24px; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <span style="font-size: 0.875rem; font-weight: 500; color: #334155;">Start Saving</span>
    </a>
    @endif
    <a href="{{ route('user.accounts.wallet') }}" style="display: flex; flex-direction: column; align-items: center; padding: 1.25rem; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-decoration: none; transition: all 0.2s;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef3c7; display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
            <svg style="width: 24px; height: 24px; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
        </div>
        <span style="font-size: 0.875rem; font-weight: 500; color: #334155;">My Wallet</span>
    </a>
</div>

@push('scripts')
<script>
function copyReferralLink() {
    const link = "{{ url('/register?ref=' . $user->referral_code) }}";
    if (link) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(link).then(() => {
                showCopySuccess();
            }).catch(err => {
                fallbackCopy(link);
            });
        } else {
            fallbackCopy(link);
        }
    }
}

function fallbackCopy(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.left = "-9999px";
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand("copy");
        showCopySuccess();
    } catch (err) {
        alert('Failed to copy. Please copy manually: ' + text);
    }
    document.body.removeChild(textArea);
}

function showCopySuccess() {
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Copied!';
    btn.style.background = 'linear-gradient(135deg, #059669, #047857)';
    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.style.background = '';
    }, 2000);
}

function toggleEligibilityDetails() {
    const details = document.getElementById('eligibilityDetails');
    if (details) {
        details.style.display = details.style.display === 'none' ? 'block' : 'none';
    }
}
</script>
@endpush
@endsection