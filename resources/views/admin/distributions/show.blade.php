{{-- resources/views/admin/distributions/show.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Distribution Details')
@section('page-title', 'Distribution: ' . $distribution->distribution_number)
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Distribution Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="text-sm text-gray-500">Distribution Number</label><p class="font-mono font-medium">{{ $distribution->distribution_number }}</p></div>
                <div><label class="text-sm text-gray-500">Status</label><p><span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">{{ ucfirst($distribution->status) }}</span></p></div>
                <div><label class="text-sm text-gray-500">Type</label><p class="font-medium">{{ ucfirst(str_replace('_', ' ', $distribution->distribution_type)) }}</p></div>
                <div><label class="text-sm text-gray-500">Package Value</label><p class="font-bold text-lg">{{ format_currency($distribution->package_value) }}</p></div>
                <div><label class="text-sm text-gray-500">Delivery Method</label><p class="font-medium">{{ ucfirst($distribution->delivery_method) }}</p></div>
                @if($distribution->scheduled_date)
                <div><label class="text-sm text-gray-500">Scheduled Date</label><p>{{ $distribution->scheduled_date->format('M d, Y') }}</p></div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Food Items</h3>
            <table class="min-w-full">
                <thead><tr class="border-b"><th class="text-left py-2">Item</th><th class="text-right py-2">Quantity</th><th class="text-right py-2">Value</th></tr></thead>
                <tbody>
                    @foreach($distribution->items as $item)
                    <tr class="border-b"><td class="py-2">{{ $item->foodItem->name }}</td><td class="text-right">{{ $item->quantity }} {{ $item->foodItem->unit }}</td><td class="text-right">{{ format_currency($item->total_value) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">User Details</h3>
            <p class="font-medium">{{ $distribution->user->full_name }}</p>
            <p class="text-sm text-gray-600">{{ $distribution->user->email }}</p>
            <p class="text-sm text-gray-600">{{ $distribution->user->phone }}</p>
        </div>

        @if($distribution->isPending())
        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('admin.distributions.approve', $distribution) }}">
                @csrf
                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Approve Distribution</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
