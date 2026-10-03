{{-- resources/views/admin/inventory/low-stock.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Low Stock Alert')
@section('page-title', 'Low Stock Items')
@section('content')
<div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
    <p class="text-red-700"><strong>{{ count($lowStockItems) }} items</strong> are running low on stock and need restocking.</p>
</div>
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current Stock</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($lowStockItems as $item)
            <tr class="bg-red-50">
                <td class="px-6 py-4 font-medium">{{ $item['name'] }}</td>
                <td class="px-6 py-4 font-bold text-red-600">{{ number_format($item['current_stock'], 2) }}</td>
                <td class="px-6 py-4">{{ $item['unit'] }}</td>
                <td class="px-6 py-4">{{ format_currency($item['value']) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">All items have sufficient stock!</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
