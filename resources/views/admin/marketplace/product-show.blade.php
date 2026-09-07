@extends('admin.layouts.app')

@section('title', $product->name)
@section('header', 'Product Details')
@section('subheader', $product->name)

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 12px;">
    <a href="{{ route('admin.marketplace.products.index') }}" class="btn btn-secondary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Products
    </a>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.marketplace.products.edit', $product) }}" class="btn btn-primary">Edit Product</a>
        <form method="POST" action="{{ route('admin.marketplace.products.toggle', $product) }}"
            data-confirm='{"title": "Toggle product?", "text": "{{ $product->is_active ? 'Deactivate' : 'Activate' }} \"{{ $product->name }}\"?", "btnText": "Confirm"}'>
            @csrf
            <button type="submit" class="btn btn-warning">{{ $product->is_active ? 'Deactivate' : 'Activate' }}</button>
        </form>
    </div>
</div>

<div class="grid-2" style="margin-bottom: 2rem;">
    <!-- Product info -->
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">{{ $product->name }}</h3>
                <p class="page-card-subtitle">SKU: {{ $product->sku }}</p>
            </div>
            <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-error' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span>
        </div>
        <div class="page-card-body">
            <div style="display: flex; gap: 16px; margin-bottom: 1rem;">
                @if($product->image)
                <img src="{{ $product->image }}" alt="{{ $product->name }}" style="width: 140px; height: 140px; border-radius: 12px; object-fit: cover; background: #f1f5f9; border: 1px solid #e2e8f0;">
                @endif
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b;">Category</span><span style="font-weight: 600; color: #1e293b;">{{ $product->category?->name ?? '—' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b;">Brand</span><span style="font-weight: 600; color: #1e293b;">{{ $product->brand?->name ?? '—' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b;">Vendor</span><span style="font-weight: 600; color: #1e293b;">{{ $product->vendor?->business_name ?? '—' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b;">Unit</span><span style="font-weight: 600; color: #1e293b;">{{ $product->unit }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 6px 0;">
                        <span style="color: #64748b;">Weight</span><span style="font-weight: 600; color: #1e293b;">{{ $product->weight ? number_format((float) $product->weight, 2).' '.$product->weight_unit : '—' }}</span>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 1.25rem;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; text-align: center;">
                    <div style="font-size: 12px; color: #64748b;">Price</div>
                    <div style="font-weight: 700; color: #059669; margin-top: 4px;">{{ format_currency($product->price) }}</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; text-align: center;">
                    <div style="font-size: 12px; color: #64748b;">Selling</div>
                    <div style="font-weight: 700; color: #059669; margin-top: 4px;">{{ format_currency($product->selling_price) }}</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; text-align: center;">
                    <div style="font-size: 12px; color: #64748b;">Member</div>
                    <div style="font-weight: 700; color: #7c3aed; margin-top: 4px;">{{ $product->member_selling_price ? format_currency($product->member_selling_price) : '—' }}</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; text-align: center;">
                    <div style="font-size: 12px; color: #64748b;">Discount</div>
                    <div style="font-weight: 700; color: #d97706; margin-top: 4px;">{{ $product->discount_percent ? $product->discount_percent.'%' : '—' }}</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; text-align: center;">
                    <div style="font-size: 12px; color: #64748b;">Stock</div>
                    <div style="font-weight: 700; color: #1e293b; margin-top: 4px;">{{ number_format($product->stock_quantity) }}</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; text-align: center;">
                    <div style="font-size: 12px; color: #64748b;">Sold</div>
                    <div style="font-weight: 700; color: #1e293b; margin-top: 4px;">{{ number_format($product->sold_count) }}</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; text-align: center;">
                    <div style="font-size: 12px; color: #64748b;">Views</div>
                    <div style="font-weight: 700; color: #1e293b; margin-top: 4px;">{{ number_format($product->views_count) }}</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; text-align: center;">
                    <div style="font-size: 12px; color: #64748b;">Rating</div>
                    <div style="font-weight: 700; color: #f59e0b; margin-top: 4px;">★ {{ number_format((float) $product->average_rating, 1) }} ({{ number_format($product->rating_count) }})</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dates & details -->
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Product Details</h3>
                <p class="page-card-subtitle">Shelf life, origin and classifications</p>
            </div>
        </div>
        <div class="page-card-body">
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Barcode</span><span style="font-weight: 600; color: #1e293b;">{{ $product->barcode ?? '—' }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Batch</span><span style="font-weight: 600; color: #1e293b;">{{ $product->batch_number ?? '—' }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Manufacturing Date</span><span style="font-weight: 600; color: #1e293b;">{{ $product->manufacturing_date?->format('M d, Y') ?? '—' }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Expiry Date</span>
                <span style="font-weight: 600; color: #1e293b;">
                    {{ $product->expiry_date?->format('M d, Y') ?? '—' }}
                    @if($product->isExpired()) <span class="badge badge-error">Expired</span>
                    @elseif($product->isExpiringSoon()) <span class="badge badge-warning">Expiring</span>@endif
                </span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Country of Origin</span><span style="font-weight: 600; color: #1e293b;">{{ $product->country_of_origin ?? '—' }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Featured</span>
                <span class="badge {{ $product->is_featured ? 'badge-success' : 'badge-dark' }}">{{ $product->is_featured ? 'Featured' : 'Not featured' }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Created</span><span style="font-weight: 600; color: #1e293b;">{{ $product->created_at->format('M d, Y') }}</span>
            </div>
            @if($product->video_url)
            <div style="padding: 8px 0;">
                <a href="{{ $product->video_url }}" target="_blank" class="action-link">Watch product video →</a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Images -->
@if($product->images->count())
<div class="page-card" style="margin-bottom: 2rem;">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Product Images</h3>
            <p class="page-card-subtitle">{{ $product->images->count() }} image(s)</p>
        </div>
    </div>
    <div style="display: flex; gap: 12px; flex-wrap: wrap; padding: 1rem;">
        @foreach($product->images as $image)
        <div style="position: relative;">
            <img src="{{ $image->image_path }}" alt="" style="width: 100px; height: 100px; border-radius: 10px; object-fit: cover; border: 2px solid {{ $image->is_primary ? '#10b981' : '#e2e8f0' }};">
            @if($image->is_primary)
            <span class="badge badge-success" style="position: absolute; top: -6px; left: -6px; font-size: 10px;">Primary</span>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Description + Info -->
<div class="grid-2" style="margin-bottom: 2rem;">
    <div class="page-card">
        <div class="page-card-header">
            <div><h3 class="page-card-title">Description</h3></div>
        </div>
        <div class="page-card-body" style="color: #475569; line-height: 1.7;">{!! nl2br(e($product->description ?? 'No description provided.')) !!}</div>
    </div>
    <div class="page-card">
        <div class="page-card-header">
            <div><h3 class="page-card-title">Ingredients & Nutrition</h3></div>
        </div>
        <div class="page-card-body">
            <div style="margin-bottom: 1rem;">
                <p style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">Ingredients</p>
                <p style="color: #475569; margin: 0;">{{ $product->ingredients ?? '—' }}</p>
            </div>
            <div style="margin-bottom: 1rem;">
                <p style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">Nutritional Info</p>
                <p style="color: #475569; margin: 0;">{!! nl2br(e($product->nutritional_info ?? '—')) !!}</p>
            </div>
            <div>
                <p style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">Storage Instructions</p>
                <p style="color: #475569; margin: 0;">{{ $product->storage_instructions ?? '—' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Inventory Movements -->
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Inventory Movement History</h3>
            <p class="page-card-subtitle">{{ $product->inventoryMovements->count() }} recorded movements</p>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Qty</th>
                <th>Batch</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            @forelse($product->inventoryMovements as $movement)
            <tr>
                <td style="white-space: nowrap;">{{ $movement->created_at->format('M d, Y h:i A') }}</td>
                <td>
                    <span class="badge
                        @if(in_array($movement->type, ['stock_in', 'returned'])) badge-success
                        @elseif(in_array($movement->type, ['stock_out', 'sale'])) badge-info
                        @elseif($movement->type === 'damaged') badge-error
                        @else badge-warning @endif">
                        {{ \App\Models\Marketplace\InventoryMovement::TYPES[$movement->type] ?? $movement->type }}
                    </span>
                </td>
                <td>{{ $movement->quantity }}</td>
                <td>{{ $movement->batch_number ?? '—' }}</td>
                <td>{{ $movement->reason ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="5"><div class="empty-state">No inventory movements recorded.</div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
