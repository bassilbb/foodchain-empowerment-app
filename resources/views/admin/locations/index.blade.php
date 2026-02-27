{{-- resources/views/admin/locations/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Locations')
@section('page-title', 'Pickup Locations')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('admin.locations.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        Add Location
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($locations as $location)
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-lg font-semibold">{{ $location->name }}</h3>
            @if($location->is_headquarters)
                <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">HQ</span>
            @endif
        </div>

        <div class="space-y-2 text-sm">
            <p class="text-gray-600">{{ $location->address }}</p>
            <p class="text-gray-600">{{ $location->city }}, {{ $location->state }}</p>
            @if($location->phone)
            <p class="text-gray-600">📞 {{ $location->phone }}</p>
            @endif
            @if($location->email)
            <p class="text-gray-600">✉️ {{ $location->email }}</p>
            @endif
            @if($location->operating_hours)
            <p class="text-gray-600">🕐 {{ $location->operating_hours }}</p>
            @endif
        </div>

        <div class="mt-4 pt-4 border-t">
            <span class="px-2 py-1 text-xs font-semibold rounded {{ $location->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $location->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <div class="mt-4 flex gap-2">
            <a href="{{ route('admin.locations.edit', $location) }}" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded text-center hover:bg-blue-700">
                Edit
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-3 text-center py-12 text-gray-500">
        No locations found. Add your first location!
    </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $locations->links() }}
</div>
@endsection
