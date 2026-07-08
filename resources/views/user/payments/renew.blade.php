{{-- resources/views/user/payments/renew.blade.php --}}
@extends('layouts.user')
@section('title', 'Renew Membership')
@section('header', 'Renew Membership')
@section('subheader', 'Extend your access and continue enjoying all platform benefits')

@section('content')
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Current Plan</h3>
        </div>
        <div class="page-card-body">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <span class="badge" style="background: #10b981; color: white;">
                            {{ $user->currentTier?->name ?? 'Bronze' }}
                        </span>
                        <p style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 1rem 0 0.5rem 0;">{{ $user->currentTier?->formatted_price ?? 'N/A' }}/year</p>
                        <p style="color: #64748b; font-size: 0.875rem; margin: 0;">
                            @if($user->membership_expires_at)
                                Current expiry: {{ $user->membership_expires_at->format('M d, Y') }}
                            @else
                                Lifetime member (eligible for renewal to extend benefits)
                            @endif
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 0.25rem 0;">Next Billing</p>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ now()->addYear()->format('M d, Y') }}</p>
                    </div>
                </div>

                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                    <h4 style="font-weight: 500; color: #475569; font-size: 0.875rem; margin: 0 0 0.75rem 0;">Includes:</h4>
                    <ul style="color: #64748b; font-size: 0.875rem; padding-left: 1.25rem; margin: 0;">
                        @foreach(explode("\n", trim($user->currentTier->features)) as $feature)
                            <li style="margin-bottom: 0.25rem;">✓ {{ $feature }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div>
                <h3 style="font-weight: 600; color: #1e293b; margin: 0 0 1rem 0;">Payment Details</h3>

                <form action="{{ route('payments.renew.post') }}" method="POST" id="payment-form">
                    @csrf
                    <input type="hidden" name="tier_id" value="{{ $user->currentTier->id }}">

                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                            <svg style="width: 24px; height: 24px; color: #10b981; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            <span style="font-size: 0.875rem; color: #065f46;">You will be redirected to <strong>Paystack</strong> to complete payment securely.</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <svg style="width: 24px; height: 24px; color: #10b981; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            <span style="font-size: 0.875rem; color: #065f46;">Visa, Mastercard, Verve, and more accepted.</span>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" id="pay-button" class="btn btn-primary">
                            <span>Pay {{ $user->currentTier->formatted_price }} with Paystack</span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div>
        <div class="page-card">
            <div class="page-card-body" style="background: #dbeafe; border: 1px solid #bfdbfe; border-radius: 12px;">
                <h3 style="font-weight: 600; color: #1e40af; margin: 0 0 0.75rem 0; font-size: 0.9rem;">Why Renew?</h3>
                <ul style="color: #1e3a8a; font-size: 0.875rem; padding-left: 1.25rem; margin: 0;">
                    <li style="margin-bottom: 0.25rem;">Keep earning from referrals</li>
                    <li style="margin-bottom: 0.25rem;">Maintain your current tier benefits</li>
                    <li style="margin-bottom: 0.25rem;">Access exclusive community events</li>
                    <li>Avoid account deactivation</li>
                </ul>
            </div>
        </div>

        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Need Help?</h3>
            </div>
            <div class="page-card-body">
                <p style="color: #64748b; font-size: 0.875rem; margin: 0 0 0.75rem 0;">
                    Contact our support team if you have questions about billing.
                </p>
                <a href="{{ route('support.create') }}" style="color: #10b981; font-size: 0.875rem; font-weight: 500; text-decoration: none;">
                    Open a Support Ticket →
                </a>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const payButton = document.getElementById('pay-button');
    if (payButton) {
        payButton.addEventListener('click', function () {
            payButton.disabled = true;
            payButton.innerHTML = '<span>Redirecting to Paystack...</span>';
        });
    }
});
</script>
@endpush