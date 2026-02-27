{{-- resources/views/admin/reports/overview.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports Overview')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <a href="{{ route('admin.reports.users') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
        <h3 class="text-lg font-semibold mb-2">User Reports</h3>
        <p class="text-gray-600 text-sm">View user statistics and analytics</p>
    </a>

    <a href="{{ route('admin.reports.payments') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
        <h3 class="text-lg font-semibold mb-2">Payment Reports</h3>
        <p class="text-gray-600 text-sm">Revenue and transaction reports</p>
    </a>

    <a href="{{ route('admin.reports.inventory') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
        <h3 class="text-lg font-semibold mb-2">Inventory Reports</h3>
        <p class="text-gray-600 text-sm">Stock levels and valuation</p>
    </a>

    <a href="{{ route('admin.reports.distributions') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
        <h3 class="text-lg font-semibold mb-2">Distribution Reports</h3>
        <p class="text-gray-600 text-sm">Food distribution analytics</p>
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">Quick Stats</h3>
    <p class="text-gray-600">Select a report type above to view detailed analytics.</p>
</div>
@endsection
