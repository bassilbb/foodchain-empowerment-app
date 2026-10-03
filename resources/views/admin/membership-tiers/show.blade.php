@extends('admin.layouts.app')

@section('title', $membershipTier->name)
@section('page-title', $membershipTier->name . ' Tier Details')

@section('header', $membershipTier->name)
@section('subheader', 'Membership tier overview and statistics')

@section('content')
<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
            <div style="padding: 0.5rem; border-radius: 12px; background: #d1fae5;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #059669;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <span style="font-size: 0.875rem; font-weight: 500; color: #64748b;">Total Members</span>
        </div>
        <p style="font-size: 2rem; font-weight: 700; color: #1e293b;">{{ number_format($stats['total_members']) }}</p>
    </div>
    <div class="stat-card accent">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
            <div style="padding: 0.5rem; border-radius: 12px; background: #dcfce7;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #16a34a;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span style="font-size: 0.875rem; font-weight: 500; color: #64748b;">Active Members</span>
        </div>
        <p style="font-size: 2rem; font-weight: 700; color: #1e293b;">{{ number_format($stats['active_members']) }}</p>
    </div>
    <div class="stat-card warning">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
            <div style="padding: 0.5rem; border-radius: 12px; background: #f3e8ff;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #9333ea;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <span style="font-size: 0.875rem; font-weight: 500; color: #64748b;">Total Unions</span>
        </div>
        <p style="font-size: 2rem; font-weight: 700; color: #1e293b;">{{ number_format($stats['total_unions']) }}</p>
    </div>
    <div class="stat-card info">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
            <div style="padding: 0.5rem; border-radius: 12px; background: #fef3c7;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #d97706;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span style="font-size: 0.875rem; font-weight: 500; color: #64748b;">Active Loans</span>
        </div>
        <p style="font-size: 2rem; font-weight: 700; color: #1e293b;">{{ number_format($stats['active_loans']) }}</p>
    </div>
</div>

<div class="grid-2">
    <!-- Tier Details -->
    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Tier Details</h3>
            <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; {{ $membershipTier->is_active ? 'background: #dcfce7; color: #16a34a;' : 'background: #f1f5f9; color: #64748b;' }}">
                {{ $membershipTier->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        <div class="page-card-body">
            <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Annual Fee</span>
                <span style="font-weight: 600; color: #1e293b;">{{ format_currency($membershipTier->annual_fee) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Min. Referrals</span>
                <span style="font-weight: 600; color: #1e293b;">{{ $membershipTier->min_referrals }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Free Food Pack</span>
                <span style="font-weight: 600; color: #1e293b;">{{ ucfirst($membershipTier->free_food_pack_type) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Community Size</span>
                <span style="font-weight: 600; color: #1e293b;">{{ $membershipTier->community_min_members }} - {{ $membershipTier->community_max_members }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Delivery</span>
                <span style="font-weight: 600; color: #1e293b;">
                    @if($membershipTier->free_delivery)
                        Free
                    @elseif($membershipTier->has_delivery)
                        {{ $membershipTier->delivery_subsidy_percentage }}% subsidized
                    @else
                        Not available
                    @endif
                </span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 0.75rem 0;">
                <span style="color: #64748b;">Union Equity</span>
                <span style="font-weight: 600; color: #1e293b;">{{ $membershipTier->union_equity_percentage }}%</span>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem; padding: 1.5rem; padding-top: 0;">
            <a href="{{ route('admin.membership-tiers.edit', $membershipTier) }}" style="flex: 1; padding: 0.5rem 1rem; text-align: center; background: #ecfdf5; color: #10b981; border-radius: 12px; font-weight: 500; text-decoration: none;">
                Edit Tier
            </a>
            <form method="POST" action="{{ route('admin.membership-tiers.toggle-status', $membershipTier) }}">
                @csrf
                <button type="submit" style="padding: 0.5rem 1rem; background: #f1f5f9; color: #64748b; border-radius: 12px; font-weight: 500; border: none; cursor: pointer;">
                    {{ $membershipTier->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Quick Actions</h3>
        </div>
        <div class="page-card-body" style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="{{ route('admin.membership-tiers.members', $membershipTier) }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f8fafc; border-radius: 12px; text-decoration: none; transition: all 0.2s;">
                <div style="padding: 0.5rem; border-radius: 8px; background: #d1fae5;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #059669;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div style="flex: 1;">
                    <p style="font-weight: 500; color: #1e293b; margin: 0;">View Members</p>
                    <p style="font-size: 0.875rem; color: #64748b; margin: 0;">See all members in this tier</p>
                </div>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #94a3b8;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
            <a href="{{ route('admin.membership-tiers.unions', $membershipTier) }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f8fafc; border-radius: 12px; text-decoration: none; transition: all 0.2s;">
                <div style="padding: 0.5rem; border-radius: 8px; background: #f3e8ff;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #9333ea;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div style="flex: 1;">
                    <p style="font-weight: 500; color: #1e293b; margin: 0;">View Unions</p>
                    <p style="font-size: 0.875rem; color: #64748b; margin: 0;">See all unions in this tier</p>
                </div>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #94a3b8;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
    </div>
</div>
@endsection
