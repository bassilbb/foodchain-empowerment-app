{{-- resources/views/admin/users/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'User Details')
@section('page-title', 'User: ' . $user->full_name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- User Info Card -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">User Information</h3>

            <div class="space-y-3">
                <div>
                    <label class="text-sm text-gray-500">Full Name</label>
                    <p class="font-medium">{{ $user->full_name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Email</label>
                    <p class="font-medium">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Phone</label>
                    <p class="font-medium">{{ $user->phone }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Referral Code</label>
                    <p class="font-mono font-medium">{{ $user->referral_code }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Current Tier</label>
                    <p><span class="px-2 py-1 text-xs font-semibold rounded {{ tier_badge_color($user->currentTier->slug) }} text-white">
                        {{ $user->currentTier->name }}
                    </span></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Status</label>
                    <p><span class="px-2 py-1 text-xs font-semibold rounded {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Member Since</label>
                    <p class="font-medium">{{ $user->created_at->format('M d, Y') }}</p>
                </div>
                @if($user->membership_expires_at)
                <div>
                    <label class="text-sm text-gray-500">Membership Expires</label>
                    <p class="font-medium">{{ $user->membership_expires_at->format('M d, Y') }}</p>
                </div>
                @endif
            </div>

            <div class="mt-6 space-y-2">
                @if($user->is_active)
                    <form method="POST" action="{{ route('admin.users.deactivate', $user) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            Deactivate User
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            Activate User
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Referral Stats -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="text-lg font-semibold mb-4">Referral Stats</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm text-gray-500">Direct Referrals</label>
                    <p class="text-2xl font-bold">{{ $referralStats['direct_referrals'] }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Active Referrals</label>
                    <p class="text-2xl font-bold">{{ $referralStats['active_referrals'] }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Referrals Needed for Next Tier</label>
                    <p class="text-2xl font-bold">{{ $referralStats['referrals_needed'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Payments -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold">Payment History</h3>
            </div>
            <div class="divide-y">
                @forelse($user->payments as $payment)
                <div class="px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $payment->type)) }}</p>
                            <p class="text-sm text-gray-500">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold">{{ format_currency($payment->amount) }}</p>
                            <span class="text-xs px-2 py-1 rounded {{ $payment->status === 'successful' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-4 text-center text-gray-500">No payments yet</div>
                @endforelse
            </div>
        </div>

        <!-- Distributions -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold">Food Distributions</h3>
            </div>
            <div class="divide-y">
                @forelse($user->distributions as $distribution)
                <div class="px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium">{{ $distribution->distribution_number }}</p>
                            <p class="text-sm text-gray-500">{{ $distribution->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold">{{ format_currency($distribution->package_value) }}</p>
                            <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-800">
                                {{ ucfirst($distribution->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-4 text-center text-gray-500">No distributions yet</div>
                @endforelse
            </div>
        </div>

        <!-- Referral Tree -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Referral Tree</h3>
            @if(count($referralTree) > 0)
                <div class="space-y-2">
                    @foreach($referralTree as $referral)
                    <div class="border-l-2 border-blue-500 pl-4">
                        <p class="font-medium">{{ $referral['name'] }}</p>
                        <p class="text-sm text-gray-500">{{ $referral['email'] }} • {{ $referral['tier'] }} • {{ $referral['referral_count'] }} referrals</p>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No referrals yet</p>
            @endif
        </div>
    </div>
</div>
@endsection
