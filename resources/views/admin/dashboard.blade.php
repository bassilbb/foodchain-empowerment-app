@php $adminRole = auth()->guard('admin')->user(); @endphp

@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard Overview')
@section('subheader', "Here's what's happening with your FoodChain platform today.")

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1>Welcome back, {{ $adminRole->name ?? 'Admin' }}!</h1>
        <p>Here's what's happening with your FoodChain platform today.</p>
    </div>
    <div class="header-actions-btns">
        @if($adminRole->isSuperAdmin() || $adminRole->isInventoryManager())
        <a href="{{ route('admin.distributions.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Distribution
        </a>
        @endif
        @if($adminRole->isCustomerSupport())
        <a href="{{ route('admin.support.index') }}" class="btn btn-secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            View Tickets
        </a>
        @endif
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <p class="stat-label">Total Users</p>
        <p class="stat-value">{{ number_format($totalUsers) }}</p>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="stat-badge success">{{ number_format($activeUsers) }} active</span>
            <span style="font-size: 0.75rem; color: #94a3b8;">+{{ $newUsersThisMonth }} this month</span>
        </div>
    </div>

    @if($adminRole->isSuperAdmin())
    <div class="stat-card accent">
        <div class="stat-icon sky">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Total Revenue</p>
        <p class="stat-value">{{ format_currency($totalRevenue) }}</p>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="stat-badge info">{{ format_currency($revenueThisMonth) }} this month</span>
            <span style="font-size: 0.75rem; color: #94a3b8;">{{ format_currency($revenueToday) }} today</span>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-icon teal">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Clearance Revenue</p>
        <p class="stat-value">{{ format_currency($clearanceRevenue) }}</p>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="stat-badge info">{{ $clearanceRequestsThisMonth }} this month</span>
            <span style="font-size: 0.75rem; color: #94a3b8;">{{ $pendingClearances }} pending</span>
        </div>
    </div>
    @endif

    @if($adminRole->isSuperAdmin() || $adminRole->isInventoryManager())
    <div class="stat-card warning">
        <div class="stat-icon amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
        </div>
        <p class="stat-label">Distributions</p>
        <p class="stat-value">{{ number_format($distributionsThisMonth) }}</p>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="stat-badge warning">{{ $pendingDistributions }} pending</span>
            <a href="{{ route('admin.distributions.index', ['status' => 'pending']) }}" class="action-link">View →</a>
        </div>
    </div>
    @endif

    @if($adminRole->isSuperAdmin() || $adminRole->isCustomerSupport())
    <div class="stat-card info">
        <div class="stat-icon pink">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <p class="stat-label">Open Tickets</p>
        <p class="stat-value">{{ number_format($openTickets) }}</p>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="stat-badge" style="background: #f1f5f9; color: #475569;">Support requests</span>
            <a href="{{ route('admin.support.index') }}" class="action-link">View →</a>
        </div>
    </div>

    <div class="stat-card danger">
        <div class="stat-icon red">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <p class="stat-label">Open Incidents</p>
        <p class="stat-value">{{ number_format($openIncidents) }}</p>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="stat-badge" style="background: #fee2e2; color: #dc2626;">{{ $criticalIncidents }} critical</span>
            <a href="{{ route('admin.incidents.index') }}" class="action-link">View →</a>
        </div>
    </div>
    @endif

    @if($adminRole->isSuperAdmin())
    <div class="stat-card info">
        <div class="stat-icon purple">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
        <p class="stat-label">Pending Verifications</p>
        <p class="stat-value">{{ number_format($pendingManualPayments) }}</p>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="stat-badge info">{{ format_currency($pendingManualAmount) }} pending</span>
            <a href="{{ route('admin.manual-payments.index') }}" class="action-link">View →</a>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-icon orange">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
        </div>
        <p class="stat-label">Pending Vouchers</p>
        <p class="stat-value">{{ number_format($pendingVouchers) }}</p>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="stat-badge warning">{{ $issuedVouchers }} issued</span>
            <a href="{{ route('admin.vouchers.index') }}" class="action-link">View →</a>
        </div>
    </div>

    <div class="stat-card accent">
        <div class="stat-icon indigo">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
        </div>
        <p class="stat-label">Pending Starter Packs</p>
        <p class="stat-value">{{ number_format($pendingStarterPacks) }}</p>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="stat-badge info">{{ $starterPacksThisMonth }} this month</span>
            <a href="{{ route('admin.issuance.index') }}" class="action-link">View →</a>
        </div>
    </div>

    <div class="stat-card success-card">
        <div class="stat-icon cyan">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <p class="stat-label">Active Unions</p>
        <p class="stat-value">{{ number_format($activeUnions) }}</p>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="stat-badge success">{{ $totalUnionMembers }} members</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon rose">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Active Food Loans</p>
        <p class="stat-value">{{ number_format($activeFoodLoans) }}</p>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="stat-badge success">{{ format_currency($totalDisbursed) }} disbursed</span>
            <a href="{{ route('admin.food-loans.index') }}" class="action-link">View →</a>
        </div>
    </div>
    @endif
