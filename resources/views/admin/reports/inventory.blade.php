{{-- resources/views/admin/reports/inventory.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Inventory Reports')
@section('page-title', 'Inventory Reports & Stock Analysis')

@section('content')
<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Total Food Items</p>
        <p class="text-3xl font-bold text-gray-800">{{ $foodItems->count() }}</p>
    </div>
    <div class="bg-green-50 p-6 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Total Inventory Value</p>
        <p class="text-3xl font-bold text-green-600">{{ format_currency($totalInventoryValue) }}</p>
    </div>
    <div class="bg-red-50 p-6 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Low Stock Items</p>
        <p class="text-3xl font-bold text-red-600">{{ $lowStockItems->count() }}</p>
    </div>
</div>

<!-- Low Stock Alert -->
@if($lowStockItems->count() > 0)
<div class="bg-red-50 border-l-4 border-red-400 p-4 mb-8">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-red-700">
                <strong>{{ $lowStockItems->count() }} items</strong> are running low on stock and need immediate restocking.
                <a href="{{ route('admin.inventory.low-stock') }}" class="font-medium underline hover:text-red-600">View details →</a>
            </p>
        </div>
    </div>
</div>
@endif

<!-- Current Stock Levels -->
<div class="bg-white rounded-lg shadow overflow-hidden mb-8">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold">Current Stock Levels by Item</h3>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Food Item</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Stock</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Unit</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stock Value</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($foodItems as $item)
            <tr class="hover:bg-gray-50 {{ $item->isLowStock() ? 'bg-red-50' : '' }}">
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">{{ $item->name }}</div>
                    @if($item->description)
                    <div class="text-sm text-gray-500">{{ Str::limit($item->description, 50) }}</div>
                    @endif
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="text-2xl font-bold {{ $item->isLowStock() ? 'text-red-600' : 'text-gray-900' }}">
                        {{ number_format($item->getTotalStock(), 2) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center text-sm text-gray-600">
                    {{ $item->unit }}
                </td>
                <td class="px-6 py-4 text-right">
                    <span class="text-lg font-semibold text-green-600">
                        {{ format_currency($item->getTotalStockValue()) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    @if($item->isLowStock())
                        <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
                            Low Stock
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                            Sufficient
                        </span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-gray-50">
            <tr>
                <td colspan="3" class="px-6 py-4 text-right font-bold">Total Inventory Value:</td>
                <td class="px-6 py-4 text-right font-bold text-green-600 text-xl">
                    {{ format_currency($totalInventoryValue) }}
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Recent Transactions -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold">Recent Inventory Transactions (Last 50)</h3>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Performed By</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($recentTransactions as $transaction)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    {{ $transaction->created_at->format('M d, Y h:i A') }}
                </td>
                <td class="px-6 py-4 font-medium">
                    {{ $transaction->foodItem->name }}
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs font-semibold rounded
                        {{ $transaction->type === 'stock_in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="text-lg font-semibold {{ $transaction->type === 'stock_in' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $transaction->type === 'stock_in' ? '+' : '-' }}{{ number_format($transaction->quantity, 2) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm">
                    {{ $transaction->location->name }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $transaction->performedBy->name }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
