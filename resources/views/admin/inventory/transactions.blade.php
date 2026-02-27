{{-- resources/views/admin/inventory/transactions.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Inventory Transactions')
@section('page-title', 'Inventory Transaction History')
@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Performed By</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($transactions as $transaction)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $transaction->created_at->format('M d, Y h:i A') }}</td>
                <td class="px-6 py-4">{{ $transaction->foodItem->name }}</td>
                <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded bg-blue-100">{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</span></td>
                <td class="px-6 py-4 font-semibold">{{ number_format($transaction->quantity, 2) }}</td>
                <td class="px-6 py-4">{{ $transaction->location->name }}</td>
                <td class="px-6 py-4 text-sm">{{ $transaction->performedBy->name }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No transactions found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $transactions->links() }}</div>
@endsection
