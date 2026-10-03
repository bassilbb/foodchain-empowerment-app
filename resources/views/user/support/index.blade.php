@extends('layouts.user')
@section('title', 'Support Tickets')
@section('header', 'Support Tickets')
@section('subheader', 'View your open and resolved support requests')

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Your Support Tickets</h3>
            <p class="page-card-subtitle">Manage your support requests</p>
        </div>
        <a href="{{ route('support.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Ticket
        </a>
    </div>
    <div class="page-card-body" style="padding: 0;">
        @if($tickets->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                </div>
                <h3 class="empty-title">No support tickets yet</h3>
                <p class="empty-text">Need help? Create a new ticket and we'll get back to you soon.</p>
                <a href="{{ route('support.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    Create Your First Ticket
                </a>
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Subject</th>
                        <th>Created</th>
                        <th>Last Update</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    <tr>
                        <td>
                            @if($ticket->status === 'open')
                                <span class="badge badge-info">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #2563eb; display: inline-block; margin-right: 6px;"></span>
                                    Open
                                </span>
                            @elseif($ticket->status === 'in_progress')
                                <span class="badge badge-warning">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #d97706; display: inline-block; margin-right: 6px;"></span>
                                    In Progress
                                </span>
                            @else
                                <span class="badge badge-success">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #16a34a; display: inline-block; margin-right: 6px;"></span>
                                    Resolved
                                </span>
                            @endif
                        </td>
                        <td style="font-weight: 600; color: #1e293b;">{{ $ticket->subject }}</td>
                        <td style="color: #64748b;">{{ $ticket->created_at->format('M d, Y') }}</td>
                        <td style="color: #64748b;">
                            @if($ticket->last_activity_at)
                                {{ $ticket->last_activity_at->diffForHumans() }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('support.show', $ticket) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($tickets->hasPages())
            <div style="padding: 1.5rem; border-top: 1px solid #f1f5f9;">
                {{ $tickets->links() }}
            </div>
            @endif
        @endif
    </div>
</div>
@endsection