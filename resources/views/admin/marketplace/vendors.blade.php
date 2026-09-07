@extends('admin.layouts.app')

@section('title', 'Marketplace Vendors')
@section('header', 'Marketplace Vendors')
@section('subheader', 'Approve, review and manage third-party sellers on the marketplace.')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1>Vendors</h1>
        <p>{{ number_format($vendors->total()) }} vendor(s) on the platform.</p>
    </div>
</div>

<!-- Filters -->
<div class="filter-card">
    <form method="GET" action="{{ route('admin.marketplace.vendors.index') }}" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 220px;">
            <label class="form-label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Search by name or business name...">
        </div>
        <div style="min-width: 160px;">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.marketplace.vendors.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="page-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Vendor</th>
                <th>Contact</th>
                <th>Products</th>
                <th>Rating</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vendors as $vendor)
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        @if($vendor->logo)
                        <img src="{{ $vendor->logo }}" alt="" style="width: 44px; height: 44px; border-radius: 10px; object-fit: cover; background: #f1f5f9;">
                        @else
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #94a3b8;">{{ substr($vendor->business_name ?? $vendor->name, 0, 1) }}</div>
                        @endif
                        <div>
                            <div style="font-weight: 600; color: #1e293b;">{{ $vendor->business_name ?? $vendor->name }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">{{ $vendor->city ?? '' }} {{ $vendor->city && $vendor->state ? '·' : '' }} {{ $vendor->state }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="font-size: 13px; color: #475569;">{{ $vendor->email }}</div>
                    <div style="font-size: 12px; color: #94a3b8;">{{ $vendor->phone }}</div>
                </td>
                <td>
                    <span class="badge badge-primary">{{ number_format($vendor->products_count) }} products</span>
                </td>
                <td>
                    @if($vendor->review_count > 0)
                    <span style="color: #f59e0b;">★</span> {{ number_format((float) $vendor->rating, 1) }}
                    <span style="color: #94a3b8; font-size: 12px;">({{ number_format($vendor->review_count) }})</span>
                    @else
                    <span style="color: #94a3b8;">—</span>
                    @endif
                </td>
                <td>
                    <span class="badge
                        @if($vendor->status === 'approved') badge-success
                        @elseif($vendor->status === 'pending') badge-warning
                        @else badge-error @endif">{{ ucfirst($vendor->status) }}</span>
                </td>
                <td style="white-space: nowrap;">
                    <a href="{{ route('admin.marketplace.vendors.show', $vendor) }}" class="action-link">View</a>
                    @if($vendor->status !== 'approved')
                    <form method="POST" action="{{ route('admin.marketplace.vendors.status', $vendor) }}" style="display: inline;">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="action-link" style="border: none; background: none; cursor: pointer; padding: 0; color: #059669;">Approve</button>
                    </form>
                    @endif
                    @if($vendor->status === 'approved')
                    <form method="POST" action="{{ route('admin.marketplace.vendors.status', $vendor) }}" style="display: inline;"
                        data-confirm='{"title": "Suspend vendor?", "text": "Suspend \"{{ $vendor->business_name ?? $vendor->name }}\"? Their products will be hidden from the storefront.", "btnClass": "confirm-danger", "btnText": "Suspend"}'>
                        @csrf
                        <input type="hidden" name="status" value="suspended">
                        <button type="submit" class="action-link" style="border: none; background: none; cursor: pointer; padding: 0; color: #dc2626;">Suspend</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6"><div class="empty-state">No vendors found</div></td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination">
    {{ $vendors->links() }}
</div>
@endsection
