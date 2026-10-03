@extends('admin.layouts.app')

@section('title', 'Issuance')
@section('header', 'Issuance')
@section('subheader', 'Look up vouchers and process collections')

@section('content')
@php
$vBadge = ['issued' => 'info', 'collected' => 'success', 'expired' => 'dark'];
@endphp

<div class="page-card">
    <div class="page-card-body">
        <form method="GET" action="{{ route('admin.issuance.lookup') }}" style="display: flex; gap: 1rem; align-items: flex-end;">
            <div style="flex: 1;">
                <label class="form-label">
                    Search by Voucher Number or Collection Code
                </label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Type voucher number or collection code..." required class="form-input">
            </div>
            <button type="submit" class="btn btn-primary" style="white-space: nowrap;">
                Look Up
            </button>
        </form>
    </div>
</div>

@if($voucher)
<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">Voucher Details</h3>
        <span class="badge badge-{{ $vBadge[$voucher->status] ?? 'warning' }}">
            {{ ucfirst($voucher->status) }}
        </span>
    </div>
    <div class="page-card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <div style="background: #f9fafb; border-radius: 0.75rem; padding: 1rem;">
                <p class="form-label" style="text-transform: uppercase; font-size: 0.7rem; margin: 0 0 0.25rem 0;">Voucher Number</p>
                <p style="font-family: monospace; font-weight: 700; font-size: 1.1rem; color: #1f2937; margin: 0;">{{ $voucher->voucher_number }}</p>
            </div>
            <div style="background: #f9fafb; border-radius: 0.75rem; padding: 1rem;">
                <p class="form-label" style="text-transform: uppercase; font-size: 0.7rem; margin: 0 0 0.25rem 0;">Collection Code</p>
                <p style="font-family: monospace; font-weight: 700; font-size: 1.1rem; color: #059669; margin: 0; letter-spacing: 0.1em;">{{ $voucher->collection_code ?? 'N/A' }}</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <div style="background: #f0fdf4; border-radius: 0.75rem; padding: 1rem; border: 1px solid #bbf7d0;">
                <p class="form-label" style="text-transform: uppercase; font-size: 0.7rem; margin: 0 0 0.25rem 0;">Member</p>
                <p style="font-weight: 700; color: #1f2937; margin: 0;">{{ $voucher->user?->full_name ?? 'N/A' }}</p>
                <p style="color: #64748b; font-size: 0.85rem; margin: 0.15rem 0 0 0;">{{ $voucher->user?->email ?? '' }}</p>
            </div>
            <div style="background: #f9fafb; border-radius: 0.75rem; padding: 1rem;">
                <p class="form-label" style="text-transform: uppercase; font-size: 0.7rem; margin: 0 0 0.25rem 0;">Phone</p>
                <p style="font-weight: 600; color: #374151; margin: 0;">{{ $voucher->user?->phone ?? 'Not provided' }}</p>
            </div>
        </div>

        @if($voucher->distribution)
        <div style="margin-bottom: 1.5rem;">
            <h4 style="font-size: 0.95rem; font-weight: 700; color: #1f2937; margin: 0 0 0.75rem 0;">
                Items to Collect — {{ $voucher->distribution->name ?? $voucher->distribution->distribution_number }}
            </h4>
            <div class="page-card" style="margin-bottom: 0;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th style="text-align: center;">Quantity</th>
                            <th style="text-align: right;">Unit Value</th>
                            <th style="text-align: right;">Total Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($voucher->distribution->items as $item)
                        <tr>
                            <td style="font-weight: 600; color: #1e293b;">{{ $item->foodItem?->name ?? 'N/A' }}</td>
                            <td style="text-align: center; color: #475569;">{{ number_format($item->quantity, 1) }} {{ $item->foodItem?->unit ?? '' }}</td>
                            <td style="text-align: right; font-family: monospace; color: #475569;">₦{{ number_format($item->unit_value, 2) }}</td>
                            <td style="text-align: right; font-family: monospace; font-weight: 600; color: #059669;">₦{{ number_format($item->total_value, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="empty-state">No items listed in this distribution</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <div style="background: #f9fafb; border-radius: 0.75rem; padding: 0.75rem;">
                <p class="form-label" style="text-transform: uppercase; font-size: 0.65rem; margin: 0 0 0.15rem 0;">Distribution Date</p>
                <p style="font-weight: 600; color: #1f2937; margin: 0; font-size: 0.9rem;">{{ $voucher->distribution->scheduled_date ? $voucher->distribution->scheduled_date->format('M d, Y') : 'N/A' }}</p>
            </div>
            <div style="background: #f9fafb; border-radius: 0.75rem; padding: 0.75rem;">
                <p class="form-label" style="text-transform: uppercase; font-size: 0.65rem; margin: 0 0 0.15rem 0;">Pickup Location</p>
                <p style="font-weight: 600; color: #1f2937; margin: 0; font-size: 0.9rem;">{{ $voucher->distribution?->pickupLocation?->name ?? 'N/A' }}</p>
            </div>
            <div style="background: #f9fafb; border-radius: 0.75rem; padding: 0.75rem;">
                <p class="form-label" style="text-transform: uppercase; font-size: 0.65rem; margin: 0 0 0.15rem 0;">Expires</p>
                <p style="font-weight: 600; color: #dc2626; margin: 0; font-size: 0.9rem;">{{ $voucher->expires_at ? $voucher->expires_at->format('M d, Y g:i A') : 'No expiry' }}</p>
            </div>
        </div>
        @else
        <div class="alert alert-warning" style="margin-bottom: 1.5rem;">
            <div>
                <p style="margin: 0 0 0.25rem 0; font-weight: 600;">No Distribution Linked</p>
                <p style="margin: 0 0 1rem 0; font-size: 0.85rem;">This voucher was issued manually. Select a distribution below to link it and show collectible items.</p>

                @if($distributions->isNotEmpty())
                <form method="POST" action="{{ route('admin.issuance.link-distribution', $voucher) }}">
                    @csrf
                    <div style="display: flex; gap: 1rem; align-items: flex-end;">
                        <div style="flex: 1;">
                            <select name="food_distribution_id" required class="form-select" style="border-color: #fde68a;">
                                <option value="">Select a distribution...</option>
                                @foreach($distributions as $dist)
                                <option value="{{ $dist->id }}">
                                    {{ $dist->distribution_number }} — ₦{{ number_format($dist->package_value, 0) }} ({{ $dist->items->count() }} items)
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn" style="padding: 0.7rem 1.5rem; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; white-space: nowrap;">
                            Link & Show Items
                        </button>
                    </div>
                </form>

                <div style="margin-top: 1.25rem; display: grid; gap: 1rem;">
                    @foreach($distributions as $dist)
                    <div style="background: white; border: 1px solid #fde68a; border-radius: 0.75rem; padding: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $dist->distribution_number }}</p>
                            <p style="font-size: 0.8rem; color: #64748b; margin: 0;">₦{{ number_format($dist->package_value, 2) }}</p>
                        </div>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem;">
                            <thead>
                                <tr style="border-bottom: 1px solid #fef3c7;">
                                    <th style="padding: 0.35rem 0.5rem; text-align: left; color: #64748b; font-weight: 600;">Item</th>
                                    <th style="padding: 0.35rem 0.5rem; text-align: center; color: #64748b; font-weight: 600;">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dist->items as $item)
                                <tr style="border-bottom: 1px solid #fef9c3;">
                                    <td style="padding: 0.25rem 0.5rem; color: #1e293b;">{{ $item->foodItem?->name ?? 'N/A' }}</td>
                                    <td style="padding: 0.25rem 0.5rem; text-align: center; color: #475569;">{{ number_format($item->quantity, 1) }} {{ $item->foodItem?->unit ?? '' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endforeach
                </div>
                @else
                <div style="background: white; border-radius: 0.5rem; padding: 1rem; text-align: center;">
                    <p style="color: #92400e; margin: 0; font-size: 0.85rem;">This user has no approved distributions. Create a distribution first.</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($voucher->isIssued())
        <div style="border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
            <h4 style="font-size: 0.95rem; font-weight: 700; color: #1f2937; margin: 0 0 0.75rem 0;">Confirm Collection</h4>
            <form id="collectForm" method="POST" action="{{ route('admin.issuance.collect', $voucher) }}" style="display: flex; gap: 1rem; align-items: flex-end;">
                @csrf
                <div style="flex: 1;">
                    <input type="text" name="notes" placeholder="Optional notes..." class="form-input">
                </div>
                <button type="button" onclick="document.getElementById('collectModal').style.display='flex'" class="btn btn-primary" style="white-space: nowrap;">
                    Mark as Collected
                </button>
            </form>
        </div>
        @elseif($voucher->isCollected())
        <div class="alert alert-success" style="margin-bottom: 0;">
            <span style="font-size: 1.25rem;">✓</span>
            <div>
                <p style="margin: 0; font-weight: 600;">Already collected</p>
                <p style="margin: 0.15rem 0 0 0; font-size: 0.85rem; color: #64748b;">
                    @if($voucher->collector)
                    Collected by {{ $voucher->collector->name }} on {{ $voucher->collected_at->format('M d, Y g:i A') }}
                    @else
                    Collected on {{ $voucher->collected_at->format('M d, Y g:i A') }}
                    @endif
                </p>
            </div>
        </div>
        @elseif($voucher->isExpired())
        <div class="alert alert-error" style="margin-bottom: 0;">
            <span style="font-size: 1.25rem;">✕</span>
            <div>
                <p style="margin: 0; font-weight: 600;">Voucher expired</p>
                <p style="margin: 0.15rem 0 0 0; font-size: 0.85rem; color: #64748b;">This voucher expired on {{ $voucher->expires_at->format('M d, Y g:i A') }} and can no longer be collected.</p>
            </div>
        </div>
        @else
        <div class="alert alert-warning" style="margin-bottom: 0;">
            <span style="font-size: 1.25rem;">⏳</span>
            <div>
                <p style="margin: 0; font-weight: 600;">Pending issuance</p>
                <p style="margin: 0.15rem 0 0 0; font-size: 0.85rem; color: #64748b;">This voucher needs to be issued first before collection.</p>
            </div>
        </div>
        @endif
    </div>
</div>
@elseif(request('search'))
<div class="page-card">
    <div class="page-card-body" style="text-align: center; padding: 3rem;">
        <p style="color: #94a3b8;">Enter a voucher number or collection code above to look up a voucher.</p>
    </div>
</div>
@endif

<div id="collectModal" style="display: none; position: fixed; inset: 0; z-index: 9999; align-items: center; justify-content: center;" onclick="if(event.target===this) document.getElementById('collectModal').style.display='none'">
    <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);"></div>
    <div style="position: relative; background: white; border-radius: 24px; max-width: 440px; width: 90%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); overflow: hidden;">
        <div class="progress-bar" style="height: 6px; border-radius: 0;">
            <div class="progress-fill emerald" style="width: 100%; border-radius: 0;"></div>
        </div>
        <div style="padding: 2rem 1.75rem 1.5rem; text-align: center;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: #f0fdf4; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 32px; height: 32px; color: #16a34a;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0;">Confirm Collection?</h3>
            <p style="color: #64748b; margin: 0 0 1rem 0; font-size: 0.9rem;">
                Mark this voucher as collected? The member will receive all items listed in the distribution.
            </p>
            @if($voucher && $voucher->distribution)
            <div style="background: #f8fafc; border-radius: 12px; padding: 0.75rem 1rem; margin-bottom: 1.25rem; text-align: left;">
                <p style="font-size: 0.7rem; text-transform: uppercase; font-weight: 600; color: #94a3b8; margin: 0 0 0.25rem 0;">Items in this collection</p>
                @foreach($voucher->distribution->items as $item)
                <div style="display: flex; justify-content: space-between; padding: 0.2rem 0; font-size: 0.85rem;">
                    <span style="color: #1e293b;">{{ $item->foodItem?->name ?? 'N/A' }}</span>
                    <span style="color: #64748b;">{{ number_format($item->quantity, 1) }} {{ $item->foodItem?->unit ?? '' }}</span>
                </div>
                @endforeach
            </div>
            @endif
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" onclick="document.getElementById('collectModal').style.display='none'" class="btn btn-secondary" style="flex: 1;">
                    Cancel
                </button>
                <button type="button" onclick="document.getElementById('collectForm').submit()" class="btn btn-primary" style="flex: 1;">
                    Confirm & Collect
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
