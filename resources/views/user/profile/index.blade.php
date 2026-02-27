{{-- resources/views/user/profile/index.blade.php --}}
@extends('layouts.user')
@section('title', 'My Profile & Settings')
@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">My Profile & Settings</h1>
    <p class="mt-1 text-sm text-gray-500">
        Manage your personal information, account preferences, and security settings
    </p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Profile Info & Edit Form -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Personal Information Card -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Personal Information</h2>
            </div>
            <form action="{{ route('profile.update') }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500">
                        @error('first_name')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500">
                        @error('last_name')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" disabled
                        class="mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm p-2 cursor-not-allowed">
                    <p class="mt-1 text-xs text-gray-500">Your email cannot be changed. Contact support if needed.</p>
                </div>

                <div class="mt-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500">
                    @error('phone')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Security Settings Card -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Security</h2>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-600 mb-4">
                    Keep your account secure by updating your password regularly.
                </p>
                <a href="{{ route('password.request') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                    Change Password
                </a>
            </div>
        </div>
    </div>

    <!-- Right Column: Account Summary -->
    <div class="space-y-6">
        <!-- Membership Status -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Membership</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-500">Status</span>
                    <div class="mt-1">
                        @if($user->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Tier</span>
                    <div class="mt-1">
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded {{ tier_badge_color($user->currentTier->slug) }} text-white">
                            {{ $user->currentTier->name }}
                        </span>
                    </div>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Joined</span>
                    <p class="mt-1 text-sm font-medium">{{ $user->created_at->format('M Y') }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Next Renewal</span>
                    <p class="mt-1 text-sm font-medium">
                        @if($user->membership_expires_at)
                            {{ $user->membership_expires_at->format('M d, Y') }}
                        @else
                            Never (Lifetime)
                        @endif
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('payments.renew') }}"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                    Renew Membership
                </a>
            </div>
        </div>

        <!-- Referral Link Card -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Your Referral Link</h3>
            <div class="flex">
                <input type="text" value="{{ $user->referral_link }}" readonly
                    class="flex-1 min-w-0 block w-full px-3 py-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-l-md focus:outline-none">
                <button onclick="copyReferralLink()"
                    class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 bg-gray-100 text-gray-700 rounded-r-md hover:bg-gray-200 text-sm">
                    Copy
                </button>
            </div>
            <p class="mt-2 text-xs text-gray-500">
                Share this link to earn rewards when others join!
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyReferralLink() {
    const link = "{{ $user->referral_link }}";
    navigator.clipboard.writeText(link).then(() => {
        alert('✅ Referral link copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy: ', err);
        alert('⚠️ Could not copy link. Please try again.');
    });
}
</script>
@endpush
