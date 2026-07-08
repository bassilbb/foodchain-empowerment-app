@extends('admin.layouts.app')

@section('title', 'Payment Details')
@section('page-title', 'Payment: ' . $payment->reference)

@section('content')
<div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; @media(min-width: 992px) { grid-template-columns: 2fr 1fr; }">
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Payment Information</h3>
            </div>
            <div class="page-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Reference</p>
                        <p style="font-family: monospace; font-weight: 500; color: #1e293b;">{{ $payment->reference }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Amount</p>
                        <p style="font-size: 1.5rem; font-weight: 700; color: #1e293b;">{{ format_currency($payment->amount) }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Type</p>
                        <p style="font-weight: 500; color: #1e293b;">{{ ucfirst(str_replace('_', ' ', $payment->type)) }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Status</p>
                        <p><span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;
                            @if($payment->status === 'successful') background: #dcfce7; color: #16a34a; @endif
                            @if($payment->status === 'pending') background: #fef3c7; color: #d97706; @endif
                            @if($payment->status === 'failed') background: #fee2e2; color: #dc2626; @endif">
                            {{ ucfirst($payment->status) }}
                        </span></p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Payment Method</p>
                        <p style="font-weight: 500; color: #1e293b;">{{ ucfirst($payment->payment_method) }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Date</p>
                        <p style="font-weight: 500; color: #1e293b;">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @if($payment->paid_at)
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Paid At</p>
                        <p style="font-weight: 500; color: #1e293b;">{{ $payment->paid_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @endif
                    @if($payment->paystack_reference)
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Paystack Reference</p>
                        <p style="font-family: monospace; font-size: 0.875rem; color: #1e293b;">{{ $payment->paystack_reference }}</p>
                    </div>
                    @endif
                </div>

                @if($payment->isPending())
                <div style="margin-top: 1.5rem;">
                    <form method="POST" action="{{ route('admin.payments.verify', $payment) }}">
                        @csrf
                        <button type="submit" style="padding: 0.5rem 1rem; background: #2563eb; color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">
                            Verify Payment
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        @if($payment->paystack_response)
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Paystack Response</h3>
            </div>
            <div class="page-card-body">
                <pre style="background: #f8fafc; padding: 1rem; border-radius: 8px; font-size: 0.75rem; overflow: auto; margin: 0;">{{ json_encode($payment->paystack_response, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
        @endif
    </div>

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">User Details</h3>
            </div>
            <div class="page-card-body">
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b;">Name</p>
                        <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $payment->user?->full_name ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b;">Email</p>
                        <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $payment->user?->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b;">Phone</p>
                        <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $payment->user?->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b;">Tier</p>
                        <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; color: white;
                            @if(($payment->user?->currentTier?->slug ?? 'bronze') === 'bronze') background: linear-gradient(135deg, #f59e0b, #d97706); @endif
                            @if(($payment->user?->currentTier?->slug ?? 'bronze') === 'silver') background: linear-gradient(135deg, #64748b, #475569); @endif
                            @if(($payment->user?->currentTier?->slug ?? 'bronze') === 'gold') background: linear-gradient(135deg, #eab308, #ca8a04); @endif
                            @if(($payment->user?->currentTier?->slug ?? 'bronze') === 'emerald') background: linear-gradient(135deg, #10b981, #059669); @endif">
                            {{ $payment->user?->currentTier?->name ?? 'Bronze' }}
                        </span>
                    </div>
                </div>
                <div style="margin-top: 1.5rem;">
                    <a href="{{ route('admin.users.show', $payment->user?->id) }}" style="display: block; text-align: center; padding: 0.5rem 1rem; background: #f1f5f9; color: #475569; border-radius: 8px; font-weight: 500; text-decoration: none;">
                        View User Profile
                    </a>
                </div>
            </div>
        </div>

        @if($payment->starterPack)
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Starter Pack</h3>
            </div>
            <div class="page-card-body">
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b;">Package Value</p>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ format_currency($payment->starterPack->package_value) }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b;">Status</p>
                        <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ ucfirst($payment->starterPack->status) }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
