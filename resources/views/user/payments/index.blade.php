{{-- resources/views/user/payments/index.blade.php --}}
@extends('layouts.user')

@section('title', 'Payment History')
@section('header', 'Payment History')
@section('subheader', 'View your past transactions')

@section('content')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if($payments->isEmpty())
<div class="page-card">
    <div class="empty-state">
        <div class="empty-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="empty-title">No payments yet</p>
        <p class="empty-text">All your future payments will appear here.</p>
        <a href="{{ route('payments.renew') }}" class="btn btn-primary" style="margin-top: 1rem;">
            Renew Membership
        </a>
    </div>
</div>
@else
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">All Payments</h3>
            <p class="page-card-subtitle">{{ $payments->total() }} total transactions</p>
        </div>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
                                @if(($payment->status ?? '') === 'successful' || $payment->status === 'completed') background: #dcfce7; @endif
                                @if(($payment->status ?? '') === 'pending') background: #fef3c7; @endif
                                @if(($payment->status ?? '') === 'failed') background: #fee2e2; @endif">
                                <svg style="width: 20px; height: 20px;
                                    @if(($payment->status ?? '') === 'successful' || $payment->status === 'completed') color: #16a34a; @endif
                                    @if(($payment->status ?? '') === 'pending') color: #d97706; @endif
                                    @if(($payment->status ?? '') === 'failed') color: #dc2626; @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span style="font-weight: 600; color: #1e293b;">{{ ucfirst(str_replace('_', ' ', $payment->type ?? 'payment')) }}</span>
                        </div>
                    </td>
                    <td style="font-weight: 600; color: #1e293b; font-size: 1rem;">{{ format_currency($payment->amount ?? 0) }}</td>
                    <td style="color: #64748b;">{{ $payment->created_at?->format('M d, Y h:i A') ?? 'N/A' }}</td>
                    <td>
                        <span class="badge 
                            @if(($payment->status ?? '') === 'successful' || $payment->status === 'completed') badge-success @endif
                            @if(($payment->status ?? '') === 'pending') badge-warning @endif
                            @if(($payment->status ?? '') === 'failed') badge-error @endif">
                            {{ ucfirst($payment->status ?? 'pending') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($payments->hasPages())
<div style="display: flex; justify-content: center; margin-top: 1.5rem;">
    {{ $payments->links() }}
</div>
@endif

@endif
@endsection