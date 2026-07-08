{{-- resources/views/user/distributions/index.blade.php --}}
@extends('layouts.user')

@section('title', 'Your Distributions')
@section('header', 'Distributions')
@section('subheader', 'View your earnings from referral rewards')

@section('content')

@if($distributions->isEmpty())
<div class="page-card">
    <div class="empty-state">
        <div class="empty-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
        </div>
        <p class="empty-title">No distributions yet</p>
        <p class="empty-text">Distributions are processed monthly based on your referral activity.</p>
        <a href="{{ route('referrals.tree') }}" class="btn btn-primary" style="margin-top: 1rem;">
            View Referral Tree
        </a>
    </div>
</div>
@else
<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">All Distributions</h3>
        <p class="page-card-subtitle">{{ $distributions->total() }} total packages</p>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Package</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th style="text-align: right;">Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($distributions as $distribution)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: #f3e8ff; display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 20px; height: 20px; color: #9333ea;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <div>
                                <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $distribution->distribution_number ?? 'N/A' }}</p>
                                <p style="font-size: 0.875rem; color: #64748b; margin: 0;">{{ $distribution->package?->name ?? 'Standard Package' }}</p>
                            </div>
                        </div>
                    </td>
                    <td style="color: #64748b;">{{ $distribution->created_at?->format('M d, Y') }}</td>
                    <td>
                        <span class="badge 
                            @if(($distribution->status ?? '') === 'delivered') badge-success @endif
                            @if(($distribution->status ?? '') === 'pending') badge-warning @endif
                            @if(($distribution->status ?? '') === 'processing') badge-info @endif
                            @if(($distribution->status ?? '') === 'cancelled') badge-error @endif">
                            {{ ucfirst($distribution->status ?? 'pending') }}
                        </span>
                    </td>
                    <td style="text-align: right; font-weight: 700; color: #1e293b;">
                        {{ format_currency($distribution->package_value ?? 0) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($distributions->hasPages())
<div style="display: flex; justify-content: center; margin-top: 1.5rem;">
    {{ $distributions->links() }}
</div>
@endif

@endif
@endsection