</div>

<!-- Low Stock Alert -->
@if(count($lowStockItems) > 0)
<div class="alert-warning">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
    <p>
        <strong style="color: #a16207;">{{ count($lowStockItems) }} items</strong> are running low on stock.
        <a href="{{ route('admin.inventory.low-stock') }}" class="action-link">View details →</a>
    </p>
</div>
@endif

<!-- Charts Row -->
<div class="grid-2" style="margin-bottom: 2rem;">
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Users by Tier</h3>
                <p class="page-card-subtitle">Membership distribution</p>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #d1fae5; display: flex; align-items: center; justify-content: center;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #059669;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
        <div class="page-card-body">
            @php
                $tierColors = [
                    'bronze' => 'amber',
                    'silver' => 'slate',
                    'gold' => 'amber',
                    'emerald' => 'emerald',
                ];
            @endphp
            @foreach($usersByTier as $tier)
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span style="font-weight: 600; color: #475569; font-size: 0.9rem;">{{ $tier['tier'] }}</span>
                    <span style="font-weight: 700; color: #1e293b;">{{ number_format($tier['count']) }}</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill {{ $tierColors[strtolower($tier['tier'])] ?? 'emerald' }}" style="width: {{ $totalUsers > 0 ? ($tier['count'] / $totalUsers) * 100 : 0 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @if($adminRole->isSuperAdmin())
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Revenue Trend</h3>
                <p class="page-card-subtitle">Last 6 months</p>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #dbeafe; display: flex; align-items: center; justify-content: center;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #2563eb;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
        </div>
        <div class="page-card-body">
            <canvas id="revenueChart" style="width: 100%; height: 256px;"></canvas>
        </div>
    </div>
    @endif
</div>

<!-- Recent Activity -->
<div class="grid-2">
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Recent Registrations</h3>
                <p class="page-card-subtitle">New users this week</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="action-link">View all →</a>
        </div>
        <div style="padding: 0;">
            @foreach($recentUsers as $user)
            <div class="list-item">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div class="user-avatar">{{ substr($user->full_name ?? 'U', 0, 1) }}</div>
                    <div>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $user->full_name ?? 'Unknown' }}</p>
                        <p style="color: #64748b; font-size: 0.875rem; margin: 0;">{{ $user->email }}</p>
                        <span class="badge {{ $user->currentTier?->slug ?? 'bronze' }}">{{ $user->currentTier?->name ?? 'Bronze' }}</span>
                    </div>
                </div>
                <div style="text-align: right;">
                    <p style="color: #64748b; font-size: 0.875rem; margin: 0;">{{ $user->created_at->format('M d, Y') }}</p>
                    <a href="{{ route('admin.users.show', ['user' => $user->id]) }}" class="action-link">View →</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @if($adminRole->isSuperAdmin())
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Recent Payments</h3>
                <p class="page-card-subtitle">Latest transactions</p>
            </div>
            <a href="{{ route('admin.payments.index') }}" class="action-link">View all →</a>
        </div>
        <div style="padding: 0;">
            @foreach($recentPayments as $payment)
            <div class="list-item">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
                        @if($payment->status === 'successful') background: #d1fae5; @endif
                        @if($payment->status === 'pending') background: #fef3c7; @endif
                        @if($payment->status === 'failed') background: #fee2e2; @endif">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;
                            @if($payment->status === 'successful') color: #059669; @endif
                            @if($payment->status === 'pending') color: #d97706; @endif
                            @if($payment->status === 'failed') color: #dc2626; @endif">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $payment->user?->full_name ?? 'Unknown User' }}</p>
                        <p style="color: #64748b; font-size: 0.875rem; margin: 0; text-transform: capitalize;">{{ str_replace('_', ' ', $payment->type) }}</p>
                        <span class="badge
                            @if($payment->status === 'successful') badge-success @endif
                            @if($payment->status === 'pending') badge-warning @endif
                            @if($payment->status === 'failed') badge-error @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                </div>
                <div style="text-align: right;">
                    <p style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0;">{{ format_currency($payment->amount) }}</p>
                    <p style="color: #94a3b8; font-size: 0.75rem; margin: 0;">{{ $payment->created_at->format('M d, h:i A') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Quick Actions -->
<div style="margin-top: 2rem;">
    <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">Quick Actions</h3>
    <div class="quick-actions-grid">
        <a href="{{ route('admin.users.index') }}" class="quick-action">
            <div class="quick-action-icon blue">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            </div>
            <span class="quick-action-label">Add User</span>
        </a>
        @if($adminRole->isSuperAdmin())
        <a href="{{ route('admin.payments.index') }}" class="quick-action">
            <div class="quick-action-icon green">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="quick-action-label">View Payments</span>
        </a>
        <a href="{{ route('admin.manual-payments.index') }}" class="quick-action">
            <div class="quick-action-icon yellow">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <span class="quick-action-label">Verify Payments</span>
        </a>
        @endif
        @if($adminRole->isSuperAdmin() || $adminRole->isInventoryManager())
        <a href="{{ route('admin.distributions.index') }}" class="quick-action">
            <div class="quick-action-icon purple">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            </div>
            <span class="quick-action-label">Distributions</span>
        </a>
        <a href="{{ route('admin.inventory.index') }}" class="quick-action">
            <div class="quick-action-icon orange">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <span class="quick-action-label">Inventory</span>
        </a>
        @endif
        @if($adminRole->isSuperAdmin() || $adminRole->isCustomerSupport())
        <a href="{{ route('admin.support.index') }}" class="quick-action">
            <div class="quick-action-icon pink">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <span class="quick-action-label">Support</span>
        </a>
        @endif
    </div>
</div>

<!-- Cron Job Status -->
<div style="margin-top: 2rem;">
    <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">
        <svg style="width: 20px; height: 20px; vertical-align: middle; margin-right: 6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Cron Job Status
        <span style="font-size: 12px; font-weight: 400; color: #94a3b8; margin-left: 8px;">Last 48h = OK, Older = Stale</span>
    </h3>
    <div class="stat-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
                        <th style="padding: 14px 20px; text-align: left; color: #64748b; font-weight: 600;">Task</th>
                        <th style="padding: 14px 20px; text-align: left; color: #64748b; font-weight: 600;">Command</th>
                        <th style="padding: 14px 20px; text-align: left; color: #64748b; font-weight: 600;">Last Run</th>
                        <th style="padding: 14px 20px; text-align: left; color: #64748b; font-weight: 600;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cronSummary as $cron)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 500;">{{ $cron['label'] }}</td>
                        <td style="padding: 14px 20px; color: #64748b; font-family: monospace; font-size: 13px;">{{ $cron['command'] }}</td>
                        <td style="padding: 14px 20px; color: #64748b;">
                            @if($cron['last_run'])
                                {{ \Carbon\Carbon::parse($cron['last_run'])->diffForHumans() }}
                            @else
                                <span style="color: #94a3b8;">Never</span>
                            @endif
                        </td>
                        <td style="padding: 14px 20px;">
                            @if($cron['status'] === 'ok')
                                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #d1fae5; color: #065f46;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981;"></span>
                                    OK
                                </span>
                            @elseif($cron['status'] === 'stale')
                                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #fef3c7; color: #92400e;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #f59e0b;"></span>
                                    Stale
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #fee2e2; color: #991b1b;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #ef4444;"></span>
                                    Never Run
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <p style="font-size: 12px; color: #94a3b8; margin-top: 8px;">
        To manually test: <code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">php artisan accounts:process-weekly-deductions</code>
    </p>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');

const gradient = ctx.createLinearGradient(0, 0, 0, 256);
gradient.addColorStop(0, 'rgba(14, 165, 233, 0.3)');
gradient.addColorStop(1, 'rgba(14, 165, 233, 0)');

const revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($monthlyRevenue->pluck('month')),
        datasets: [{
            label: 'Revenue',
            data: @json($monthlyRevenue->pluck('total')),
            borderColor: '#0ea5e9',
            backgroundColor: gradient,
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#0ea5e9',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                titleColor: '#fff',
                bodyColor: '#cbd5e1',
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return '₦' + context.parsed.y.toLocaleString();
                    }
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { color: '#64748b', font: { family: 'Inter', size: 12 } },
                border: { display: false }
            },
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(148, 163, 184, 0.1)' },
                ticks: { color: '#64748b', font: { family: 'Inter', size: 12 }, callback: function(value) { return '₦' + (value / 1000) + 'k'; } },
                border: { display: false }
            }
        },
        interaction: { intersect: false, mode: 'index' },
    }
});
</script>
@endpush