{{-- resources/views/admin/users/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'User Details')
@section('header', 'User Details')
@section('subheader', $user->full_name)

@section('content')
<style>
@media (min-width: 1024px) {
    .user-detail-grid {
        grid-template-columns: 320px 1fr;
    }
}
</style>
<div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
    <a href="{{ route('admin.users.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: #64748b; text-decoration: none; font-size: 0.875rem; width: fit-content;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Users
    </a>
</div>

<div class="user-detail-grid" style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
    <!-- Left Column - User Info -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- User Information Card -->
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">User Information</h3>
            </div>
            <div class="page-card-body">
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Full Name</label>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $user->full_name }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Email</label>
                        <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Phone</label>
                        <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $user->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Referral Code</label>
                        <p style="font-weight: 700; color: #10b981; font-family: monospace; margin: 0; background: linear-gradient(135deg, #ecfdf5, #d1fae5); padding: 0.5rem 0.75rem; border-radius: 8px;">{{ $user->referral_code }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Current Tier</label>
                        <form method="POST" action="{{ route('admin.users.update-tier', $user) }}" id="tier-form" style="display: flex; gap: 0.5rem; align-items: center;">
                            @csrf
                            <select name="tier_id" id="tier-select" style="flex: 1; padding: 0.5rem 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; background: white;">
                                @foreach($tiers as $tier)
                                <option value="{{ $tier->id }}" {{ ($user->currentTier?->id ?? null) == $tier->id ? 'selected' : '' }}>
                                    {{ $tier->name }}
                                </option>
                                @endforeach
                            </select>
                            <button type="submit" id="tier-update-btn" style="display: none; padding: 0.5rem 0.75rem; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 8px; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                                Update
                            </button>
                        </form>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Status</label>
                        @if($user->is_active)
                        <span style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, #dcfce7, #bbf7d0); border-radius: 10px; font-weight: 600; color: #16a34a;">
                            <span style="width: 8px; height: 8px; background: #16a34a; border-radius: 50%;"></span>
                            Active
                        </span>
                        @else
                        <span style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, #fee2e2, #fecaca); border-radius: 10px; font-weight: 600; color: #dc2626;">
                            <span style="width: 8px; height: 8px; background: #dc2626; border-radius: 50%;"></span>
                            Inactive
                        </span>
                        @endif
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Member Since</label>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    @if($user->membership_expires_at)
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Membership Expires</label>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $user->membership_expires_at->format('M d, Y') }}</p>
                    </div>
                    @endif
                </div>

                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9;">
                    @if($user->is_active)
                        <form method="POST" action="{{ route('admin.users.deactivate', $user) }}">
                            @csrf
                            <button type="submit" style="width: 100%; padding: 0.875rem; background: linear-gradient(135deg, #dc2626, #b91c1c); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; display: inline; vertical-align: middle; margin-right: 0.5rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                Deactivate User
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                            @csrf
                            <button type="submit" style="width: 100%; padding: 0.875rem; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; display: inline; vertical-align: middle; margin-right: 0.5rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Activate User
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Referral Stats Card -->
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Referral Stats</h3>
            </div>
            <div class="page-card-body">
                <div style="display: grid; gap: 1.25rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Direct Referrals</label>
                        <p style="font-size: 2rem; font-weight: 700; color: #10b981; margin: 0;">{{ $referralStats['direct_referrals'] }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Active Referrals</label>
                        <p style="font-size: 2rem; font-weight: 700; color: #2563eb; margin: 0;">{{ $referralStats['active_referrals'] }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Next Tier Needs</label>
                        <p style="font-size: 2rem; font-weight: 700; color: #f59e0b; margin: 0;">{{ $referralStats['referrals_needed'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column - Main Content -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Payment History -->
        <div class="page-card">
            <div class="page-card-header">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #d1fae5, #a7f3d0); display: flex; align-items: center; justify-content: center;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #059669;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="page-card-title">Payment History</h3>
                        <p class="page-card-subtitle">User's transaction records</p>
                    </div>
                </div>
            </div>
            <div style="padding: 0;">
                @forelse($user->payments as $payment)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <div>
                        <p style="font-weight: 600; color: #1e293b; margin: 0; text-transform: capitalize;">{{ str_replace('_', ' ', $payment->type) }}</p>
                        <p style="font-size: 0.875rem; color: #64748b; margin: 0.25rem 0 0 0;">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="font-weight: 700; color: #1e293b; margin: 0; font-size: 1.125rem;">{{ format_currency($payment->amount) }}</p>
                        <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; margin-top: 0.25rem;
                            @if($payment->status === 'successful') background: #dcfce7; color: #16a34a; @endif
                            @if($payment->status === 'pending') background: #fef3c7; color: #d97706; @endif
                            @if($payment->status === 'failed') background: #fee2e2; color: #dc2626; @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <div style="padding: 3rem; text-align: center; color: #94a3b8;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 48px; height: 48px; margin: 0 auto 1rem; opacity: 0.5;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    No payments yet
                </div>
                @endforelse
            </div>
        </div>

        <!-- Food Distributions -->
        <div class="page-card">
            <div class="page-card-header">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #dbeafe, #bfdbfe); display: flex; align-items: center; justify-content: center;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #2563eb;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <div>
                        <h3 class="page-card-title">Food Distributions</h3>
                        <p class="page-card-subtitle">User's food package deliveries</p>
                    </div>
                </div>
            </div>
            <div style="padding: 0;">
                @forelse($user->distributions as $distribution)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <div>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $distribution->distribution_number }}</p>
                        <p style="font-size: 0.875rem; color: #64748b; margin: 0.25rem 0 0 0;">{{ $distribution->created_at->format('M d, Y') }}</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="font-weight: 700; color: #1e293b; margin: 0; font-size: 1.125rem;">{{ format_currency($distribution->package_value) }}</p>
                        <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; margin-top: 0.25rem; background: #dbeafe; color: #2563eb;">
                            {{ ucfirst($distribution->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <div style="padding: 3rem; text-align: center; color: #94a3b8;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 48px; height: 48px; margin: 0 auto 1rem; opacity: 0.5;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    No distributions yet
                </div>
                @endforelse
            </div>
        </div>

        <!-- Referral Tree -->
        <div class="page-card">
            <div class="page-card-header">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #f3e8ff, #e9d5ff); display: flex; align-items: center; justify-content: center;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #9333ea;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="page-card-title">Referral Tree</h3>
                        <p class="page-card-subtitle">User's direct referrals</p>
                    </div>
                </div>
            </div>
            <div class="page-card-body">
                @if(count($referralTree) > 0)
                <div style="display: grid; gap: 0.75rem;">
                    @foreach($referralTree as $referral)
                    <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-radius: 12px; border-left: 4px solid #10b981;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.875rem;">
                            {{ substr($referral['name'] ?? 'U', 0, 1) }}
                        </div>
                        <div style="flex: 1;">
                            <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $referral['name'] }}</p>
                            <p style="font-size: 0.875rem; color: #64748b; margin: 0;">{{ $referral['email'] }}</p>
                        </div>
                        <div style="text-align: right;">
                            <span class="badge" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; font-size: 0.625rem;">{{ $referral['tier'] }}</span>
                            <p style="font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0 0;">{{ $referral['referral_count'] }} refs</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div style="padding: 2rem; text-align: center; color: #94a3b8;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 48px; height: 48px; margin: 0 auto 1rem; opacity: 0.5;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    No referrals yet
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('tier-select').addEventListener('change', function() {
    var btn = document.getElementById('tier-update-btn');
    if (this.value != '{{ $user->currentTier->id ?? '' }}') {
        btn.style.display = 'block';
    } else {
        btn.style.display = 'none';
    }
});
</script>
@endpush
@endsection