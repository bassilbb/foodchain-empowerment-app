@extends('admin.layouts.app')

@section('title', 'Account Clearances')
@section('header', 'Account Clearances')
@section('subheader', 'Review and manage savings plan clearance requests')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="stat-card" style="background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white;">
        <p style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.85; margin: 0 0 0.25rem 0;">Pending Payment</p>
        <p style="font-size: 2rem; font-weight: 800; margin: 0;">{{ number_format($stats['pending_payment']) }}</p>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #0ea5e9, #3b82f6); color: white;">
        <p style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.85; margin: 0 0 0.25rem 0;">Pending Approval</p>
        <p style="font-size: 2rem; font-weight: 800; margin: 0;">{{ number_format($stats['pending_approval']) }}</p>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #10b981, #059669); color: white;">
        <p style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.85; margin: 0 0 0.25rem 0;">Approved</p>
        <p style="font-size: 2rem; font-weight: 800; margin: 0;">{{ number_format($stats['approved']) }}</p>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #ec4899, #8b5cf6); color: white;">
        <p style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.85; margin: 0 0 0.25rem 0;">Clearance Revenue</p>
        <p style="font-size: 1.75rem; font-weight: 800; margin: 0;">₦{{ number_format($stats['total_revenue'], 0) }}</p>
    </div>
</div>

<div class="filter-card">
    <div class="page-card-body">
        <form method="GET" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending_payment" {{ request('status') === 'pending_payment' ? 'selected' : '' }}>Pending Payment</option>
                    <option value="pending_approval" {{ request('status') === 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Search</label>
                <input type="text" name="search" placeholder="User name, email, or account..." value="{{ request('search') }}" class="form-input" style="width: 250px;">
            </div>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->anyFilled(['status', 'search']))
            <a href="{{ route('admin.clearances.index') }}" class="btn-secondary">Clear</a>
            @endif
        </form>
    </div>
</div>

<div class="page-card">
    <div class="data-table">
        <form method="POST" action="{{ route('admin.clearances.batch-approve') }}" id="batchForm">
            @csrf
            <div style="padding: 0.75rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 0.75rem;">
                <button type="button" onclick="
                    const cbs = document.querySelectorAll('.clearance-checkbox');
                    const allChecked = Array.from(cbs).every(cb => cb.checked);
                    cbs.forEach(cb => cb.checked = !allChecked);
                " class="btn-secondary btn-sm">Toggle All</button>
                <button type="submit" onclick="
                    const ids = Array.from(document.querySelectorAll('.clearance-checkbox:checked')).map(cb => cb.value);
                    if (ids.length === 0) { alert('Select at least one clearance.'); return false; }
                    return confirm('Approve '+ids.length+' clearance(s)? One distribution and voucher will be created for all selected accounts.');
                " class="btn-primary btn-sm">
                    Batch Approve Selected
                </button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th class="table-header" style="width: 40px; text-align: center;"></th>
                        <th class="table-header">User</th>
                        <th class="table-header">Account</th>
                        <th class="table-header" style="text-align: center;">Progress</th>
                        <th class="table-header" style="text-align: center;">Status</th>
                        <th class="table-header" style="text-align: right;">Fee</th>
                        <th class="table-header">Date</th>
                        <th class="table-header" style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clearanceRequests as $cr)
                    <tr>
                        <td style="text-align: center;">
                            @if($cr->status === 'pending_approval')
                            <input type="checkbox" name="clearance_ids[]" value="{{ $cr->id }}" class="clearance-checkbox" style="width: 16px; height: 16px; cursor: pointer;">
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #10b981, #0ea5e9); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.8rem; font-weight: 700;">
                                    {{ substr($cr->user?->first_name ?? '?', 0, 1) }}{{ substr($cr->user?->last_name ?? '', 0, 1) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; font-size: 0.9rem;">{{ $cr->user?->full_name ?? 'N/A' }}</div>
                                    <div style="font-size: 0.75rem; color: #94a3b8;">{{ $cr->user?->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 600; font-size: 0.9rem;">{{ $cr->userAccount?->account_name ?? 'N/A' }}</div>
                            <div style="font-size: 0.75rem; color: #94a3b8;">{{ $cr->userAccount?->plan_name ?? '' }}</div>
                        </td>
                        <td style="text-align: center;">
                            @if($cr->userAccount)
                            @php
                                $pct = $cr->userAccount->plan_duration_weeks > 0
                                    ? min(100, round(($cr->userAccount->current_tariff_weeks / $cr->userAccount->plan_duration_weeks) * 100))
                                    : 0;
                            @endphp
                            <div style="display: flex; align-items: center; gap: 0.5rem; justify-content: center;">
                                <span style="font-weight: 600;">{{ $cr->userAccount->current_tariff_weeks }}/{{ $cr->userAccount->plan_duration_weeks }}</span>
                                <div style="width: 50px; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                                    <div style="height: 100%; width: {{ $pct }}%; background: linear-gradient(135deg, #10b981, #059669); border-radius: 3px;"></div>
                                </div>
                            </div>
                            @else
                            <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-{{ $cr->status === 'approved' ? 'success' : ($cr->status === 'pending_approval' ? 'info' : 'warning') }}">
                                {{ $cr->status === 'pending_payment' ? 'Pending Payment' : ($cr->status === 'pending_approval' ? 'Pending Approval' : 'Approved') }}
                            </span>
                        </td>
                        <td style="text-align: right; font-family: monospace; font-weight: 600;">₦{{ number_format($cr->fee_amount, 2) }}</td>
                        <td style="font-size: 0.85rem; color: #64748b;">{{ $cr->created_at->format('M d, Y') }}</td>
                        <td style="text-align: right;">
                            <a href="{{ route('admin.clearances.show', $cr->id) }}" class="btn-primary btn-sm">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Review
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="empty-state">
                            <p style="font-weight: 600; margin: 0 0 0.25rem 0;">No clearance requests found</p>
                            <p style="font-size: 0.875rem; color: #94a3b8; margin: 0;">Clearance requests appear automatically when accounts reach their full duration.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </form>
    </div>
    @if($clearanceRequests->hasPages())
    <div class="pagination">
        {{ $clearanceRequests->links() }}
    </div>
    @endif
</div>
@endsection
