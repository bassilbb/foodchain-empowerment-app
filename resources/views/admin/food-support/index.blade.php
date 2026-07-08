@extends('admin.layouts.app')
@section('title', 'Food Support Plans')
@section('header', 'Food Support Plans')
@section('subheader', 'Manage and track savings plans')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="stats-grid">
    <div class="stat-card accent"><div class="stat-icon sky"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg></div><div class="stat-label">Active Plans</div><div class="stat-value">{{ $stats['active'] }}</div></div>
    <div class="stat-card info"><div class="stat-icon purple"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div><div class="stat-label">Fully Funded</div><div class="stat-value">{{ $stats['fully_funded'] }}</div></div>
    <div class="stat-card success-card"><div class="stat-icon emerald"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div><div class="stat-label">Matured</div><div class="stat-value">{{ $stats['matured'] }}</div></div>
    <div class="stat-card danger"><div class="stat-icon rose"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg></div><div class="stat-label">Withdrawn</div><div class="stat-value">{{ $stats['withdrawn'] }}</div></div>
</div>

<div style="margin-bottom: 1.5rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
    <div class="page-card" style="margin-bottom: 0;">
        <div class="page-card-body" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div class="stat-label">Total Contributions</div>
                <div class="stat-value" style="font-size: 1.5rem;">₦{{ number_format($stats['total_contributions'], 2) }}</div>
            </div>
            <div class="stat-icon sky"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
        </div>
    </div>
    <div class="page-card" style="margin-bottom: 0;">
        <div class="page-card-body" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div class="stat-label">Total Payout Value</div>
                <div class="stat-value" style="font-size: 1.5rem;">₦{{ number_format($stats['total_payout'], 2) }}</div>
            </div>
            <div class="stat-icon emerald"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg></div>
        </div>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">All Plans</h3>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.food-support.index') }}" class="btn btn-secondary" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;">All</a>
            <a href="{{ route('admin.food-support.index', ['status' => 'active']) }}" class="btn {{ request('status') == 'active' ? 'btn-primary' : 'btn-secondary' }}" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;">Active</a>
            <a href="{{ route('admin.food-support.index', ['status' => 'matured']) }}" class="btn {{ request('status') == 'matured' ? 'btn-primary' : 'btn-secondary' }}" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;">Matured</a>
            <a href="{{ route('admin.food-support.index', ['status' => 'withdrawn']) }}" class="btn {{ request('status') == 'withdrawn' ? 'btn-primary' : 'btn-secondary' }}" style="padding: 0.375rem 0.75rem; font-size: 0.8rem;">Withdrawn</a>
        </div>
    </div>
    <div class="page-card-body" style="padding: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>User</th>
                    <th>Type</th>
                    <th>Target</th>
                    <th>Contributed</th>
                    <th>Status</th>
                    <th>Maturity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plans as $plan)
                <tr>
                    <td style="font-family: monospace; font-weight: 600;">{{ $plan->reference }}</td>
                    <td>
                        <div style="font-weight: 500;">{{ $plan->user?->full_name ?? 'N/A' }}</div>
                        <div style="font-size: 0.75rem; color: #94a3b8;">{{ $plan->user?->email ?? '' }}</div>
                    </td>
                    <td>{{ str_replace('_', ' ', $plan->plan_type) }}</td>
                    <td style="font-weight: 600; color: #1e293b; font-family: monospace;">₦{{ number_format($plan->plan_value, 2) }}</td>
                    <td style="font-weight: 600; color: #1e293b; font-family: monospace;">₦{{ number_format($plan->total_contributed, 2) }}</td>
                    <td>
                        <span class="badge
                            @if(in_array($plan->status, ['active', 'pending'])) badge-info
                            @elseif($plan->status === 'fully_funded') badge-purple
                            @elseif($plan->status === 'matured') badge-success
                            @elseif($plan->status === 'withdrawn') badge-primary
                            @else badge-dark
                            @endif">
                            {{ str_replace('_', ' ', $plan->status) }}
                        </span>
                    </td>
                    <td style="font-size: 0.85rem;">{{ $plan->maturity_date ? $plan->maturity_date->format('d M Y') : 'N/A' }}</td>
                    <td><a href="{{ route('admin.food-support.show', $plan) }}" class="btn btn-primary" style="padding: 0.375rem 0.75rem; font-size: 0.75rem;">View</a></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p>No plans found.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 1rem; border-top: 1px solid #f1f5f9;">
        {{ $plans->withQueryString()->links() }}
    </div>
</div>
@endsection
