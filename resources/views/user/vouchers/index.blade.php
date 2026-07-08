@extends('layouts.user')

@section('title', 'My Vouchers')
@section('header', 'My Vouchers')
@section('subheader', 'View and acknowledge your distribution vouchers')

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

@if($vouchers->isEmpty())
<div class="page-card">
    <div class="empty-state">
        <div class="empty-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
        </div>
        <p class="empty-title">No vouchers yet</p>
        <p class="empty-text">Vouchers will appear here once your clearance requests are approved and distributions are created.</p>
    </div>
</div>
@else
<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">All Vouchers</h3>
        <p class="page-card-subtitle">{{ $vouchers->total() }} total vouchers</p>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Voucher</th>
                    <th>Distribution</th>
                    <th>Date Issued</th>
                    <th>Status</th>
                    <th>Acknowledged</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vouchers as $voucher)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: #fef3c7; display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 20px; height: 20px; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                            </div>
                            <div>
                                <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $voucher->voucher_number }}</p>
                                <p style="font-size: 0.875rem; color: #64748b; margin: 0;">Code: {{ $voucher->collection_code }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($voucher->distribution)
                        <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $voucher->distribution->distribution_number ?? 'Distribution' }}</p>
                        <p style="font-size: 0.875rem; color: #64748b; margin: 0;">{{ $voucher->distribution->package?->name ?? $voucher->distribution->tier?->name ?? 'Standard' }}</p>
                        @else
                        <span style="color: #94a3b8;">Not linked</span>
                        @endif
                    </td>
                    <td style="color: #64748b;">{{ $voucher->issued_at?->format('M d, Y') ?? $voucher->created_at->format('M d, Y') }}</td>
                    <td>
                        <span class="badge
                            @if($voucher->status === 'issued') badge-info @endif
                            @if($voucher->status === 'collected') badge-success @endif
                            @if($voucher->status === 'expired') badge-error @endif
                            @if($voucher->status === 'pending') badge-warning @endif">
                            {{ ucfirst($voucher->status) }}
                        </span>
                    </td>
                    <td>
                        @if($voucher->isAcknowledged())
                        <span class="badge badge-success">Acknowledged</span>
                        @elseif($voucher->isIssued())
                        <span class="badge badge-warning">Pending</span>
                        @else
                        <span style="color: #94a3b8;">N/A</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('vouchers.show', $voucher) }}" style="color: #10b981; font-weight: 600; font-size: 0.875rem; text-decoration: none; margin-right: 0.75rem;">View</a>
                        @if($voucher->isIssued() && !$voucher->isAcknowledged())
                        <form method="POST" action="{{ route('vouchers.acknowledge', $voucher) }}" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: #10b981; color: white; border: none; padding: 6px 16px; border-radius: 8px; font-weight: 600; font-size: 0.75rem; cursor: pointer;">
                                Acknowledge
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($vouchers->hasPages())
<div style="display: flex; justify-content: center; margin-top: 1.5rem;">
    {{ $vouchers->links() }}
</div>
@endif

@endif
@endsection
