{{-- resources/views/user/referrals/tree.blade.php --}}
@extends('layouts.user')
@section('title', 'Referral Tree')
@section('header', 'Referral Tree')
@section('subheader', 'Visualize your community growth and track your referrals')

@section('content')
<div class="stats-grid">
    <div class="stat-card cyan">
        <div class="stat-icon cyan">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <div class="stat-label">Total Direct Referrals</div>
        <div class="stat-value" style="color: #06b6d4;">{{ $referralStats['direct_referrals'] }}</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon orange">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="stat-label">Active Members</div>
        <div class="stat-value" style="color: #f97316;">{{ $referralStats['active_referrals'] }}</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon purple">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
        </div>
        <div class="stat-label">Next Tier Needs</div>
        <div class="stat-value" style="color: #a855f7;">
            @if($referralStats['referrals_needed'] > 0)
                {{ $referralStats['referrals_needed'] }}
            @else
                Max!
            @endif
        </div>
    </div>
    <div class="stat-card rose">
        <div class="stat-icon rose">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
        </div>
        <div class="stat-label">Your Tier</div>
        <div class="stat-value" style="font-size: 1.25rem;">{{ $user->currentTier?->name ?? 'Bronze' }}</div>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">Your Referral Network</h3>
    </div>
    <div class="page-card-body">
        @if(empty($referralTree))
            <div class="empty-state">
                <div class="empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="empty-title">No referrals yet</h3>
                <p class="empty-text">Start sharing your referral link to build your network!</p>
                <button onclick="copyReferralLink()" class="btn btn-primary" style="margin-top: 1rem;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    Copy Referral Link
                </button>
            </div>
        @else
            <div style="text-align: center; padding: 1.5rem 0; margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9;">
                <div style="display: inline-block; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 2rem; border-radius: 12px; box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);">
                    <div style="font-size: 1.25rem; font-weight: 700;">{{ $user->first_name }}</div>
                    <div style="font-size: 0.75rem; opacity: 0.9;">{{ $user->currentTier->name }} Tier</div>
                    <div style="font-size: 0.75rem; opacity: 0.9; margin-top: 0.25rem;">{{ $referralStats['direct_referrals'] }} direct</div>
                </div>
            </div>

            @if(count($referralTree) > 0)
            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 1.5rem;">
                @foreach($referralTree as $referral)
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <div style="width: 2px; height: 24px; background: #cbd5e1; margin: 0 auto;"></div>
                        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; text-align: center; max-width: 140px; transition: all 0.3s; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                            <div style="font-weight: 600; color: #1e293b; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 120px;">{{ $referral['name'] }}</div>
                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">{{ $referral['tier'] }}</div>
                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">{{ $referral['referral_count'] }} refs</div>
                            <span class="badge {{ $referral['is_active'] ? 'badge-success' : 'badge-warning' }}" style="margin-top: 0.5rem;">
                                {{ $referral['is_active'] ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        @if(!empty($referral['children']))
                            <div style="margin-top: 1rem; display: flex; flex-wrap: wrap; justify-content: center; gap: 0.75rem;">
                                @foreach($referral['children'] as $child)
                                    <div style="display: flex; flex-direction: column; align-items: center;">
                                        <div style="width: 2px; height: 16px; background: #e2e8f0; margin: 0 auto;"></div>
                                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.5rem; text-align: center; max-width: 100px;">
                                            <div style="font-weight: 500; color: #475569; font-size: 0.75rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 80px;">{{ $child['name'] }}</div>
                                            <div style="font-size: 0.625rem; color: #94a3b8;">{{ $child['tier'] }}</div>
                                            @if(!empty($child['children']) && count($child['children']) > 0)
                                                <div style="font-size: 0.625rem; color: #94a3b8; margin-top: 0.25rem;">+{{ count($child['children']) }} more</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            @endif

            <div style="text-align: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                <p style="color: #64748b; font-size: 0.875rem; margin: 0;">This tree shows up to 3 levels of your referral network.</p>
                <a href="{{ route('referrals.index') }}" style="color: #10b981; font-size: 0.875rem; text-decoration: none; font-weight: 500;">View All Referrals →</a>
            </div>
        @endif
    </div>
</div>

<div style="display: flex; justify-content: center; gap: 1rem; margin-top: 1.5rem;">
    <button onclick="copyReferralLink()" class="btn btn-primary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
        Copy Referral Link
    </button>
    <a href="{{ route('referrals.index') }}" class="btn btn-secondary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
        View All Referrals
    </a>
</div>
@endsection

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
    btn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Copied!';
    btn.style.background = 'linear-gradient(135deg, #059669, #047857)';
    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.style.background = '';
    }, 2000);
}
</script>
@endpush