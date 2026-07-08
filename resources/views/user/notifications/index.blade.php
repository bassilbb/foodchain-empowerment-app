@extends('layouts.user')

@section('title', 'Notifications')
@section('header', 'Notifications')
@section('subheader', 'Stay updated')

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Your Notifications</h3>
            <p class="page-card-subtitle">Stay updated with the latest activities</p>
        </div>
        @if(auth()->user()->notifications()->where('is_read', false)->exists())
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Mark All as Read
            </button>
        </form>
        @endif
    </div>
    <div class="page-card-body" style="padding: 0;">
        @forelse($notifications as $notification)
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; {{ !$notification->is_read ? 'background: #f0fdf4;' : 'background: white;' }}">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="display: flex; align-items: flex-start; gap: 1rem; flex: 1;">
                    @if(!$notification->is_read)
                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981; margin-top: 6px; flex-shrink: 0;"></span>
                    @else
                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #cbd5e1; margin-top: 6px; flex-shrink: 0;"></span>
                    @endif
                    <div style="flex: 1;">
                        <h4 style="font-weight: {{ !$notification->is_read ? '700' : '500' }}; color: #1e293b; margin: 0 0 0.5rem 0; font-size: 0.95rem;">
                            {{ $notification->title }}
                        </h4>
                        @if($notification->message)
                        <p style="color: #64748b; margin: 0 0 0.5rem 0; font-size: 0.875rem;">{{ $notification->message }}</p>
                        @endif
                        <span style="font-size: 0.75rem; color: #94a3b8;">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem; margin-left: 1rem;">
                    @if(!$notification->is_read)
                    <form action="{{ route('notifications.read', $notification) }}" method="POST">
                        @csrf
                        <button type="submit" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 8px; background: white; cursor: pointer; color: #64748b; transition: all 0.2s;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('notifications.destroy', $notification) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="padding: 0.5rem; border: 1px solid #fecaca; border-radius: 8px; background: #fef2f2; cursor: pointer; color: #dc2626; transition: all 0.2s;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
            @if($notification->action_url)
            <div style="margin-top: 1rem; margin-left: 1.5rem;">
                <a href="{{ $notification->action_url }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    View Details
                </a>
            </div>
            @endif
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon" style="background: #f1f5f9;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </div>
            <h3 class="empty-title">No notifications</h3>
            <p class="empty-text">You're all caught up!</p>
        </div>
        @endforelse
    </div>
</div>

@if($notifications->hasPages())
<div style="padding: 1rem 0;">
    {{ $notifications->links() }}
</div>
@endif
@endsection