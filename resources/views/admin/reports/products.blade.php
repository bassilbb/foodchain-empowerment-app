@extends('admin.layouts.app')

@section('title', 'Products Report')
@section('header', 'Products Report')
@section('subheader', 'What is selling, what earns the most, and what is running out.')

@section('content')
<div class="page-header">
    <div>
        <h1>Products</h1>
        <p>{{ number_format($activeCount) }} active, {{ number_format($inactiveCount) }} inactive.</p>
    </div>
    <div class="header-actions-btns">
        @foreach($ranges as $range)
            <a href="{{ route('admin.reports.products', ['days' => $range]) }}"
               class="btn {{ $days === $range ? 'btn-primary' : 'btn-secondary' }}">{{ $range }}d</a>
        @endforeach
    </div>
</div>

<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card accent">
        <div class="stat-icon emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
        </div>
        <p class="stat-label">Inventory Value</p>
        <p class="stat-value">{{ format_currency($inventoryValue) }}</p>
    </div>
    <div class="stat-card danger">
        <div class="stat-icon red">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-5l-1 3h-4l-1-3H4"></path></svg>
        </div>
        <p class="stat-label">Out of Stock</p>
        <p class="stat-value">{{ number_format($outOfStock) }}</p>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0l-7 12a2 2 0 001.74 3z"></path></svg>
        </div>
        <p class="stat-label">Low Stock</p>
        <p class="stat-value">{{ number_format($lowStockCount) }}</p>
    </div>
    <div class="stat-card info">
        <div class="stat-icon indigo">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"></path></svg>
        </div>
        <p class="stat-label">Categories Selling</p>
        <p class="stat-value">{{ number_format($topCategories->count()) }}</p>
    </div>
</div>

<div class="grid-2" style="margin-bottom: 2rem;">
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Best Sellers</h3>
                <p class="page-card-subtitle">By units sold in the last {{ $days }} days</p>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Units</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bestSellers as $row)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #1e293b;">{{ $row->name }}</div>
                        <div style="font-size: 12px; color: #94a3b8;">{{ $row->sku }}</div>
                    </td>
                    <td style="font-weight: 700; color: #059669;">{{ number_format($row->units) }}</td>
                    <td>{{ format_currency($row->revenue) }}</td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="empty-state">No sales in this period.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Revenue Leaders</h3>
                <p class="page-card-subtitle">Highest earning products</p>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Units</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($revenueLeaders as $row)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #1e293b;">{{ $row->name }}</div>
                        <div style="font-size: 12px; color: #94a3b8;">{{ $row->sku }}</div>
                    </td>
                    <td>{{ number_format($row->units) }}</td>
                    <td style="font-weight: 700; color: #059669;">{{ format_currency($row->revenue) }}</td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="empty-state">No sales in this period.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="grid-2">
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Low Stock</h3>
                <p class="page-card-subtitle">At or below the reorder threshold</p>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Vendor</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lowStock as $product)
                <tr>
                    <td>
                        <a href="{{ route('admin.marketplace.products.show', $product) }}" class="action-link" style="font-weight: 600;">{{ $product->name }}</a>
                    </td>
                    <td>{{ $product->vendor->business_name ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $product->stock_quantity == 0 ? 'badge-error' : 'badge-warning' }}">
                            {{ number_format($product->stock_quantity) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="empty-state">Stock levels are healthy.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Category Performance</h3>
                <p class="page-card-subtitle">Ranked by revenue, not product count</p>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Units</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topCategories as $row)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #1e293b;">{{ $row->icon ? $row->icon.' ' : '' }}{{ $row->name }}</div>
                    </td>
                    <td>{{ number_format($row->units) }}</td>
                    <td style="font-weight: 700; color: #059669;">{{ format_currency($row->revenue) }}</td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="empty-state">No sales in this period.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
