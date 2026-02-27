{{-- resources/views/user/referrals/tree.blade.php --}}
@extends('layouts.user')
@section('title', 'Referral Tree')
@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">My Referral Tree</h1>
    <p class="mt-1 text-sm text-gray-500">
        Visualize your community growth and track your referrals
    </p>
</div>

<!-- Stats Summary -->
<div class="bg-white shadow rounded-lg p-6 mb-8">
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-center">
        <div>
            <p class="text-sm text-gray-500">Total Direct Referrals</p>
            <p class="text-2xl font-bold text-blue-600">{{ $referralStats['direct_referrals'] }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Active Members</p>
            <p class="text-2xl font-bold text-green-600">{{ $referralStats['active_referrals'] }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Next Tier Needs</p>
            <p class="text-2xl font-bold text-purple-600">
                @if($referralStats['referrals_needed'] > 0)
                    {{ $referralStats['referrals_needed'] }}
                @else
                    Max! 🎉
                @endif
            </p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Your Tier</p>
            <span class="inline-block px-3 py-1 text-sm font-semibold rounded {{ tier_badge_color($user->currentTier->slug) }} text-white">
                {{ $user->currentTier->name }}
            </span>
        </div>
    </div>
</div>

<!-- Tree Visualization -->
<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-lg font-medium text-gray-900 mb-4">Your Referral Network</h2>

    @if(empty($referralTree))
        <div class="text-center py-12">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No referrals yet</h3>
            <p class="mt-1 text-sm text-gray-500">Start sharing your referral link to build your network!</p>
            <div class="mt-4">
                <button onclick="copyReferralLink()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                    Copy Referral Link
                </button>
            </div>
        </div>
    @else
        <div class="overflow-x-auto">
            <div id="tree-container" class="min-w-full">
                <!-- Root Node -->
                <div class="flex justify-center mb-6">
                    <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-md p-4 text-center max-w-xs">
                        <div class="text-xl font-bold">{{ $user->first_name }}</div>
                        <div class="text-xs opacity-90">{{ $user->currentTier->name }} Tier</div>
                        <div class="text-xs mt-1">{{ $referralStats['direct_referrals'] }} direct</div>
                    </div>
                </div>

                <!-- Level 1: Direct Referrals -->
                @if(count($referralTree) > 0)
                    <div class="flex flex-wrap justify-center gap-6 mb-8">
                        @foreach($referralTree as $referral)
                            <div class="flex flex-col items-center">
                                <!-- Connector Line -->
                                <div class="w-px h-8 bg-gray-300 mx-auto"></div>

                                <!-- Referral Node -->
                                <div class="bg-white border border-gray-200 rounded-lg shadow p-3 text-center max-w-[120px] hover:shadow-md transition">
                                    <div class="font-medium text-sm truncate">{{ $referral['name'] }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $referral['tier'] }}</div>
                                    <div class="text-xs text-gray-600 mt-1">{{ $referral['referral_count'] }} referrals</div>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $referral['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $referral['is_active'] ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Level 2: Their Referrals (if any) -->
                                @if(!empty($referral['children']))
                                    <div class="mt-4 flex flex-wrap justify-center gap-3">
                                        @foreach($referral['children'] as $child)
                                            <div class="flex flex-col items-center">
                                                <div class="w-px h-6 bg-gray-200 mx-auto"></div>
                                                <div class="bg-gray-50 border border-gray-200 rounded text-xs p-2 text-center max-w-[100px]">
                                                    <div class="font-medium truncate">{{ $child['name'] }}</div>
                                                    <div class="text-[10px] text-gray-500 mt-0.5">{{ $child['tier'] }}</div>
                                                    <div class="text-[10px] text-gray-600 mt-0.5">{{ $child['referral_count'] }} ref</div>
                                                </div>

                                                <!-- Level 3: Grandchildren (optional preview) -->
                                                @if(!empty($child['children']) && count($child['children']) > 0)
                                                    <div class="mt-2 text-[10px] text-gray-500">
                                                        +{{ count($child['children']) }} more
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-6 text-center text-sm text-gray-600">
            <p>This tree shows up to 3 levels of your referral network.</p>
            <p>For a full list, visit <a href="{{ route('referrals.index') }}" class="text-green-600 hover:underline">My Referrals</a>.</p>
        </div>
    @endif
</div>

<!-- Quick Actions -->
<div class="mt-6 flex justify-center space-x-4">
    <button onclick="copyReferralLink()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
        📋 Copy Referral Link
    </button>
    <a href="{{ route('referrals.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-sm font-medium">
        📋 View All Referrals
    </a>
</div>
@endsection

@push('scripts')
<script>
function copyReferralLink() {
    const link = "{{ $user->referral_link }}";
    navigator.clipboard.writeText(link).then(() => {
        alert('✅ Your referral link has been copied!');
    }).catch(err => {
        console.error('Failed to copy: ', err);
        alert('Failed to copy link. Please try again.');
    });
}
</script>
@endpush
