@extends('admin.layouts.app')

@section('title', 'Vouchers')
@section('header', 'Vouchers')
@section('subheader', 'Manage food collection vouchers')

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">Issue New Voucher</h3>
    </div>
    <div class="page-card-body">
        <form method="POST" action="{{ route('admin.vouchers.store') }}" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
            @csrf
            <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                <label class="form-label">User</label>
                <select name="user_id" required class="form-select">
                    <option value="">Select User</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                <label class="form-label">Distribution (optional)</label>
                <select name="food_distribution_id" class="form-select">
                    <option value="">Select Distribution</option>
                    @foreach($distributions as $dist)
                    <option value="{{ $dist->id }}">{{ $dist->name }} - {{ $dist->distribution_date ? $dist->distribution_date->format('M d, Y') : 'No date' }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                Issue Voucher
            </button>
        </form>
    </div>
</div>

<div class="page-card">
    <div class="page-card-body">
        <form method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
            <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                <label class="form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by voucher number, collection code..." class="form-input">
            </div>
            <div class="form-group" style="min-width: 150px; margin-bottom: 0;">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="issued" {{ request('status') === 'issued' ? 'selected' : '' }}>Issued</option>
                    <option value="collected" {{ request('status') === 'collected' ? 'selected' : '' }}>Collected</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                Filter
            </button>
        </form>
    </div>
</div>

<div class="page-card">
    <div class="page-card-body" style="padding: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Voucher #</th>
                    <th>User</th>
                    <th>Collection Code</th>
                    <th>Distribution</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vouchers as $voucher)
                <tr>
                    <td style="font-family: monospace; font-weight: 700; color: #1e293b;">{{ $voucher->voucher_number }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #10b981, #0ea5e9); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem; font-weight: 700;">
                                {{ substr($voucher->user?->first_name ?? 'U', 0, 1) }}
                            </div>
                            <span style="font-weight: 600; color: #1e293b;">{{ $voucher->user?->full_name ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td>
                        @if($voucher->collection_code)
                        <span style="font-family: monospace; font-size: 0.875rem; color: #64748b;">{{ $voucher->collection_code }}</span>
                        @else
                        <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                    <td>
                        @if($voucher->distribution)
                        {{ $voucher->distribution->name }}
                        @else
                        <span style="color: #94a3b8;">Manual</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge
                            @if($voucher->status === 'pending') badge-warning
                            @elseif($voucher->status === 'issued') badge-info
                            @elseif($voucher->status === 'collected') badge-success
                            @else badge-dark
                            @endif">
                            {{ ucfirst($voucher->status) }}
                        </span>
                    </td>
                    <td style="font-size: 0.875rem; color: #64748b;">{{ $voucher->created_at->format('M d, Y') }}</td>
                    <td style="text-align: right;">
                        <a href="{{ route('admin.vouchers.show', $voucher) }}" class="action-link">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                            <p>No vouchers found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($vouchers->hasPages())
<div class="pagination">
    {{ $vouchers->links() }}
</div>
@endif
@endsection
