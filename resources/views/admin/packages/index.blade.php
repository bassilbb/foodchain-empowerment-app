{{-- resources/views/admin/packages/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Packages')
@section('page-title', 'Food Packages')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('admin.packages.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        Create Package
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @forelse($packages as $package)
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-lg font-semibold">{{ $package->name }}</h3>
                <span class="px-2 py-1 text-xs font-semibold rounded {{ tier_badge_color($package->tier->slug) }} text-white">
                    {{ $package->tier->name }}
                </span>
                @if($package->is_default)
                    <span class="ml-2 px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">Default</span>
                @endif
            </div>
            <p class="text-2xl font-bold">{{ format_currency($package->total_value) }}</p>
        </div>

        @if($package->description)
        <p class="text-gray-600 text-sm mb-4">{{ $package->description }}</p>
        @endif

        <div class="border-t pt-4">
            <h4 class="font-medium mb-2">Package Items:</h4>
            <ul class="space-y-1 text-sm">
                @foreach($package->items as $item)
                <li class="flex justify-between">
                    <span>{{ $item->foodItem->name }}</span>
                    <span class="text-gray-600">{{ $item->quantity }} {{ $item->foodItem->unit }}</span>
                </li>
                @endforeach
            </ul>
        </div>

        <div class="mt-4 flex gap-2">
            <a href="{{ route('admin.packages.edit', $package) }}" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded text-center hover:bg-blue-700">
                Edit
            </a>
            <a href="{{ route('admin.packages.show', $package) }}" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded text-center hover:bg-gray-300">
                View
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-2 text-center py-12 text-gray-500">
        No packages found. Create your first package!
    </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $packages->links() }}
</div>
@endsection
