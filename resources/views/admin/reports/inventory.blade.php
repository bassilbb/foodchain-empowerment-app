{{-- resources/views/admin/reports/inventory.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Inventory Reports')
@section('page-title', 'Inventory Reports & Stock Analysis')

@section('content')
<style>
    .stat-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); padding: 24px; transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 20px 50px -12px rgba(16, 185, 129, 0.2); }
    .stat-card.green { border-left: 4px solid #10b981; }
    .stat-card.red { border-left: 4px solid #ef4444; }
    .stat-value { font-size: 32px; font-weight: 700; color: #1e293b; }
    .stat-card.green .stat-value { color: #10b981; }
    .stat-card.red .stat-value { color: #ef4444; }
    .stat-label { font-size: 14px; color: #64748b; margin-top: 4px; }
    .alert-card { background: linear-gradient(135deg, #fef2f2, #fee2e2); border-left: 4px solid #ef4444; border-radius: 12px; padding: 16px; margin-bottom: 24px; }
    .alert-icon { color: #ef4444; }
    .alert-text { color: #991b1b; font-size: 14px; }
    .alert-link { color: #dc2626; font-weight: 600; text-decoration: underline; }
    .alert-link:hover { color: #b91c1c; }
    .table-container { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 24px; }
    .table-header { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border-bottom: 1px solid #e2e8f0; }
    .table-header th { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px; text-align: left; }
    .table-row { border-bottom: 1px solid #f1f5f9; transition: all 0.2s ease; }
    .table-row:hover { background: linear-gradient(180deg, #f0fdf4, #dcfce7); }
    .table-row.low-stock { background: linear-gradient(180deg, #fef2f2, #fee2e2); }
    .table-cell { padding: 16px; color: #475569; font-size: 14px; }
    .item-name { font-weight: 600; color: #1e293b; }
    .item-desc { font-size: 13px; color: #94a3b8; }
    .stock-value { font-size: 24px; font-weight: 700; color: #1e293b; }
    .stock-value.low { color: #ef4444; }
    .stock-value.ok { color: #10b981; }
    .value-text { font-size: 18px; font-weight: 600; color: #10b981; }
    .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-low { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b; }
    .badge-ok { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; }
    .stock-in { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; }
    .stock-out { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b; }
    .quantity-text { font-size: 18px; font-weight: 600; }
    .quantity-text.in { color: #10b981; }
    .quantity-text.out { color: #ef4444; }
    .total-row { background: linear-gradient(180deg, #f8fafc, #f1f5f9); }
    .total-label { font-weight: 700; color: #1e293b; }
    .total-value { font-weight: 700; color: #10b981; font-size: 20px; }
</style>

<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 32px;">
    <div class="stat-card">
        <p class="stat-label">Total Food Items</p>
        <p class="stat-value">{{ $foodItems->count() }}</p>
    </div>
    <div class="stat-card green">
        <p class="stat-label">Total Inventory Value</p>
        <p class="stat-value">{{ format_currency($totalInventoryValue) }}</p>
    </div>
    <div class="stat-card red">
        <p class="stat-label">Low Stock Items</p>
        <p class="stat-value">{{ $lowStockItems->count() }}</p>
    </div>
</div>

@if($lowStockItems->count() > 0)
<div class="alert-card">
    <div style="display: flex; gap: 12px;">
        <div class="alert-icon">
            <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        </div>
        <div class="alert-text">
            <strong>{{ $lowStockItems->count() }} items</strong> are running low on stock and need immediate restocking.
            <a href="{{ route('admin.inventory.low-stock') }}" class="alert-link">View details →</a>
        </div>
    </div>
</div>
@endif

<div class="table-container">
    <div style="padding: 16px 24px; border-bottom: 1px solid #e2e8f0; background: linear-gradient(180deg, #f8fafc, #f1f5f9);">
        <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">Current Stock Levels by Item</h3>
    </div>
    <table style="width: 100%; border-collapse: collapse;">
        <thead class="table-header">
            <tr>
                <th>Food Item</th>
                <th style="text-align: center;">Total Stock</th>
                <th style="text-align: center;">Unit</th>
                <th style="text-align: right;">Stock Value</th>
                <th style="text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($foodItems as $item)
            <tr class="table-row {{ $item->isLowStock() ? 'low-stock' : '' }}">
                <td class="table-cell">
                    <div class="item-name">{{ $item->name }}</div>
                    @if($item->description)
                    <div class="item-desc">{{ Str::limit($item->description, 50) }}</div>
                    @endif
                </td>
                <td class="table-cell" style="text-align: center;">
                    <span class="stock-value {{ $item->isLowStock() ? 'low' : 'ok' }}">{{ number_format($item->getTotalStock(), 2) }}</span>
                </td>
                <td class="table-cell" style="text-align: center; color: #64748b;">{{ $item->unit }}</td>
                <td class="table-cell" style="text-align: right;"><span class="value-text">{{ format_currency($item->getTotalStockValue()) }}</span></td>
                <td class="table-cell" style="text-align: center;">
                    @if($item->isLowStock())
                        <span class="badge badge-low">Low Stock</span>
                    @else
                        <span class="badge badge-ok">Sufficient</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="total-row">
            <tr>
                <td colspan="3" class="table-cell total-label" style="text-align: right; padding: 20px;">Total Inventory Value:</td>
                <td class="table-cell total-value" style="text-align: right; padding: 20px;">{{ format_currency($totalInventoryValue) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="table-container">
    <div style="padding: 16px 24px; border-bottom: 1px solid #e2e8f0; background: linear-gradient(180deg, #f8fafc, #f1f5f9);">
        <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">Recent Inventory Transactions (Last 50)</h3>
    </div>
    <table style="width: 100%; border-collapse: collapse;">
        <thead class="table-header">
            <tr>
                <th>Date & Time</th>
                <th>Item</th>
                <th>Type</th>
                <th style="text-align: center;">Quantity</th>
                <th>Location</th>
                <th>Performed By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentTransactions as $transaction)
            <tr class="table-row">
                <td class="table-cell" style="color: #64748b;">{{ $transaction->created_at->format('M d, Y h:i A') }}</td>
                <td class="table-cell" style="font-weight: 500;">{{ $transaction->foodItem->name }}</td>
                <td class="table-cell">
                    <span class="badge {{ $transaction->type === 'stock_in' ? 'stock-in' : 'stock-out' }}">{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</span>
                </td>
                <td class="table-cell" style="text-align: center;">
                    <span class="quantity-text {{ $transaction->type === 'stock_in' ? 'in' : 'out' }}">
                        {{ $transaction->type === 'stock_in' ? '+' : '-' }}{{ number_format($transaction->quantity, 2) }}
                    </span>
                </td>
                <td class="table-cell">{{ $transaction->location->name }}</td>
                <td class="table-cell" style="color: #64748b;">{{ $transaction->performedBy->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection