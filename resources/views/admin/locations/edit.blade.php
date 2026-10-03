{{-- resources/views/admin/locations/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Edit Location')
@section('page-title', 'Edit Location: ' . $location->name)

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.locations.update', $location) }}">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location Name *</label>
                <input type="text" name="name" value="{{ $location->name }}" required
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                <textarea name="address" rows="3" required
                          class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $location->address }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                    <input type="text" name="city" value="{{ $location->city }}" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                    <input type="text" name="state" value="{{ $location->state }}" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ $location->phone }}"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ $location->email }}"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Operating Hours</label>
                <input type="text" name="operating_hours" value="{{ $location->operating_hours }}"
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex gap-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ $location->is_active ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm font-medium">Active</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="is_headquarters" value="1" {{ $location->is_headquarters ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm font-medium">Mark as Headquarters</span>
                </label>
            </div>

            <div class="flex gap-4 pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Update Location
                </button>
                <a href="{{ route('admin.locations.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
