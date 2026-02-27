{{-- resources/views/user/referrals/index.blade.php --}}
@extends('layouts.user')

@section('title', 'My Referrals')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">My Referrals</h1>
    <p class="mt-1 text-sm text-gray-500">
        Track your referrals and grow your community
    </p>
</div>

<!-- Referral Stats Cards -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-10 w-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Referrals</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $referralStats['direct_referrals'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Active Referrals</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $referralStats['active_referrals'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-10 w-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Needed for Next Tier</dt>
                        <dd class="text-2xl font-bold text-gray-900">
                            @if($referralStats['referrals_needed'] > 0)
                                {{ $referralStats['referrals_needed'] }}
                            @else
                                Max! 🎉
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-green-500 to-green-600 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-green-100 truncate">Referral Value</dt>
                        <dd class="text-2xl font-bold text-white">{{ format_currency($referralStats['total_referral_value']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Share Section -->
<div class="bg-gradient-to-r from-green-600 to-blue-600 rounded-lg shadow-xl p-6 mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="sm:flex sm:space-x-5">
            <div class="flex-shrink-0">
                <div class="mx-auto h-20 w-20 rounded-full bg-white flex items-center justify-center">
                    <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 text-center sm:mt-0 sm:pt-1 sm:text-left">
                <p class="text-xl font-bold text-white">Share Your Referral Link</p>
                <p class="text-sm font-medium text-green-100">Help others join FoodChain and earn rewards!</p>
                <div class="mt-3 flex items-center space-x-2">
                    <input type="text" readonly value="{{ $user->referral_link }}" id="referralLink"
                           class="flex-1 px-4 py-2 bg-white bg-opacity-20 text-white placeholder-green-200 border border-white border-opacity-30 rounded-md focus:outline-none focus:ring-2 focus:ring-white">
                    <button onclick="copyReferralLink()"
                            class="inline-flex items-center px-4 py-2 border border-white text-sm font-medium rounded-md text-white hover:bg-white hover:text-green-600 transition">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Copy
                    </button>
                </div>
            </div>
        </div>
        <div class="mt-5 sm:mt-0 sm:ml-6 sm:flex-shrink-0 sm:flex sm:items-center">
            <div class="flex flex-col space-y-2">
                <a href="https://wa.me/?text={{ urlencode('Join FoodChain Community and get access to free food! Use my referral code: ' . $user->referral_code . ' - ' . $user->referral_link) }}"
                   target="_blank"
                   class="inline-flex items-center justify-center px-4 py-2 border border-white text-sm font-medium rounded-md text-white hover:bg-white hover:text-green-600 transition">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    WhatsApp
                </a>
                <a href="{{ route('referrals.tree') }}"
                   class="inline-flex items-center justify-center px-4 py-2 border border-white text-sm font-medium rounded-md text-white hover:bg-white hover:text-green-600 transition">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                    View Tree
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Referrals List -->
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                All Referrals ({{ $referrals->total() }})
            </h3>
            <div class="flex space-x-2">
                <select class="text-sm border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                    <option>All Status</option>
                    <option>Active</option>
                    <option>Inactive</option>
                </select>
            </div>
        </div>
    </div>

    @if($referrals->count() > 0)
    <ul class="divide-y divide-gray-200">
        @foreach($referrals as $referral)
        <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center min-w-0 flex-1">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-full {{ $referral->is_active ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center">
                            <span class="text-lg font-medium {{ $referral->is_active ? 'text-green-600' : 'text-gray-600' }}">
                                {{ substr($referral->first_name, 0, 1) }}{{ substr($referral->last_name, 0, 1) }}
                            </span>
                        </div>
                    </div>
                    <div class="min-w-0 flex-1 px-4">
                        <div>
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $referral->full_name }}
                            </p>
                            <p class="text-sm text-gray-500 truncate">
                                {{ $referral->email }}
                            </p>
                        </div>
                        <div class="mt-2 flex items-center space-x-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ tier_badge_color($referral->currentTier->slug) }} text-white">
                                {{ $referral->currentTier->name }}
                            </span>
                            <span class="text-xs text-gray-500">
                                <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Joined {{ $referral->created_at->diffForHumans() }}
                            </span>
                            @if($referral->is_active)
                            <span class="inline-flex items-center text-xs text-green-600">
                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Active
                            </span>
                            @else
                            <span class="inline-flex items-center text-xs text-gray-500">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Inactive
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="ml-5 flex-shrink-0 text-right">
                    <div class="text-sm text-gray-900">
                        <span class="font-medium">{{ $referral->direct_referrals_count }}</span>
                        <span class="text-gray-500">referrals</span>
                    </div>
                    @if($referral->membership_expires_at)
                    <div class="mt-1 text-xs text-gray-500">
                        Expires {{ $referral->membership_expires_at->diffForHumans() }}
                    </div>
                    @endif
                </div>
            </div>
        </li>
        @endforeach
    </ul>

    <!-- Pagination -->
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $referrals->links() }}
    </div>
    @else
    <div class="px-4 py-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No referrals yet</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by sharing your referral link!</p>
        <div class="mt-6">
            <button onclick="copyReferralLink()"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                </svg>
                Share Referral Link
            </button>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function copyReferralLink() {
    const input = document.getElementById('referralLink');
    input.select();
    document.execCommand('copy');

    // Show success message
    const originalHTML = event.target.innerHTML;
    event.target.innerHTML = '<svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Copied!';

    setTimeout(() => {
        event.target.innerHTML = originalHTML;
    }, 2000);
}
</script>
@endpush
@endsection
