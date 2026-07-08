@extends('layouts.user')
@section('title', 'Manual Payments')
@section('header', 'Manual Payments')
@section('subheader', 'View your payment history')

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Your Manual Payments</h3>
            <p class="page-card-subtitle">Track your payment submissions</p>
        </div>
        <a href="{{ route('manual-payments.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Make Payment
        </a>
    </div>
    <div class="page-card-body" style="padding: 0;">
        @if($payments->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="empty-title">No payments yet</h3>
                <p class="empty-text">You haven't made any manual payments yet.</p>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td style="font-family: monospace; font-weight: 600; color: #1e293b;">{{ $payment->reference }}</td>
                        <td style="font-weight: 600; color: #10b981;">{{ format_currency($payment->amount) }}</td>
                        <td style="text-transform: capitalize; color: #475569;">{{ str_replace('_', ' ', $payment->type) }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'pending' => 'badge-warning',
                                    'verified' => 'badge-success',
                                    'rejected' => 'badge-error',
                                ];
                            @endphp
                            <span class="badge {{ $statusColors[$payment->status] ?? 'badge-info' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td style="color: #64748b;">{{ $payment->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection