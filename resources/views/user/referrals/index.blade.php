{{-- resources/views/user/referrals/index.blade.php --}}
@extends('layouts.user')

@section('title', 'My Referrals')
@section('header', 'My Referrals')
@section('subheader', 'Track your referrals and grow your community')

@section('content')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<!-- Stats Grid -->
<div class="stats-grid" style="margin-bottom: 1.5rem;">
    <div class="stat-card cyan">
        <div class="stat-icon cyan">
            <svg style="width: 24px; height: 24px; color: #0891b2;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <p class="stat-label">Direct Referrals</p>
        <p class="stat-value">{{ number_format($referralStats['direct_referrals']) }}</p>
    </div>

    <div class="stat-card purple">
        <div class="stat-icon purple">
            <svg style="width: 24px; height: 24px; color: #9333ea;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
        </div>
        <p class="stat-label">Total Community</p>
        <p class="stat-value">{{ number_format($referralStats['direct_referrals'] + ($referralStats['indirect_referrals'] ?? 0)) }}</p>
    </div>

    <div class="stat-card orange">
        <div class="stat-icon orange">
            <svg style="width: 24px; height: 24px; color: #ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
        </div>
        <p class="stat-label">Current Tier</p>
        <p class="stat-value">{{ $user->currentTier?->name ?? 'Bronze' }}</p>
        <span class="badge" style="background: linear-gradient(135deg, #f97316, #ea580c); color: white; margin-top: 0.5rem;">{{ $referralStats['referrals_needed'] }} to upgrade</span>
    </div>

    <div class="stat-card rose">
        <div class="stat-icon rose">
            <svg style="width: 24px; height: 24px; color: #e11d48;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <p class="stat-label">Required for Next Tier</p>
        <p class="stat-value">{{ $referralStats['referrals_needed'] }}</p>
    </div>
</div>

<!-- Referral Code -->
<div class="page-card" style="padding: 1.5rem; margin-bottom: 1.5rem; background: linear-gradient(135deg, #f0fdf4 0%, #ecfeff 100%);">
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
        <button onclick="copyReferralLink()" class="btn btn-primary">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
            Copy Link
        </button>
    </div>
    <div style="margin-top: 1rem; padding: 0.75rem; background: white; border-radius: 8px; border: 1px solid #e2e8f0;">
        <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.5rem;">Your Referral Link:</p>
        <div style="display: flex; gap: 0.5rem;">
            <input type="text" id="referralLinkInput" readonly value="{{ url('/register?ref=' . $user->referral_code) }}" style="flex: 1; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.875rem; font-family: monospace; color: #10b981;">
            <button onclick="copyFromInput()" style="padding: 0.5rem 1rem; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 6px; font-size: 0.875rem; font-weight: 500; cursor: pointer;">Copy</button>
        </div>
    </div>
    <p style="font-size: 0.875rem; color: #64748b; margin-top: 1rem;">Share this link to invite new members and earn rewards!</p>
</div>

<!-- Referrals List -->
<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">Your Referrals</h3>
        <p class="page-card-subtitle">{{ $referrals->total() }} total referrals</p>
    </div>
    
    @if($referrals->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <p class="empty-title">No referrals yet</p>
        <p class="empty-text">Share your referral code to invite new members!</p>
    </div>
    @else
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Tier</th>
                    <th>Joined</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($referrals as $referral)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #34d399, #38bdf8); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                                {{ substr($referral->first_name ?? 'U', 0, 1) }}{{ substr($referral->last_name ?? '', 0, 1) }}
                            </div>
                            <div>
                                <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $referral->full_name ?? 'Unknown' }}</p>
                                <p style="font-size: 0.875rem; color: #64748b; margin: 0;">{{ $referral->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-purple">{{ $referral->currentTier?->name ?? 'Bronze' }}</span>
                    </td>
                    <td style="color: #64748b;">{{ $referral->created_at?->format('M d, Y') }}</td>
                    <td>
                        <span class="badge {{ $referral->is_active ? 'badge-success' : 'badge-warning' }}">
                            {{ $referral->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@if($referrals->hasPages())
<div style="display: flex; justify-content: center; margin-top: 1.5rem;">
    {{ $referrals->links() }}
</div>
@endif

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

function copyFromInput() {
    const input = document.getElementById('referralLinkInput');
    input.select();
    input.setSelectionRange(0, 99999);
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(input.value).then(() => {
            showCopyFeedback('input');
        }).catch(() => {
            document.execCommand('copy');
            showCopyFeedback('input');
        });
    } else {
        document.execCommand('copy');
        showCopyFeedback('input');
    }
}

function showCopyFeedback(type) {
    let btn, originalText;
    if (type === 'input') {
        btn = document.querySelector('#referralLinkInput + button');
    }
    if (btn) {
        originalText = btn.innerText;
        btn.innerText = 'Copied!';
        btn.style.background = 'linear-gradient(135deg, #059669, #047857)';
        setTimeout(() => {
            btn.innerText = originalText;
            btn.style.background = '';
        }, 2000);
    }
}
</script>
@endpush
@endsection