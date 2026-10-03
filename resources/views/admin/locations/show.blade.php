@extends('admin.layouts.app')

@section('title', $location->name)
@section('header', $location->name)
@section('subheader', 'Pickup location details')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary btn-sm">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Locations
    </a>
    <a href="{{ route('admin.locations.edit', $location) }}" class="btn btn-primary btn-sm">Edit Location</a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon emerald"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg></div>
        <div class="stat-label">Inventory Records</div>
        <div class="stat-value">{{ $location->inventory->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon sky"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg></div>
        <div class="stat-label">Distributions</div>
        <div class="stat-value">{{ $location->distributions->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
        <div class="stat-label">Total Inventory Value</div>
        <div class="stat-value" style="font-size: 1.5rem;">{{ format_currency($location->getTotalInventoryValue()) }}</div>
    </div>
    <div class="stat-card {{ $location->is_active ? 'success-card' : 'danger' }}">
        <div class="stat-icon {{ $location->is_active ? 'emerald' : 'red' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
        <div class="stat-label">Status</div>
        <div class="stat-value" style="font-size: 1.5rem;">{{ $location->is_active ? 'Active' : 'Inactive' }}</div>
    </div>
</div>

<div class="grid-2" style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Location Details</h3>
            @if($location->is_headquarters)
            <span class="badge badge-primary">Headquarters</span>
            @endif
        </div>
        <div class="page-card-body">
            <table style="width: 100%;">
                <tr><td style="padding: 0.5rem 0; color: #64748b; font-size: 0.875rem;">Address</td><td style="padding: 0.5rem 0; font-weight: 600; color: #1e293b;">{{ $location->address }}</td></tr>
                <tr><td style="padding: 0.5rem 0; color: #64748b; font-size: 0.875rem;">City</td><td style="padding: 0.5rem 0;">{{ $location->city }}</td></tr>
                <tr><td style="padding: 0.5rem 0; color: #64748b; font-size: 0.875rem;">State</td><td style="padding: 0.5rem 0;">{{ $location->state }}</td></tr>
                <tr><td style="padding: 0.5rem 0; color: #64748b; font-size: 0.875rem;">Phone</td><td style="padding: 0.5rem 0;">{{ $location->phone ?? '-' }}</td></tr>
                <tr><td style="padding: 0.5rem 0; color: #64748b; font-size: 0.875rem;">Email</td><td style="padding: 0.5rem 0;">{{ $location->email ?? '-' }}</td></tr>
                <tr><td style="padding: 0.5rem 0; color: #64748b; font-size: 0.875rem;">Operating Hours</td><td style="padding: 0.5rem 0;">{{ $location->operating_hours ?? '-' }}</td></tr>
            </table>
        </div>
    </div>

    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Inventory at Location</h3>
            <span class="badge badge-info">{{ $location->inventory->count() }} records</span>
        </div>
        <div style="padding: 0;">
            @if($location->inventory->count())
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Food Item</th>
                            <th>Quantity</th>
                            <th>Available</th>
                            <th>Cost / Unit</th>
                            <th>Expiry</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($location->inventory as $inventory)
                        <tr>
                            <td style="font-weight: 600; color: #1e293b;">{{ $inventory->foodItem?->name ?? 'Unknown' }}</td>
                            <td>{{ number_format($inventory->quantity, 2) }}</td>
                            <td>
                                <span class="badge {{ $inventory->available_quantity < 10 ? 'badge-error' : 'badge-success' }}">
                                    {{ number_format($inventory->available_quantity, 2) }}
                                </span>
                            </td>
                            <td>{{ format_currency($inventory->cost_per_unit) }}</td>
                            <td>
                                @if($inventory->expiry_date)
                                    @if($inventory->isExpired())
                                        <span class="badge badge-error">Expired</span>
                                    @elseif($inventory->isExpiringSoon())
                                        <span class="badge badge-warning">{{ $inventory->expiry_date->format('M d, Y') }}</span>
                                    @else
                                        <span class="badge badge-dark">{{ $inventory->expiry_date->format('M d, Y') }}</span>
                                    @endif
                                @else
                                    <span style="color: #94a3b8;">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <p>No inventory at this location</p>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">Distribution History</h3>
        <span class="badge badge-info">{{ $location->distributions->count() }} total</span>
    </div>
    <div style="padding: 0;">
        @if($location->distributions->count())
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Distribution #</th>
                        <th>Member</th>
                        <th>Package Value</th>
                        <th>Scheduled</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($location->distributions->sortByDesc('created_at') as $distribution)
                    <tr>
                        <td style="font-weight: 600; color: #1e293b;">{{ $distribution->distribution_number }}</td>
                        <td>{{ $distribution->user?->full_name ?? $distribution->user?->name ?? '—' }}</td>
                        <td>{{ format_currency($distribution->package_value) }}</td>
                        <td>{{ $distribution->scheduled_date?->format('M d, Y') ?? '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $distribution->status === 'delivered' ? 'success' : ($distribution->status === 'pending' ? 'warning' : ($distribution->status === 'cancelled' ? 'error' : 'info')) }}">
                                {{ ucfirst($distribution->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <p>No distribution history for this location</p>
        </div>
        @endif
    </div>
</div>
@endsection