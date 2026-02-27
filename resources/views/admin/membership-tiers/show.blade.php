@extends('admin.layouts.app')

@section('title', $membershipTier->name)
@section('page-title', $membershipTier->name . ' Tier Details')

@section('header', $membershipTier->name)
@section('subheader', 'Membership tier overview and statistics')

@section('content')
<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="stat-card group">
        <div class="flex items-center gap-2 mb-3">
            <div class="p-2 rounded-xl bg-primary-100">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <span class="text-sm font-medium text-slate-500">Total Members</span>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ number_format($stats['total_members']) }}</p>
    </div>
    <div class="stat-card accent group">
        <div class="flex items-center gap-2 mb-3">
            <div class="p-2 rounded-xl bg-green-100">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="text-sm font-medium text-slate-500">Active Members</span>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ number_format($stats['active_members']) }}</p>
    </div>
    <div class="stat-card warning group">
        <div class="flex items-center gap-2 mb-3">
            <div class="p-2 rounded-xl bg-purple-100">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <span class="text-sm font-medium text-slate-500">Total Unions</span>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ number_format($stats['total_unions']) }}</p>
    </div>
    <div class="stat-card info group">
        <div class="flex items-center gap-2 mb-3">
            <div class="p-2 rounded-xl bg-amber-100">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="text-sm font-medium text-slate-500">Active Loans</span>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ number_format($stats['active_loans']) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Tier Details -->
    <div class="card-3d p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-800">Tier Details</h3>
            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $membershipTier->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                {{ $membershipTier->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        <div class="space-y-4">
            <div class="flex justify-between py-3 border-b border-slate-100">
                <span class="text-slate-500">Annual Fee</span>
                <span class="font-semibold text-slate-800">{{ format_currency($membershipTier->annual_fee) }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-slate-100">
                <span class="text-slate-500">Min. Referrals</span>
                <span class="font-semibold text-slate-800">{{ $membershipTier->min_referrals }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-slate-100">
                <span class="text-slate-500">Free Food Pack</span>
                <span class="font-semibold text-slate-800">{{ ucfirst($membershipTier->free_food_pack_type) }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-slate-100">
                <span class="text-slate-500">Community Size</span>
                <span class="font-semibold text-slate-800">{{ $membershipTier->community_min_members }} - {{ $membershipTier->community_max_members }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-slate-100">
                <span class="text-slate-500">Delivery</span>
                <span class="font-semibold text-slate-800">
                    @if($membershipTier->free_delivery)
                        Free
                    @elseif($membershipTier->has_delivery)
                        {{ $membershipTier->delivery_subsidy_percentage }}% subsidized
                    @else
                        Not available
                    @endif
                </span>
            </div>
            <div class="flex justify-between py-3">
                <span class="text-slate-500">Union Equity</span>
                <span class="font-semibold text-slate-800">{{ $membershipTier->union_equity_percentage }}%</span>
            </div>
        </div>
        <div class="flex gap-3 mt-6">
            <a href="{{ route('admin.membership-tiers.edit', $membershipTier) }}" class="flex-1 px-4 py-2 text-center bg-primary-50 text-primary-600 rounded-xl font-medium hover:bg-primary-100 transition-colors">
                Edit Tier
            </a>
            <form method="POST" action="{{ route('admin.membership-tiers.toggle-status', $membershipTier) }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-medium hover:bg-slate-200 transition-colors">
                    {{ $membershipTier->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card-3d p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-6">Quick Actions</h3>
        <div class="space-y-3">
            <a href="{{ route('admin.membership-tiers.members', $membershipTier) }}" class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                <div class="p-2 rounded-lg bg-primary-100">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div class="flex-1">
                    <p class="font-medium text-slate-800">View Members</p>
                    <p class="text-sm text-slate-500">See all members in this tier</p>
                </div>
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
            <a href="{{ route('admin.membership-tiers.unions', $membershipTier) }}" class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                <div class="p-2 rounded-lg bg-purple-100">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div class="flex-1">
                    <p class="font-medium text-slate-800">View Unions</p>
                    <p class="text-sm text-slate-500">See all unions in this tier</p>
                </div>
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
    </div>
</div>
@endsection
