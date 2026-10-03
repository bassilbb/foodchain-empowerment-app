@extends('layouts.user')
@section('title', 'Quarterly Benefits')
@section('header', 'Quarterly Benefits')
@section('subheader', 'Claim your free food packs based on community size')

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; flex-shrink: 0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; flex-shrink: 0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        {{ session('error') }}
    </div>
@endif

@if($currentQuarter)
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Current Quarter ({{ $currentQuarter->quarter }}Q {{ $currentQuarter->year }})</h3>
            <p class="page-card-subtitle">Your eligibility status for this quarter</p>
        </div>
        <span class="badge {{ $currentQuarter->is_qualified ? 'badge-success' : 'badge-warning' }}">
            @if($currentQuarter->is_qualified)
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; margin-right: 4px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Qualified
            @else
                Not Qualified
            @endif
        </span>
    </div>
    <div class="page-card-body">
        @if($currentQuarter->is_qualified)
            <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 16px; padding: 1.5rem; color: white;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 64px; height: 64px; border-radius: 16px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 32px; height: 32px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div>
                            <p style="font-weight: 700; font-size: 1.1rem; margin: 0 0 0.25rem 0;">You're eligible for a free food pack!</p>
                            <p style="opacity: 0.9; margin: 0; font-size: 0.9rem;">
                                Pack Type: <strong style="font-weight: 700;">{{ ucfirst($currentQuarter->food_pack_type) }}</strong>
                                @if($currentQuarter->food_pack_type === 'mini')
                                    (₦5,000 value)
                                @elseif($currentQuarter->food_pack_type === 'average')
                                    (₦10,000 value)
                                @elseif($currentQuarter->food_pack_type === 'standard')
                                    (₦25,000 value)
                                @elseif($currentQuarter->food_pack_type === 'premium')
                                    (₦50,000 value)
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($currentQuarter->claim_date)
                        <span style="padding: 0.75rem 1.5rem; background: rgba(255,255,255,0.2); border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Claimed
                        </span>
                    @else
                        <form action="{{ route('quarterly-benefits.claim', $currentQuarter->id) }}" method="POST">
                            @csrf
                            <button type="submit" style="padding: 0.75rem 1.5rem; background: white; color: #10b981; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: all 0.3s;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                Claim Now
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @else
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #e2e8f0; display: flex; align-items: center; justify-content: center;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px; color: #64748b;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p style="font-weight: 600; color: #475569; margin: 0 0 0.25rem 0;">Build your community to qualify for free quarterly food packs!</p>
                        <p style="color: #64748b; font-size: 0.875rem; margin: 0;">Refer more members to increase your eligibility</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endif

<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Benefit History</h3>
            <p class="page-card-subtitle">Your past quarterly benefits and claims</p>
        </div>
    </div>
    <div class="page-card-body" style="padding: 0;">
        @if($benefits->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <h3 class="empty-title">No quarterly benefits yet</h3>
                <p class="empty-text">Your benefit history will appear here</p>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Quarter</th>
                        <th>Pack Type</th>
                        <th>Status</th>
                        <th>Claim Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($benefits as $benefit)
                    <tr>
                        <td style="font-weight: 600;">Q{{ $benefit->quarter }} {{ $benefit->year }}</td>
                        <td style="text-transform: capitalize;">{{ $benefit->is_qualified ? ucfirst($benefit->food_pack_type) : '-' }}</td>
                        <td>
                            @if($benefit->claim_date)
                                <span class="badge badge-success">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 12px; height: 12px; margin-right: 4px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Claimed
                                </span>
                            @elseif($benefit->is_qualified)
                                <span class="badge badge-warning">Available</span>
                            @else
                                <span style="color: #94a3b8; font-size: 0.75rem;">Not qualified</span>
                            @endif
                        </td>
                        <td style="color: #64748b;">{{ $benefit->claim_date ? $benefit->claim_date->format('M d, Y') : '-' }}</td>
                        <td>
                            <a href="{{ route('quarterly-benefits.show', $benefit->id) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection