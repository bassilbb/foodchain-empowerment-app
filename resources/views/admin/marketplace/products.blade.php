@extends('admin.layouts.app')

@section('title', 'Marketplace Products')
@section('header', 'Marketplace Products')
@section('subheader', 'Manage the full marketplace catalogue, pricing and stock.')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1>Products</h1>
        <p>{{ number_format($products->total()) }} products in the catalogue.</p>
    </div>
    <div class="header-actions-btns">
        <a href="{{ route('admin.marketplace.dashboard') }}" class="btn btn-secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            Overview
        </a>
    </div>
</div>

<!-- Filters -->
<div class="filter-card">
    <form method="GET" action="{{ route('admin.marketplace.products.index') }}" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 220px;">
            <label class="form-label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Search by name or SKU...">
        </div>
        <div style="min-width: 200px;">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select">
                <option value="">All categories</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="min-width: 160px;">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.marketplace.products.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="page-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Vendor</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Sales</th>
                <th>Rating</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        @if($product->image)
                        <img src="{{ $product->image }}" alt="" style="width: 44px; height: 44px; border-radius: 10px; object-fit: cover; background: #f1f5f9;">
                        @else
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #94a3b8;">{{ substr($product->name, 0, 1) }}</div>
                        @endif
                        <div style="min-width: 0;">
                            <div style="font-weight: 600; color: #1e293b;">{{ $product->name }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">{{ $product->sku }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ $product->category?->name ?? '—' }}</td>
                <td style="white-space: nowrap;">{{ $product->vendor?->business_name ?? '—' }}</td>
                <td style="white-space: nowrap;">
                    <span style="font-weight: 700; color: #059669;">{{ format_currency($product->selling_price) }}</span>
                    @if($product->discount_price && $product->discount_price < $product->price)
                    <div style="font-size: 12px; color: #94a3b8; text-decoration: line-through;">{{ format_currency($product->price) }}</div>
                    @endif
                </td>
                <td>
                    @if($product->stock_quantity <= 0)
                    <span class="badge badge-error">Out of stock</span>
                    @elseif($product->isLowStock())
                    <span class="badge badge-warning">{{ number_format($product->stock_quantity) }} low</span>
                    @else
                    <span class="badge badge-success">{{ number_format($product->stock_quantity) }}</span>
                    @endif
                    @if($product->isExpiringSoon())
                    <div style="font-size: 12px; color: #d97706; margin-top: 2px;">Expires {{ $product->expiry_date->format('M d, Y') }}</div>
                    @endif
                </td>
                <td>{{ number_format($product->sold_count) }}</td>
                <td style="white-space: nowrap;">
                    @if($product->rating_count > 0)
                    <span style="color: #f59e0b;">★</span> {{ number_format((float) $product->average_rating, 1) }}
                    <span style="color: #94a3b8; font-size: 12px;">({{ number_format($product->rating_count) }})</span>
                    @else
                    <span style="color: #94a3b8;">No ratings</span>
                    @endif
                </td>
                <td>
                    <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-error' }}">
                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td style="white-space: nowrap;">
                    <a href="{{ route('admin.marketplace.products.show', $product) }}" class="action-link">View</a>
                    <a href="{{ route('admin.marketplace.products.edit', $product) }}" class="action-link">Edit</a>
                    <form method="POST" action="{{ route('admin.marketplace.products.toggle', $product) }}" style="display: inline;"
                        data-confirm='{"title": "Toggle product?", "text": "This will {{ $product->is_active ? 'deactivate' : 'activate' }} \"{{ $product->name }}\".", "btnClass": "confirm-{{ $product->is_active ? 'danger' : 'confirm' }}", "btnText": "{{ $product->is_active ? 'Deactivate' : 'Activate' }}"}'>
                        @csrf
                        <button type="submit" class="action-link" style="border: none; background: none; cursor: pointer; padding: 0; color: #2563eb;">{{ $product->is_active ? 'Deactivate' : 'Activate' }}</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9">
                    <div class="empty-state">No products found</div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination">
    {{ $products->links() }}
</div>
@endsection
