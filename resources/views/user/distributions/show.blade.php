{{-- resources/views/user/distributions/show.blade.php --}}
@extends('layouts.user')
@section('title', 'Distribution Details')
@section('header', 'Distribution Details')
@section('subheader', 'View distribution details')

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <a href="{{ route('distributions.index') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Distributions
            </a>
            <h3 class="page-card-title" style="margin-top: 0.5rem;">{{ $distribution->type_label }}</h3>
            <p class="page-card-subtitle">{{ $distribution->period_label }}</p>
        </div>
        <span class="badge {{ $distribution->status === 'paid' ? 'badge-success' : ($distribution->status === 'pending' ? 'badge-warning' : 'badge-info') }}">
            {{ ucfirst($distribution->status) }}
        </span>
    </div>
    <div class="page-card-body">
        <div style="text-align: center; padding: 2rem 0; border-bottom: 1px solid #f1f5f9;">
            <p style="color: #64748b; font-size: 0.875rem; margin: 0;">Total Amount</p>
            <p style="font-size: 2.5rem; font-weight: 700; color: #1e293b; margin: 0.5rem 0;">{{ $distribution->formatted_amount }}</p>
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.5rem;">Processed on {{ $distribution->created_at->format('F j, Y') }}</p>
        </div>

        <div style="padding: 1.5rem 0;">
            <h3 style="font-weight: 600; color: #1e293b; margin: 0 0 1rem 0;">Earnings Breakdown</h3>

            @if($distribution->breakdown->isEmpty())
                <p style="color: #94a3b8; font-size: 0.875rem;">No detailed breakdown available.</p>
            @else
                <div style="display: grid; gap: 0.75rem;">
                    @foreach($distribution->breakdown as $item)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                            <div>
                                <p style="font-weight: 500; color: #1e293b; margin: 0; font-size: 0.9rem;">{{ $item['description'] }}</p>
                                <p style="color: #94a3b8; font-size: 0.75rem; margin: 0.25rem 0 0 0;">{{ $item['referral_name'] ?? 'System' }}</p>
                            </div>
                            <p style="font-weight: 600; color: #10b981; font-size: 0.9rem;">+{{ $item['amount'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($distribution->status === 'paid' && $distribution->payout_method)
                <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                    <h4 style="font-weight: 500; color: #475569; font-size: 0.875rem; margin: 0 0 0.5rem 0;">Payout Method</h4>
                    <p style="color: #64748b; font-size: 0.875rem; margin: 0;">
                        @if($distribution->payout_method === 'bank')
                            Bank Transfer (•••• {{ $user->bank_last_four }})
                        @elseif($distribution->payout_method === 'wallet')
                            Platform Wallet
                        @else
                            {{ ucfirst($distribution->payout_method) }}
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection