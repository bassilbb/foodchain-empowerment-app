{{-- resources/views/admin/food-items/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Food Items')
@section('page-title', 'Food Items Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('admin.food-items.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        Add Food Item
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($foodItems as $item)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="font-medium">{{ $item->name }}</div>
                    @if($item->description)
                    <div class="text-sm text-gray-500">{{ Str::limit($item->description, 50) }}</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $item->unit }}</td>
                <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ format_currency($item->unit_price) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $item->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $item->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="{{ route('admin.food-items.edit', $item) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No food items found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $foodItems->links() }}
</div>
@endsection
