{{-- resources/views/admin/inventory/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Inventory')
@section('page-title', 'Inventory Management')

@section('content')
<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Total Items</p>
        <p class="text-2xl font-bold">{{ $inventorySummary['total_items'] }}</p>
    </div>
    <div class="bg-green-50 p-6 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Total Inventory Value</p>
        <p class="text-2xl font-bold text-green-600">{{ format_currency($inventorySummary['total_value']) }}</p>
    </div>
    <div class="bg-red-50 p-6 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Low Stock Items</p>
        <p class="text-2xl font-bold text-red-600">{{ $inventorySummary['low_stock_count'] }}</p>
    </div>
</div>

<div class="mb-6">
    <a href="{{ route('admin.inventory.transactions') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
        View Transactions
    </a>
    <a href="{{ route('admin.inventory.low-stock') }}" class="ml-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
        Low Stock Alert
    </a>
</div>

<!-- Add/Remove Stock Forms -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Add Stock -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Add Stock</h3>
        <form method="POST" action="{{ route('admin.inventory.add-stock') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Food Item</label>
                    <select name="food_item_id" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Select Item</option>
                        @foreach($foodItems as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <select name="location_id" required class="w-full px-3 py-2 border rounded-lg">
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <input type="number" name="quantity" step="0.01" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cost Per Unit</label>
                        <input type="number" name="cost_per_unit" step="0.01" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                {{-- class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"> --}}
                    Add Stock
                </button>
            </div>
        </form>
    </div>

    <!-- Remove Stock -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Remove Stock</h3>
        <form method="POST" action="{{ route('admin.inventory.remove-stock') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Food Item</label>
                    <select name="food_item_id" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Select Item</option>
                        @foreach($foodItems as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <select name="location_id" required class="w-full px-3 py-2 border rounded-lg">
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" name="quantity" step="0.01" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                    <select name="type" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="stock_out">Stock Out</option>
                        <option value="damaged">Damaged</option>
                        <option value="expired">Expired</option>
                        <option value="adjustment">Adjustment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Remove Stock
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Current Stock -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold">Current Stock Levels</h3>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Available</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reserved</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($foodItems as $item)
                @foreach($item->inventory as $inv)
                <tr class="hover:bg-gray-50 {{ $inv->available_quantity < 10 ? 'bg-red-50' : '' }}">
                    <td class="px-6 py-4 font-medium">{{ $item->name }}</td>
                    <td class="px-6 py-4">{{ $inv->location->name }}</td>
                    <td class="px-6 py-4 font-semibold">{{ number_format($inv->available_quantity, 2) }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ number_format($inv->reserved_quantity, 2) }}</td>
                    <td class="px-6 py-4">{{ $item->unit }}</td>
                    <td class="px-6 py-4">{{ format_currency($inv->available_quantity * $inv->cost_per_unit) }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
@endsection
