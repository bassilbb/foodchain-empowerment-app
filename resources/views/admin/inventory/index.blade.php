@extends('admin.layouts.app')

@section('title', 'Inventory')
@section('page-title', 'Inventory Management')

@section('content')
<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
    <div class="stat-card">
        <p class="stat-label">Total Items</p>
        <p class="stat-value">{{ $inventorySummary['total_items'] }}</p>
    </div>
    <div class="stat-card" style="border-left: 4px solid #10b981;">
        <p class="stat-label">Total Inventory Value</p>
        <p class="stat-value" style="color: #10b981;">{{ format_currency($inventorySummary['total_value']) }}</p>
    </div>
    <div class="stat-card" style="border-left: 4px solid #ef4444;">
        <p class="stat-label">Low Stock Items</p>
        <p class="stat-value" style="color: #ef4444;">{{ $inventorySummary['low_stock_count'] }}</p>
    </div>
</div>

<div style="margin-bottom: 24px; display: flex; gap: 12px;">
    <a href="{{ route('admin.inventory.transactions') }}" class="btn btn-secondary">
        View Transactions
    </a>
    <a href="{{ route('admin.inventory.low-stock') }}" class="btn" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);">
        Low Stock Alert
    </a>
</div>

<div class="grid-2">
    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Add Stock</h3>
        </div>
        <div class="page-card-body">
            <form method="POST" action="{{ route('admin.inventory.add-stock') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Food Item</label>
                    <select name="food_item_id" required class="form-select">
                        <option value="">Select Item</option>
                        @foreach($foodItems as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Location</label>
                    <select name="location_id" required class="form-select">
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" step="0.01" required class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cost Per Unit</label>
                        <input type="number" name="cost_per_unit" step="0.01" required class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea name="notes" rows="2" class="form-input"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Add Stock
                </button>
            </form>
        </div>
    </div>

    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Remove Stock</h3>
        </div>
        <div class="page-card-body">
            <form method="POST" action="{{ route('admin.inventory.remove-stock') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Food Item</label>
                    <select name="food_item_id" required class="form-select">
                        <option value="">Select Item</option>
                        @foreach($foodItems as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Location</label>
                    <select name="location_id" required class="form-select">
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" step="0.01" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Reason</label>
                    <select name="type" required class="form-select">
                        <option value="stock_out">Stock Out</option>
                        <option value="damaged">Damaged</option>
                        <option value="expired">Expired</option>
                        <option value="adjustment">Adjustment</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea name="notes" rows="2" class="form-input"></textarea>
                </div>
                <button type="submit" class="btn" style="width: 100%; background: linear-gradient(135deg, #ef4444, #dc2626); color: white; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);">
                    Remove Stock
                </button>
            </form>
        </div>
    </div>
</div>

<div class="page-card" style="margin-top: 24px;">
    <div class="page-card-header">
        <h3 class="page-card-title">Current Stock Levels</h3>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Location</th>
                <th>Available</th>
                <th>Reserved</th>
                <th>Unit</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($foodItems as $item)
                @foreach($item->inventory as $inv)
                <tr style="{{ $inv->available_quantity < 10 ? 'background: #fef2f2;' : '' }}">
                    <td style="font-weight: 500; color: #1e293b;">{{ $item->name }}</td>
                    <td>{{ $inv->location->name }}</td>
                    <td style="font-weight: 600; color: #1e293b;">{{ number_format($inv->available_quantity, 2) }}</td>
                    <td style="color: #94a3b8;">{{ number_format($inv->reserved_quantity, 2) }}</td>
                    <td>{{ $item->unit }}</td>
                    <td style="font-weight: 500; color: #10b981;">{{ format_currency($inv->available_quantity * $inv->cost_per_unit) }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
@endsection
