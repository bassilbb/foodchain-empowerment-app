@extends('layouts.user')

@section('title', 'Voucher ' . $voucher->voucher_number)
@section('header', 'Voucher Details')
@section('subheader', 'Voucher #' . $voucher->voucher_number)

@section('content')

@if(session('success'))
<div style="background: #d1fae5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #065f46; font-weight: 500;">
    {{ session('success') }}
</div>
@endif

@if(session('info'))
<div style="background: #dbeafe; border: 1px solid #93c5fd; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #1e40af; font-weight: 500;">
    {{ session('info') }}
</div>
@endif

@if(session('error'))
<div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #dc2626; font-weight: 500;">
    {{ session('error') }}
</div>
@endif

<!-- Voucher Status Banner -->
<div style="border-radius: 16px; padding: 24px; margin-bottom: 24px;
    @if($voucher->isCollected()) background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    @elseif($voucher->isExpired()) background: linear-gradient(135deg, #fee2e2, #fecaca);
    @else background: linear-gradient(135deg, #dbeafe, #93c5fd); @endif
    display: flex; align-items: center; gap: 16px;">
    <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255,255,255,0.8); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
        <svg style="width: 24px; height: 24px; color: #1e293b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
    </div>
    <div>
        <p style="font-weight: 700; font-size: 1.125rem; color: #1e293b; margin: 0;">
            @if($voucher->isCollected()) Collected
            @elseif($voucher->isExpired()) Expired
            @elseif($voucher->isAcknowledged()) Acknowledged - Ready for Collection
            @else Pending Acknowledgment
            @endif
        </p>
        <p style="color: #475569; margin: 4px 0 0 0; font-size: 0.875rem;">
            @if($voucher->isCollected()) Collected on {{ $voucher->collected_at->format('M d, Y h:i A') }}
            @elseif($voucher->isExpired()) Expired on {{ $voucher->expires_at->format('M d, Y h:i A') }}
            @elseif($voucher->isAcknowledged()) You acknowledged this voucher. Proceed to the pickup location.
            @else Please acknowledge this voucher to proceed with collection.
            @endif
        </p>
    </div>
</div>

<div class="grid-2">
    <!-- Voucher Info -->
    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Voucher Information</h3>
        </div>
        <div class="page-card-body">
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 8px 0; color: #64748b; font-size: 0.875rem;">Voucher Number</td>
                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">{{ $voucher->voucher_number }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #64748b; font-size: 0.875rem;">Collection Code</td>
                    <td style="padding: 8px 0; text-align: right; font-weight: 700; font-family: monospace; font-size: 1.125rem; letter-spacing: 2px;">{{ $voucher->collection_code }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #64748b; font-size: 0.875rem;">Status</td>
                    <td style="padding: 8px 0; text-align: right;">
                        <span class="badge
                            @if($voucher->status === 'issued') badge-info @endif
                            @if($voucher->status === 'collected') badge-success @endif
                            @if($voucher->status === 'expired') badge-error @endif
                            @if($voucher->status === 'pending') badge-warning @endif">
                            {{ ucfirst($voucher->status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #64748b; font-size: 0.875rem;">Issue Date</td>
                    <td style="padding: 8px 0; text-align: right;">{{ $voucher->issued_at?->format('M d, Y h:i A') ?? $voucher->created_at->format('M d, Y h:i A') }}</td>
                </tr>
                @if($voucher->isAcknowledged())
                <tr>
                    <td style="padding: 8px 0; color: #64748b; font-size: 0.875rem;">Acknowledged At</td>
                    <td style="padding: 8px 0; text-align: right;">{{ $voucher->acknowledged_at->format('M d, Y h:i A') }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 8px 0; color: #64748b; font-size: 0.875rem;">Expires At</td>
                    <td style="padding: 8px 0; text-align: right; @if($voucher->expires_at && $voucher->expires_at->isPast()) color: #dc2626; @endif">
                        {{ $voucher->expires_at?->format('M d, Y h:i A') ?? 'N/A' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Distribution Info -->
    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Distribution Details</h3>
        </div>
        <div class="page-card-body">
            @if($voucher->distribution)
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 8px 0; color: #64748b; font-size: 0.875rem;">Distribution #</td>
                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">{{ $voucher->distribution->distribution_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #64748b; font-size: 0.875rem;">Package</td>
                    <td style="padding: 8px 0; text-align: right;">{{ $voucher->distribution->package?->name ?? $voucher->distribution->tier?->name ?? 'Standard' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #64748b; font-size: 0.875rem;">Package Value</td>
                    <td style="padding: 8px 0; text-align: right; font-weight: 700; color: #059669;">
                        {{ format_currency($voucher->distribution->package_value ?? 0) }}
                    </td>
                </tr>
                @if($voucher->distribution->pickupLocation)
                <tr>
                    <td style="padding: 8px 0; color: #64748b; font-size: 0.875rem;">Pickup Location</td>
                    <td style="padding: 8px 0; text-align: right;">{{ $voucher->distribution->pickupLocation->name }}</td>
                </tr>
                @endif
            </table>
            @else
            <p style="color: #94a3b8; text-align: center; padding: 2rem;">No distribution linked to this voucher.</p>
            @endif
        </div>
    </div>
</div>

<!-- Distribution Items -->
@if($voucher->distribution && $voucher->distribution->items->isNotEmpty())
<div class="page-card" style="margin-top: 24px;">
    <div class="page-card-header">
        <h3 class="page-card-title">Package Items</h3>
        <p class="page-card-subtitle">{{ $voucher->distribution->items->count() }} item(s)</p>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Unit</th>
                    <th style="text-align: right;">Quantity</th>
                    <th style="text-align: right;">Unit Value</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($voucher->distribution->items as $item)
                <tr>
                    <td>
                        <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $item->foodItem?->name ?? 'Item' }}</p>
                    </td>
                    <td style="color: #64748b;">{{ $item->foodItem?->unit ?? '' }}</td>
                    <td style="text-align: right;">{{ number_format($item->quantity, 2) }}</td>
                    <td style="text-align: right;">{{ format_currency($item->unit_value) }}</td>
                    <td style="text-align: right; font-weight: 600;">{{ format_currency($item->quantity * $item->unit_value) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                    <td style="text-align: right; font-weight: 700; padding-top: 12px;">Total:</td>
                    <td style="text-align: right; font-weight: 700; font-size: 1.125rem; color: #059669; padding-top: 12px;">
                        {{ format_currency($voucher->distribution->package_value ?? $voucher->distribution->items->sum(fn($i) => $i->quantity * $i->unit_value)) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

<!-- Actions -->
<div style="margin-top: 24px; display: flex; gap: 12px;">
    @if($voucher->isIssued() && !$voucher->isAcknowledged())
    <form method="POST" action="{{ route('vouchers.acknowledge', $voucher) }}">
        @csrf
        <button type="submit" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 0.875rem; cursor: pointer; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Acknowledge Voucher
        </button>
    </form>
    @endif
    <a href="{{ route('vouchers.index') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 12px; font-weight: 600; font-size: 0.875rem; text-decoration: none;">
        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Vouchers
    </a>
</div>

@endsection
