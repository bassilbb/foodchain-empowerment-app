@extends('layouts.user')
@section('title', 'Quarterly Benefit Details')
@section('header', 'Quarterly Benefit Details')
@section('subheader', 'View benefit information')

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <a href="{{ route('quarterly-benefits.index') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Quarterly Benefits
            </a>
            <h3 class="page-card-title" style="margin-top: 0.5rem;">Q{{ $benefit->quarter }} {{ $benefit->year }}</h3>
            <p class="page-card-subtitle">Quarterly Benefit Details</p>
        </div>
        <span class="badge {{ $benefit->is_qualified ? 'badge-success' : 'badge-warning' }}">
            {{ $benefit->is_qualified ? 'Qualified' : 'Not Qualified' }}
        </span>
    </div>
    <div class="page-card-body">
        <div style="background: #f8fafc; border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem;">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                <div>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 0.25rem 0;">Membership Tier</p>
                    <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $benefit->tier->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 0.25rem 0;">Qualification Type</p>
                    <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ ucfirst(str_replace('_', ' ', $benefit->qualification_type)) }}</p>
                </div>
                <div>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 0.25rem 0;">Community Count</p>
                    <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $benefit->community_count_at_qualification ?? 0 }}</p>
                </div>
                <div>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 0.25rem 0;">Food Pack Type</p>
                    <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $benefit->food_pack_type ? ucfirst($benefit->food_pack_type) : 'N/A' }}</p>
                </div>
            </div>
        </div>

        @if($benefit->qualification_date)
        <div style="background: #f8fafc; border-radius: 12px; padding: 1rem; margin-bottom: 1rem;">
            <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 0.25rem 0;">Qualification Date</p>
            <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $benefit->qualification_date->format('M d, Y') }}</p>
        </div>
        @endif

        @if($benefit->claim_date)
        <div style="background: #dcfce7; border: 1px solid #bbf7d0; border-radius: 12px; padding: 1rem; display: flex; align-items: center; gap: 0.75rem;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px; color: #16a34a;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span style="font-weight: 600; color: #166534;">Claimed on {{ $benefit->claim_date->format('M d, Y') }}</span>
        </div>
        @elseif($benefit->is_qualified)
        <div style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 12px; padding: 1.25rem;">
            <p style="color: #92400e; font-weight: 500; margin: 0 0 1rem 0;">This benefit is available to claim!</p>
            <form action="{{ route('quarterly-benefits.claim', $benefit->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    Claim Benefit
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection