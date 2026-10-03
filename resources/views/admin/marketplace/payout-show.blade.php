@extends('admin.layouts.app')

@section('title', 'Payout '.$payout->reference)
@section('header', 'Payout Details')
@section('subheader', $payout->reference)

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 12px;">
    <a href="{{ route('admin.marketplace.payouts.index') }}" class="btn btn-secondary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Payouts
    </a>
    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        @if($payout->status === 'requested')
        <form method="POST" action="{{ route('admin.marketplace.payouts.approve', $payout) }}">
            @csrf
            <button type="submit" class="btn btn-primary">Approve</button>
        </form>
        @endif
        @if($payout->status === 'approved')
        <form method="POST" action="{{ route('admin.marketplace.payouts.process', $payout) }}">
            @csrf
            <button type="submit" class="btn btn-primary">Mark Processing</button>
        </form>
        @endif
        @if(in_array($payout->status, ['approved', 'processing'], true))
        <form method="POST" action="{{ route('admin.marketplace.payouts.mark-paid', $payout) }}"
            data-confirm='{"title": "Mark as paid?", "text": "Confirm this payout has been transferred to the vendor bank account.", "btnClass": "confirm-danger", "btnText": "Mark Paid"}'>
            @csrf
            <button type="submit" class="btn btn-primary">Mark Paid</button>
        </form>
        @endif
        @if(in_array($payout->status, ['requested', 'approved'], true))
        <button type="button" class="btn btn-danger" onclick="document.getElementById('rejectForm').style.display = 'block'">Reject</button>
        @endif
    </div>
</div>

@if($payout->status === 'paid')
<div class="kpi-card" style="margin-bottom: 1.5rem; border: 1px solid #059669;">
    <div class="kpi-label">Paid out</div>
    <div class="kpi-value">₦{{ number_format((float) $payout->amount, 2) }}</div>
    <div class="kpi-trend">Settled on {{ optional($payout->paid_at)->format('M j, Y g:i A') }}</div>
</div>
@endif

@if(in_array($payout->status, ['requested', 'approved'], true))
<div id="rejectForm" class="page-card" style="display: none; margin-bottom: 1.5rem; border-color: #fecaca;">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Reject payout</h3>
            <p class="page-card-subtitle">The amount will be released back to the vendor's available balance.</p>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.marketplace.payouts.reject', $payout) }}">
        @csrf
        <div style="margin-bottom: 12px;">
            <label class="form-label">Reason (required)</label>
            <textarea name="rejection_reason" rows="3" class="form-input" required placeholder="Explain why this payout is being rejected...">{{ old('rejection_reason') }}</textarea>
        </div>
        <button type="submit" class="btn btn-danger">Reject Payout</button>
    </form>
</div>
@endif

<div class="grid-2" style="margin-bottom: 2rem;">
    <!-- Payout info -->
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">{{ $payout->reference }}</h3>
                <p class="page-card-subtitle">Payout summary</p>
            </div>
            <span class="badge
                @if($payout->status === 'paid') badge-success
                @elseif($payout->status === 'requested') badge-warning
                @elseif($payout->status === 'approved') badge-primary
                @elseif($payout->status === 'processing') badge-primary
                @else badge-error @endif">{{ ucfirst($payout->status) }}</span>
        </div>
        <div class="page-card-body">
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Amount</span><span style="font-weight: 700; color: #059669;">₦{{ number_format((float) $payout->amount, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Gross sales</span><span style="font-weight: 600; color: #1e293b;">₦{{ number_format((float) $payout->gross_sales, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Commission</span><span style="font-weight: 600; color: #1e293b;">₦{{ number_format((float) $payout->commission_amount, 2) }} ({{ number_format((float) $payout->commission_rate, 1) }}%)</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Other deductions</span><span style="font-weight: 600; color: #1e293b;">₦{{ number_format((float) $payout->other_deductions, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Requested</span><span style="font-weight: 600; color: #1e293b;">{{ optional($payout->requested_at)->format('M j, Y g:i A') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Processed</span><span style="font-weight: 600; color: #1e293b;">{{ optional($payout->processed_at)->format('M j, Y g:i A') ?? '—' }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                <span style="color: #64748b;">Paid</span><span style="font-weight: 600; color: #1e293b;">{{ optional($payout->paid_at)->format('M j, Y g:i A') ?? '—' }}</span>
            </div>
        </div>
    </div>

    <!-- Bank details -->
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Payout destination</h3>
                <p class="page-card-subtitle">{{ $payout->payout_method ?? 'Bank transfer' }}</p>
            </div>
        </div>
        <div class="page-card-body">
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Account name</span><span style="font-weight: 600; color: #1e293b;">{{ $payout->account_name }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Account number</span><span style="font-weight: 600; color: #1e293b;">{{ $payout->account_number }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Bank</span><span style="font-weight: 600; color: #1e293b;">{{ $payout->bank_name }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                <span style="color: #64748b;">Vendor</span>
                <span style="font-weight: 600; color: #1e293b;">
                    @if($payout->vendor)
                    <a href="{{ route('admin.marketplace.vendors.show', $payout->vendor) }}" class="action-link">{{ $payout->vendor->business_name ?? $payout->vendor->name }}</a>
                    @else — @endif
                </span>
            </div>
            @if($payout->rejection_reason)
            <div style="margin-top: 16px; padding: 12px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; color: #991b1b; font-size: 13px;">
                <strong>Rejection reason:</strong> {{ $payout->rejection_reason }}
            </div>
            @endif
        </div>
    </div>
</div>

@if($payout->notes)
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Activity notes</h3>
            <p class="page-card-subtitle">Audit trail for this payout</p>
        </div>
    </div>
    <div class="page-card-body" style="white-space: pre-line; color: #475569; line-height: 1.6;">{{ $payout->notes }}</div>
</div>
@endif
@endsection