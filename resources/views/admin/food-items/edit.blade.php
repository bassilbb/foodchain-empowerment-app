{{-- resources/views/admin/food-items/edit.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Edit Food Item')
@section('page-title', 'Edit: ' . $foodItem->name)
@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.food-items.update', $foodItem) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" value="{{ $foodItem->name }}" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-lg">{{ $foodItem->description }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Unit</label>
                    <input type="text" name="unit" value="{{ $foodItem->unit }}" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Unit Price (₦)</label>
                    <input type="number" name="unit_price" value="{{ $foodItem->unit_price }}" step="0.01" required class="w-full px-3 py-2 border rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Image</label>
                <input type="file" name="image" accept="image/*" class="w-full px-3 py-2 border rounded-lg">
                @if($foodItem->image)
                <p class="text-sm text-gray-500 mt-1">Current image: {{ basename($foodItem->image) }}</p>
                @endif
            </div>
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ $foodItem->is_active ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm">Active</span>
                </label>
            </div>
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update Food Item</button>
                <a href="{{ route('admin.food-items.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
