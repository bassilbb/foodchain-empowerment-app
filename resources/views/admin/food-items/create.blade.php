{{-- resources/views/admin/food-items/create.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Add Food Item')
@section('page-title', 'Add New Food Item')
@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.food-items.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Unit</label>
                    <input type="text" name="unit" required placeholder="kg, liters, pack" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Unit Price (₦)</label>
                    <input type="number" name="unit_price" step="0.01" required class="w-full px-3 py-2 border rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Image (Optional)</label>
                <input type="file" name="image" accept="image/*" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Create Food Item</button>
                <a href="{{ route('admin.food-items.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
