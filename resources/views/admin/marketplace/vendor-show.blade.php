@extends('admin.layouts.app')

@section('title', $vendor->business_name ?? $vendor->name)
@section('header', 'Vendor Details')
@section('subheader', $vendor->business_name ?? $vendor->name)

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 12px;">
    <a href="{{ route('admin.marketplace.vendors.index') }}" class="btn btn-secondary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Vendors
    </a>
    <div style="display: flex; gap: 8px;">
        <form method="POST" action="{{ route('admin.marketplace.vendors.status', $vendor) }}">
            @csrf
            <input type="hidden" name="status" value="approved">
            @if($vendor->status !== 'approved')
            <button type="submit" class="btn btn-primary">Approve Vendor</button>
            @endif
        </form>
        @if($vendor->status === 'approved')
        <form method="POST" action="{{ route('admin.marketplace.vendors.status', $vendor) }}"
            data-confirm='{"title": "Suspend vendor?", "text": "Suspend this vendor? Their products will be hidden.", "btnClass": "confirm-danger", "btnText": "Suspend"}'>
            @csrf
            <input type="hidden" name="status" value="suspended">
            <button type="submit" class="btn btn-danger">Suspend Vendor</button>
        </form>
        @endif
    </div>
</div>

<div class="grid-2" style="margin-bottom: 2rem;">
    <!-- Vendor info -->
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">{{ $vendor->business_name ?? $vendor->name }}</h3>
                <p class="page-card-subtitle">{{ $vendor->name }}</p>
            </div>
            <span class="badge
                @if($vendor->status === 'approved') badge-success
                @elseif($vendor->status === 'pending') badge-warning
                @else badge-error @endif">{{ ucfirst($vendor->status) }}</span>
        </div>
        <div class="page-card-body">
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Email</span><span style="font-weight: 600; color: #1e293b;">{{ $vendor->email }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Phone</span><span style="font-weight: 600; color: #1e293b;">{{ $vendor->phone }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Location</span><span style="font-weight: 600; color: #1e293b;">{{ $vendor->address ?? '—' }} {{ $vendor->city }} {{ $vendor->state }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Commission Rate</span><span style="font-weight: 600; color: #1e293b;">{{ $vendor->commission_rate ? $vendor->commission_rate.'%' : '—' }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Wallet Balance</span><span style="font-weight: 700; color: #059669;">{{ format_currency($vendor->wallet_balance) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Owner Account</span>
                <span style="font-weight: 600; color: #1e293b;">
                    @if($vendor->user)
                    <a href="{{ route('admin.users.show', $vendor->user->id) }}" class="action-link">{{ $vendor->user->name }}</a>
                    @else — @endif
                </span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                <span style="color: #64748b;">Joined</span><span style="font-weight: 600; color: #1e293b;">{{ $vendor->created_at->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Performance</h3>
                <p class="page-card-subtitle">Sales and reputation summary</p>
            </div>
        </div>
        <div class="page-card-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; text-align: center;">
                    <div style="font-size: 12px; color: #64748b;">Products</div>
                    <div style="font-weight: 700; color: #1e293b; margin-top: 4px; font-size: 20px;">{{ number_format($vendor->products_count) }}</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; text-align: center;">
                    <div style="font-size: 12px; color: #64748b;">Delivered Revenue</div>
                    <div style="font-weight: 700; color: #059669; margin-top: 4px; font-size: 20px;">{{ format_currency($revenue) }}</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; text-align: center;">
                    <div style="font-size: 12px; color: #64748b;">Rating</div>
                    <div style="font-weight: 700; color: #f59e0b; margin-top: 4px; font-size: 20px;">★ {{ number_format((float) $vendor->rating, 1) }}</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; text-align: center;">
                    <div style="font-size: 12px; color: #64748b;">Reviews</div>
                    <div style="font-weight: 700; color: #1e293b; margin-top: 4px; font-size: 20px;">{{ number_format($vendor->review_count) }}</div>
                </div>
            </div>
            @if($vendor->description)
            <p style="color: #475569; line-height: 1.6; margin: 0;">{{ $vendor->description }}</p>
            @endif
        </div>
    </div>
</div>

<!-- Products -->
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Vendor Products</h3>
            <p class="page-card-subtitle">{{ $vendor->products->count() }} product(s) listed</p>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Sold</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vendor->products as $product)
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        @if($product->image)
                        <img src="{{ $product->image }}" alt="" style="width: 36px; height: 36px; border-radius: 8px; object-fit: cover;">
                        @endif
                        <span style="font-weight: 600; color: #1e293b;">{{ $product->name }}</span>
                    </div>
                </td>
                <td>{{ $product->category?->name ?? '—' }}</td>
                <td style="font-weight: 700; color: #059669;">{{ format_currency($product->selling_price) }}</td>
                <td>
                    @if($product->stock_quantity <= 0)<span class="badge badge-error">0</span>
                    @elseif($product->isLowStock())<span class="badge badge-warning">{{ number_format($product->stock_quantity) }}</span>
                    @else<span class="badge badge-success">{{ number_format($product->stock_quantity) }}</span>@endif
                </td>
                <td>{{ number_format($product->sold_count) }}</td>
                <td><span class="badge {{ $product->is_active ? 'badge-success' : 'badge-error' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td><a href="{{ route('admin.marketplace.products.show', $product) }}" class="action-link">View</a></td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="empty-state">No products listed by this vendor.</div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
