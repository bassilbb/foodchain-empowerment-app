{{-- resources/views/admin/distributions/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Distributions')
@section('page-title', 'Food Distributions')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('admin.distributions.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        Create Distribution
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tier</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($distributions as $distribution)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">
                    {{ $distribution->distribution_number }}
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium">{{ $distribution->user->full_name }}</div>
                    <div class="text-sm text-gray-500">{{ $distribution->user->email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded {{ tier_badge_color($distribution->tier->slug) }} text-white">
                        {{ $distribution->tier->name }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap font-semibold">
                    {{ format_currency($distribution->package_value) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                        {{ ucfirst($distribution->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $distribution->created_at->format('M d, Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="{{ route('admin.distributions.show', $distribution) }}" class="text-blue-600 hover:text-blue-900">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No distributions found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $distributions->links() }}
</div>
@endsection
