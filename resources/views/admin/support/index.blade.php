@extends('admin.layouts.app')

@section('title', 'Support Tickets')
@section('header', 'Support Tickets')
@section('subheader', 'Manage support ticket inquiries')

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">All Tickets</h3>
            <p class="page-card-subtitle">{{ $tickets->total() }} total tickets</p>
        </div>
    </div>
    <div class="page-card-body" style="padding: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Ticket #</th>
                    <th>Subject</th>
                    <th>User</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr>
                    <td style="font-family: monospace; font-size: 13px; color: #1e293b; font-weight: 500;">{{ $ticket->ticket_number }}</td>
                    <td>
                        <a href="{{ route('admin.support.show', $ticket) }}" style="color: #1e293b; text-decoration: none; font-weight: 500;">
                            {{ Str::limit($ticket->subject, 40) }}
                        </a>
                    </td>
                    <td>
                        <div style="font-weight: 500; color: #1e293b;">{{ $ticket->user?->full_name ?? 'N/A' }}</div>
                        <div style="font-size: 12px; color: #94a3b8;">{{ $ticket->user?->email ?? '' }}</div>
                    </td>
                    <td>
                        <span class="badge
                            @if($ticket->priority === 'urgent') badge-error
                            @elseif($ticket->priority === 'high') badge-warning
                            @elseif($ticket->priority === 'medium') badge-info
                            @else badge-dark
                            @endif">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge
                            @if($ticket->status === 'open') badge-info
                            @elseif($ticket->status === 'pending') badge-warning
                            @elseif($ticket->status === 'resolved') badge-success
                            @else badge-dark
                            @endif">
                            {{ ucfirst($ticket->status) }}
                        </span>
                    </td>
                    <td style="color: #94a3b8; font-size: 13px;">{{ $ticket->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('admin.support.show', $ticket) }}" class="action-link">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <p>No support tickets found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination">
    {{ $tickets->links() }}
</div>
@endsection
