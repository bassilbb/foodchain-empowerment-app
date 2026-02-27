{{-- resources/views/admin/packages/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Package Details')
@section('page-title', $package->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Package Details -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-2xl font-bold mb-2">{{ $package->name }}</h3>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 text-sm font-semibold rounded {{ tier_badge_color($package->tier->slug) }} text-white">
                            {{ $package->tier->name }}
                        </span>
                        @if($package->is_default)
                            <span class="px-3 py-1 text-sm font-semibold rounded bg-green-100 text-green-800">Default</span>
                        @endif
                        @if($package->is_active)
                            <span class="px-3 py-1 text-sm font-semibold rounded bg-blue-100 text-blue-800">Active</span>
                        @else
                            <span class="px-3 py-1 text-sm font-semibold rounded bg-gray-100 text-gray-800">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Total Value</p>
                    <p class="text-3xl font-bold text-blue-600">{{ format_currency($package->total_value) }}</p>
                </div>
            </div>

            @if($package->description)
            <div class="mb-6">
                <h4 class="font-medium text-gray-700 mb-2">Description</h4>
                <p class="text-gray-600">{{ $package->description }}</p>
            </div>
            @endif

            <div class="border-t pt-6">
                <h4 class="font-semibold text-lg mb-4">Package Items</h4>
                <div class="overflow-hidden border rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Food Item</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Value</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($package->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $item->foodItem->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $item->foodItem->unit }}</div>
                                </td>
                                <td class="px-6 py-4 text-right font-semibold">
                                    {{ number_format($item->quantity, 2) }} {{ $item->foodItem->unit }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    {{ format_currency($item->unit_value) }}
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-blue-600">
                                    {{ format_currency($item->getTotalValue()) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right font-bold">Total Package Value:</td>
                                <td class="px-6 py-4 text-right font-bold text-blue-600 text-lg">
                                    {{ format_currency($package->total_value) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Distribution History -->
        @if($package->distributions->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h4 class="font-semibold text-lg mb-4">Distribution History</h4>
            <p class="text-sm text-gray-600 mb-4">This package has been distributed {{ $package->distributions->count() }} time(s).</p>
            <div class="space-y-2">
                @foreach($package->distributions->take(5) as $distribution)
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <div>
                        <p class="font-medium">{{ $distribution->user->full_name }}</p>
                        <p class="text-sm text-gray-500">{{ $distribution->distribution_number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm">{{ $distribution->created_at->format('M d, Y') }}</p>
                        <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-800">{{ ucfirst($distribution->status) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Actions Sidebar -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h4 class="font-semibold mb-4">Actions</h4>
            <div class="space-y-3">
                <a href="{{ route('admin.packages.edit', $package) }}"
                   class="block w-full px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700">
                    Edit Package
                </a>

                <a href="{{ route('admin.packages.index') }}"
                   class="block w-full px-4 py-2 bg-gray-200 text-gray-700 text-center rounded-lg hover:bg-gray-300">
                    Back to Packages
                </a>

                @if(!$package->distributions->count())
                <form method="POST" action="{{ route('admin.packages.destroy', $package) }}"
                      onsubmit="return confirm('Are you sure you want to delete this package?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Delete Package
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="bg-blue-50 rounded-lg shadow p-6">
            <h4 class="font-semibold mb-3">Package Statistics</h4>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Total Items</p>
                    <p class="text-2xl font-bold">{{ $package->items->count() }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Times Distributed</p>
                    <p class="text-2xl font-bold">{{ $package->distributions->count() }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Value Distributed</p>
                    <p class="text-lg font-bold">{{ format_currency($package->distributions->count() * $package->total_value) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
