@extends('layouts.user')

@section('title', 'My Incidents')
@section('header', 'My Incidents')
@section('subheader', 'View and report incidents')

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Your Incident Reports</h3>
            <p class="page-card-subtitle">Track and manage your reported incidents</p>
        </div>
        <a href="{{ route('user.incidents.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Report New Incident
        </a>
    </div>
    <div class="page-card-body" style="padding: 0;">
        @if(session('success'))
            <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9;">
                <div class="alert alert-success" style="margin: 0;">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @forelse($incidents as $incident)
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
                        <span class="badge" style="background: {{ $incident->priority_color }}20; color: {{ $incident->priority_color }};">
                            {{ ucfirst($incident->priority) }}
                        </span>
                        <span class="badge" style="background: {{ $incident->status_color }}20; color: {{ $incident->status_color }};">
                            {{ ucfirst(str_replace('_', ' ', $incident->status)) }}
                        </span>
                        <span class="badge badge-info">{{ ucwords(str_replace('_', ' ', $incident->category)) }}</span>
                    </div>
                    <h4 style="font-weight: 600; color: #1e293b; margin: 0 0 0.5rem 0;">
                        <a href="{{ route('user.incidents.show', $incident) }}" style="color: inherit; text-decoration: none;">
                            {{ $incident->title }}
                        </a>
                    </h4>
                    <p style="color: #64748b; font-size: 0.875rem; margin: 0 0 0.75rem 0; max-width: 600px;">{{ Str::limit($incident->description, 150) }}</p>
                    <div style="display: flex; align-items: center; gap: 1.5rem; font-size: 0.75rem; color: #94a3b8;">
                        <span style="display: flex; align-items: center; gap: 0.25rem;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            {{ $incident->branch?->name ?? 'N/A' }}
                        </span>
                        <span style="display: flex; align-items: center; gap: 0.25rem;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $incident->created_at->diffForHumans() }}
                        </span>
                        <span style="display: flex; align-items: center; gap: 0.25rem;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            {{ $incident->comments->count() }} comments
                        </span>
                    </div>
                </div>
                <a href="{{ route('user.incidents.show', $incident) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem; white-space: nowrap;">
                    View Details
                </a>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="empty-title">No incidents reported</h3>
            <p class="empty-text">You haven't reported any incidents yet.</p>
            <a href="{{ route('user.incidents.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                Report an Incident
            </a>
        </div>
        @endforelse
    </div>
</div>

@if($incidents->hasPages())
<div style="padding: 1.5rem;">
    {{ $incidents->links() }}
</div>
@endif
@endsection