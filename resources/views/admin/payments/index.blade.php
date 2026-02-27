{{-- resources/views/admin/payments/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Payments')
@section('page-title', 'Payment Management')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Total Revenue</p>
        <p class="text-2xl font-bold">{{ format_currency($totalAmount) }}</p>
    </div>
    <div class="bg-green-50 p-6 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Successful</p>
        <p class="text-2xl font-bold text-green-600">{{ $successfulCount }}</p>
    </div>
    <div class="bg-yellow-50 p-6 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</p>
    </div>
    <div class="bg-red-50 p-6 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Failed</p>
        <p class="text-2xl font-bold text-red-600">{{ $failedCount }}</p>
    </div>
</div>

<!-- Filters -->
<div class="mb-6 bg-white p-4 rounded-lg shadow">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
               class="px-4 py-2 border rounded-lg">

        <select name="type" class="px-4 py-2 border rounded-lg">
            <option value="">All Types</option>
            <option value="membership" {{ request('type') == 'membership' ? 'selected' : '' }}>Membership</option>
            <option value="starter_pack" {{ request('type') == 'starter_pack' ? 'selected' : '' }}>Starter Pack</option>
        </select>

        <select name="status" class="px-4 py-2 border rounded-lg">
            <option value="">All Status</option>
            <option value="successful" {{ request('status') == 'successful' ? 'selected' : '' }}>Successful</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
        </select>

        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Filter
        </button>
        <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-center">
            Reset
        </a>
    </form>
</div>

<!-- Payments Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($payments as $payment)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-mono text-gray-900">{{ $payment->reference }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ $payment->user->full_name }}</div>
                    <div class="text-sm text-gray-500">{{ $payment->user->email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    {{ ucfirst(str_replace('_', ' ', $payment->type)) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                    {{ format_currency($payment->amount) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded
                        {{ $payment->status === 'successful' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $payment->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $payment->created_at->format('M d, Y h:i A') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="{{ route('admin.payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No payments found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $payments->links() }}
</div>
@endsection
