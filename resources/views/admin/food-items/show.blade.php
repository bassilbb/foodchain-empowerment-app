@extends('admin.layouts.app')

@section('title', $foodItem->name)
@section('header', $foodItem->name)
@section('subheader', 'Food item details and stock levels')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <a href="{{ route('admin.food-items.index') }}" class="btn btn-secondary btn-sm">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Food Items
    </a>
    <a href="{{ route('admin.food-items.edit', $foodItem) }}" class="btn btn-primary btn-sm">Edit Food Item</a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon emerald"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg></div>
        <div class="stat-label">Total Stock</div>
        <div class="stat-value">{{ number_format($foodItem->getTotalStock(), 2) }} <span style="font-size: 1rem; color: #64748b;">{{ $foodItem->unit }}</span></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon sky"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
        <div class="stat-label">Locations Stocked</div>
        <div class="stat-value">{{ $foodItem->inventory->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg></div>
        <div class="stat-label">Total Stock Value</div>
        <div class="stat-value" style="font-size: 1.5rem;">{{ format_currency($foodItem->getTotalStockValue()) }}</div>
    </div>
    <div class="stat-card {{ $foodItem->is_active ? '' : 'danger' }}">
        <div class="stat-icon {{ $foodItem->is_active ? 'emerald' : 'red' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
        <div class="stat-label">Status</div>
        <div class="stat-value" style="font-size: 1.5rem;">{{ $foodItem->is_active ? 'Active' : 'Inactive' }}</div>
    </div>
</div>

<div class="grid-2" style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Food Item Details</h3>
        </div>
        <div class="page-card-body">
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                @if($foodItem->image)
                <div style="text-align: center;">
                        <img src="{{ public_storage_url($foodItem->image) }}" alt="{{ $foodItem->name }}" style="max-height: 180px; border-radius: 12px; border: 1px solid #e2e8f0;">
                </div>
                @endif
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Name</label>
                    <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $foodItem->name }}</p>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Slug</label>
                    <p style="font-weight: 500; color: #1e293b; margin: 0; font-family: monospace;">{{ $foodItem->slug }}</p>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Unit</label>
                    <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $foodItem->unit }}</p>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Unit Price</label>
                    <p style="font-weight: 700; color: #10b981; margin: 0;">{{ format_currency($foodItem->unit_price) }}</p>
                </div>
                @if($foodItem->description)
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Description</label>
                    <p style="font-weight: 400; color: #1e293b; margin: 0; line-height: 1.6;">{{ $foodItem->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Inventory by Location</h3>
            <span class="badge badge-info">{{ $foodItem->inventory->count() }} records</span>
        </div>
        <div style="padding: 0;">
            @if($foodItem->inventory->count())
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>Quantity</th>
                            <th>Reserved</th>
                            <th>Available</th>
                            <th>Cost / Unit</th>
                            <th>Expiry</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($foodItem->inventory as $inventory)
                        <tr>
                            <td style="font-weight: 600; color: #1e293b;">{{ $inventory->location?->name ?? 'Unknown' }}</td>
                            <td>{{ number_format($inventory->quantity, 2) }}</td>
                            <td>{{ number_format($inventory->reserved_quantity, 2) }}</td>
                            <td>
                                <span class="badge {{ $inventory->available_quantity < 10 ? 'badge-error' : 'badge-success' }}">
                                    {{ number_format($inventory->available_quantity, 2) }}
                                </span>
                            </td>
                            <td>{{ format_currency($inventory->cost_per_unit) }}</td>
                            <td>
                                @if($inventory->expiry_date)
                                    @if($inventory->isExpired())
                                        <span class="badge badge-error">Expired</span>
                                    @elseif($inventory->isExpiringSoon())
                                        <span class="badge badge-warning">{{ $inventory->expiry_date->format('M d, Y') }}</span>
                                    @else
                                        <span class="badge badge-dark">{{ $inventory->expiry_date->format('M d, Y') }}</span>
                                    @endif
                                @else
                                    <span style="color: #94a3b8;">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <p>No inventory records for this food item</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection