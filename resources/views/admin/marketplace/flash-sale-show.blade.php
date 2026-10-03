@extends('admin.layouts.app')

@section('title', $flashSale->name)
@section('header', 'Flash Sale Details')
@section('subheader', $flashSale->name)

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 12px;">
    <a href="{{ route('admin.marketplace.flash-sales.index') }}" class="btn btn-secondary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Flash Sales
    </a>
    <div style="display: flex; gap: 8px;">
        <span class="badge
            @if($flashSale->isActive()) badge-success
            @elseif($flashSale->ends_at && $flashSale->ends_at->isPast()) badge-dark
            @else badge-warning @endif" style="font-size: 13px; padding: 8px 14px;">
            @if($flashSale->isActive()) Live now @elseif($flashSale->ends_at && $flashSale->ends_at->isPast()) Ended @else Scheduled @endif
        </span>
        <form method="POST" action="{{ route('admin.marketplace.flash-sales.toggle', $flashSale) }}">
            @csrf
            <button type="submit" class="btn {{ $flashSale->is_active ? 'btn-warning' : 'btn-primary' }}">{{ $flashSale->is_active ? 'Deactivate' : 'Activate' }}</button>
        </form>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 1rem;">{{ session('success') }}</div>
@endif

<!-- Info + Add product -->
<div class="grid-2" style="margin-bottom: 2rem;">
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">{{ $flashSale->name }}</h3>
                <p class="page-card-subtitle">Event window</p>
            </div>
        </div>
        <div class="page-card-body">
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Starts At</span><span style="font-weight: 600; color: #1e293b;">{{ $flashSale->starts_at->format('M d, Y H:i A') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Ends At</span><span style="font-weight: 600; color: #1e293b;">{{ $flashSale->ends_at->format('M d, Y H:i A') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                <span style="color: #64748b;">Items</span><span style="font-weight: 600; color: #1e293b;">{{ number_format($flashSale->items->count()) }} products</span>
            </div>
            @if($flashSale->description)
            <p style="color: #475569; line-height: 1.6; margin-top: 12px;">{{ $flashSale->description }}</p>
            @endif
        </div>
    </div>

    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Add Product to Flash Sale</h3>
                <p class="page-card-subtitle">{{ $available->count() }} active in-stock products available</p>
            </div>
        </div>
        <div class="page-card-body">
            <form method="POST" action="{{ route('admin.marketplace.flash-sales.add-item', $flashSale) }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
                @csrf
                <div style="flex: 2; min-width: 260px;">
                    <label class="form-label">Product *</label>
                    <select name="product_id" class="form-select" required>
                        <option value="">— Select product —</option>
                        @foreach($available as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} — {{ format_currency($product->price) }} ({{ number_format($product->stock_quantity) }} in stock)</option>
                        @endforeach
                    </select>
                </div>
                <div style="flex: 1; min-width: 120px;">
                    <label class="form-label">Sale Price (₦) *</label>
                    <input type="number" name="sale_price" step="0.01" min="1" required class="form-input">
                </div>
                <div style="flex: 1; min-width: 120px;">
                    <label class="form-label">Qty Limit</label>
                    <input type="number" name="quantity_limit" min="1" class="form-input" placeholder="e.g., 2 per buyer">
                </div>
                <button type="submit" class="btn btn-primary">Add Product</button>
            </form>
            @if($available->isEmpty())
            <p style="font-size: 13px; color: #94a3b8; margin-top: 10px;">All active in-stock products are already in this flash sale.</p>
            @endif
        </div>
    </div>
</div>

<!-- Items -->
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Flash Sale Products</h3>
            <p class="page-card-subtitle">Products with their special pricing</p>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Regular Price</th>
                <th>Sale Price</th>
                <th>Discount</th>
                <th>Qty Limit</th>
                <th>Sold</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($flashSale->items as $item)
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        @if($item->product && $item->product->image)
                        <img src="{{ $item->product->image }}" alt="" style="width: 36px; height: 36px; border-radius: 8px; object-fit: cover;">
                        @endif
                        <div>
                            <div style="font-weight: 600; color: #1e293b;">{{ $item->product?->name ?? 'Deleted product' }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">{{ $item->product?->sku ?? '' }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ $item->product ? format_currency($item->product->price) : '—' }}</td>
                <td style="font-weight: 700; color: #059669;">{{ format_currency($item->sale_price) }}</td>
                <td>
                    @if($item->product && $item->product->price > 0)
                    <span class="badge badge-warning">{{ round((($item->product->price - $item->sale_price) / $item->product->price) * 100) }}% off</span>
                    @endif
                </td>
                <td>{{ $item->quantity_limit ?? '—' }}</td>
                <td>{{ number_format($item->sold) }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.marketplace.flash-sales.remove-item', $item) }}"
                        data-confirm='{"title": "Remove product?", "text": "Remove this product from the flash sale? Regular pricing will apply.", "btnClass": "confirm-danger", "btnText": "Remove"}'>
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-link" style="border: none; background: none; cursor: pointer; padding: 0; color: #dc2626;">Remove</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="empty-state">No products added to this flash sale yet.</div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
