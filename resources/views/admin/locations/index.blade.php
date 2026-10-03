@extends('admin.layouts.app')

@section('title', 'Locations')
@section('page-title', 'Pickup Locations')

@section('content')
<div style="display: flex; justify-content: flex-end; margin-bottom: 24px;">
    <a href="{{ route('admin.locations.create') }}" class="btn btn-primary">Add Location</a>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 24px;">
    @forelse($locations as $location)
    <div class="page-card">
        <div class="page-card-body">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0;">{{ $location->name }}</h3>
                @if($location->is_headquarters)
                    <span class="badge badge-primary">HQ</span>
                @endif
            </div>

            <div style="margin-bottom: 16px;">
                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #64748b; margin-bottom: 8px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; color: #10b981;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>{{ $location->address }}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #64748b; margin-bottom: 8px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; color: #10b981;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                    <span>{{ $location->city }}, {{ $location->state }}</span>
                </div>
                @if($location->phone)
                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #64748b; margin-bottom: 8px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; color: #10b981;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <span>{{ $location->phone }}</span>
                </div>
                @endif
                @if($location->email)
                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #64748b; margin-bottom: 8px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; color: #10b981;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>{{ $location->email }}</span>
                </div>
                @endif
                @if($location->operating_hours)
                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #64748b; margin-bottom: 8px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; color: #10b981;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $location->operating_hours }}</span>
                </div>
                @endif
            </div>

            <div style="margin-bottom: 16px;">
                <span class="badge badge-{{ $location->is_active ? 'success' : 'error' }}">
                    {{ $location->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <a href="{{ route('admin.locations.edit', $location) }}" class="btn btn-primary" style="display: block; text-align: center;">Edit</a>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <p>No locations found. Add your first location!</p>
    </div>
    @endforelse
</div>

<div class="pagination">{{ $locations->links() }}</div>
@endsection
