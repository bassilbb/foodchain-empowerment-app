{{-- resources/views/user/dashboard.blade.php --}}
@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">
        Welcome back, {{ $user->first_name }}! 👋
    </h1>
    <p class="mt-1 text-sm text-gray-500">
        Here's what's happening with your account today.
    </p>
</div>

<!-- Membership Expiry Warning -->
@if($membershipExpiringSoon && !$user->isEmerald())
<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-yellow-700">
                Your membership expires on <strong>{{ $user->membership_expires_at->format('M d, Y') }}</strong> ({{ $user->membership_expires_at->diffForHumans() }}).
                <a href="{{ route('membership.renew') }}" class="font-medium underline hover:text-yellow-600">
                    Renew now →
                </a>
            </p>
        </div>
    </div>
</div>
@endif

<!-- Stats Grid -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- Current Tier -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md {{ tier_badge_color($user->currentTier->slug) }} text-white text-2xl">
                        @if($user->currentTier->slug === 'bronze') 🥉
                        @elseif($user->currentTier->slug === 'silver') 🥈
                        @elseif($user->currentTier->slug === 'gold') 🥇
                        @else 💎
                        @endif
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Current Tier
                        </dt>
                        <dd class="text-lg font-bold text-gray-900">
                            {{ $user->currentTier->name }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Direct Referrals -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Direct Referrals
                        </dt>
                        <dd class="text-lg font-bold text-gray-900">
                            {{ $referralStats['direct_referrals'] }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="{{ route('referrals.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    View all →
                </a>
            </div>
        </div>
    </div>

    <!-- Referrals Needed -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Next Tier Needs
                        </dt>
                        <dd class="text-lg font-bold text-gray-900">
                            @if($referralStats['referrals_needed'] > 0)
                                {{ $referralStats['referrals_needed'] }} more
                            @else
                                Max Tier! 🎉
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        @if($referralStats['next_tier'])
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm text-gray-600">
                Next: <span class="font-medium">{{ $referralStats['next_tier']->name }}</span>
            </div>
        </div>
        @endif
    </div>

    <!-- Referral Code -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-green-100 truncate">
                            Your Referral Code
                        </dt>
                        <dd class="text-lg font-mono font-bold text-white">
                            {{ $user->referral_code }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-green-700 bg-opacity-50 px-5 py-3">
            <button onclick="copyReferralLink()" class="text-sm font-medium text-white hover:text-green-100">
                📋 Copy Link
            </button>
        </div>
    </div>
</div>

<!-- Membership Grade Benefits Section -->
@if(isset($membershipSummary) && isset($benefitEligibility))
<div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-900">
            🎯 Your {{ $user->currentTier->name }} Membership Benefits
        </h3>
        @if($benefitEligibility['eligible'])
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
            ✓ Qualifies for Free Food Pack
        </span>
        @else
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
            Build community to unlock benefits
        </span>
        @endif
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Annual Subscription -->
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Annual Subscription</p>
            <p class="text-xl font-bold text-gray-900 mt-1">
                @if($user->currentTier->annual_fee == 0)
                    <span class="text-green-600">FREE</span>
                @else
                    {{ format_currency($user->currentTier->annual_fee) }}
                @endif
            </p>
        </div>

        <!-- Community Benefit -->
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Community Requirement</p>
            <p class="text-xl font-bold text-gray-900 mt-1">
                {{ $benefitEligibility['min_required'] ?? 0 }} - {{ $benefitEligibility['max_allowed'] ?? 0 }} members
            </p>
            <p class="text-xs text-gray-500 mt-1">Current: {{ $benefitEligibility['community_count'] ?? 0 }}</p>
        </div>

        <!-- Food Pack -->
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Quarterly Food Pack</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ ucfirst($benefitEligibility['food_pack_type'] ?? 'N/A') }}</p>
            <p class="text-xs text-gray-500 mt-1">Value: {{ format_currency($benefitEligibility['food_pack_value'] ?? 0) }}</p>
        </div>

        <!-- Delivery -->
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Delivery</p>
            @if($user->currentTier->free_delivery)
                <p class="text-xl font-bold text-green-600 mt-1">FREE</p>
            @elseif($user->currentTier->delivery_subsidy_percentage > 0)
                <p class="text-xl font-bold text-gray-900 mt-1">{{ $user->currentTier->delivery_subsidy_percentage }}% Off</p>
            @else
                <p class="text-xl font-bold text-gray-900 mt-1">Standard</p>
            @endif
            <p class="text-xs text-gray-500 mt-1">{{ format_currency($user->currentTier->delivery_fee) }} base fee</p>
        </div>
    </div>

    @if(isset($membershipSummary['union']))
    <div class="mt-4 pt-4 border-t border-blue-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-700">🏦 Active Union</p>
                <p class="text-xs text-gray-500">{{ $membershipSummary['union']['name'] }} ({{ $membershipSummary['union']['code'] }})</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-medium text-gray-700">Loan Balance: {{ format_currency($membershipSummary['union']['loan_balance'] ?? 0) }}</p>
                <p class="text-xs text-gray-500">{{ $membershipSummary['union']['member_count'] }} members</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Recent Referrals -->
    <div class="lg:col-span-2 bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Recent Referrals
            </h3>
        </div>
        <ul class="divide-y divide-gray-200">
            @forelse($recentReferrals as $referral)
            <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center min-w-0 flex-1">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-sm font-medium text-blue-600">
                                    {{ substr($referral->first_name, 0, 1) }}{{ substr($referral->last_name, 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div class="min-w-0 flex-1 px-4">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $referral->full_name }}
                            </p>
                            <p class="text-sm text-gray-500">
                                Joined {{ $referral->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="px-2 py-1 text-xs font-semibold rounded {{ tier_badge_color($referral->currentTier->slug) }} text-white">
                            {{ $referral->currentTier->name }}
                        </span>
                    </div>
                </div>
            </li>
            @empty
            <li class="px-4 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No referrals yet</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by sharing your referral link!</p>
                <div class="mt-6">
                    <button onclick="copyReferralLink()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        Share Referral Link
                    </button>
                </div>
            </li>
            @endforelse
        </ul>
        @if($recentReferrals->count() > 0)
        <div class="bg-gray-50 px-4 py-4 sm:px-6">
            <a href="{{ route('referrals.index') }}" class="text-sm font-medium text-green-600 hover:text-green-500">
                View all referrals →
            </a>
        </div>
        @endif
    </div>

    <!-- Notifications & Quick Actions -->
    <div class="space-y-6">
        <!-- Notifications -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Notifications
                </h3>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse($unreadNotifications as $notification)
                <li class="px-4 py-3 hover:bg-gray-50">
                    <a href="{{ $notification->action_url ?? '#' }}" class="block">
                        <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </a>
                </li>
                @empty
                <li class="px-4 py-6 text-center text-sm text-gray-500">
                    No new notifications
                </li>
                @endforelse
            </ul>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <button onclick="copyReferralLink()" class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                    </svg>
                    Share Referral Link
                </button>
                <a href="{{ route('referrals.tree') }}" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    View Referral Tree
                </a>
                <a href="{{ route('support.create') }}" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Contact Support
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Payments -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Recent Payments
            </h3>
        </div>
        <ul class="divide-y divide-gray-200">
            @forelse($recentPayments as $payment)
            <li class="px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            {{ ucfirst(str_replace('_', ' ', $payment->type)) }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $payment->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">{{ format_currency($payment->amount) }}</p>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $payment->status === 'successful' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                </div>
            </li>
            @empty
            <li class="px-4 py-6 text-center text-sm text-gray-500">
                No payments yet
            </li>
            @endforelse
        </ul>
        @if($recentPayments->count() > 0)
        <div class="bg-gray-50 px-4 py-4 sm:px-6">
            <a href="{{ route('payments.index') }}" class="text-sm font-medium text-green-600 hover:text-green-500">
                View all payments →
            </a>
        </div>
        @endif
    </div>

    <!-- Recent Distributions -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Recent Distributions
            </h3>
        </div>
        <ul class="divide-y divide-gray-200">
            @forelse($recentDistributions as $distribution)
            <li class="px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $distribution->distribution_number }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $distribution->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">{{ format_currency($distribution->package_value) }}</p>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst($distribution->status) }}
                        </span>
                    </div>
                </div>
            </li>
            @empty
            <li class="px-4 py-6 text-center text-sm text-gray-500">
                No distributions yet
            </li>
            @endforelse
        </ul>
        @if($recentDistributions->count() > 0)
        <div class="bg-gray-50 px-4 py-4 sm:px-6">
            <a href="{{ route('distributions.index') }}" class="text-sm font-medium text-green-600 hover:text-green-500">
                View all distributions →
            </a>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function copyReferralLink() {
    const link = "{{ $user->referral_link }}";
    navigator.clipboard.writeText(link).then(() => {
        alert('Referral link copied to clipboard!');
    });
}
</script>
@endpush
@endsection
