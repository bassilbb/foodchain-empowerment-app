{{-- resources/views/admin/support/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Support Ticket')
@section('page-title', 'Ticket: ' . $ticket->ticket_number)

@section('content')
<div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; @media(min-width: 992px) { grid-template-columns: 2fr 1fr; }">
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="page-card">
            <div class="page-card-body">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">{{ $ticket->subject }}</h3>
                        <div style="display: flex; gap: 0.5rem;">
                            <span style="padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600;
                                @if($ticket->priority === 'urgent') background: #fee2e2; color: #dc2626; @endif
                                @if($ticket->priority === 'high') background: #ffedd5; color: #ea580c; @endif
                                @if($ticket->priority === 'medium') background: #fef3c7; color: #d97706; @endif
                                @if($ticket->priority === 'low') background: #f3f4f6; color: #4b5563; @endif">
                                {{ ucfirst($ticket->priority) }} Priority
                            </span>
                            <span style="padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600;
                                @if($ticket->status === 'open') background: #dbeafe; color: #2563eb; @endif
                                @if($ticket->status === 'in_progress') background: #fef3c7; color: #d97706; @endif
                                @if($ticket->status === 'resolved') background: #dcfce7; color: #16a34a; @endif
                                @if($ticket->status === 'closed') background: #f3f4f6; color: #4b5563; @endif">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </div>
                    </div>
                    <p style="font-size: 0.875rem; color: #64748b;">{{ $ticket->created_at->format('M d, Y h:i A') }}</p>
                </div>

                <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem;">
                    <p style="color: #475569; white-space: pre-wrap;">{{ $ticket->message }}</p>
                    @if($ticket->attachment_path)
                    <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #e2e8f0;">
                        <a href="{{ asset('storage/'.$ticket->attachment_path) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background: white; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; font-size: 0.8rem; color: #2563eb; font-weight: 500;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            View Attachment
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="page-card">
            <div class="page-card-header">
                <h4 style="font-weight: 600; font-size: 1.125rem; margin-bottom: 1rem;">Conversation</h4>
            </div>
            <div class="page-card-body">
                <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                    @forelse($ticket->replies as $reply)
                    <div style="display: flex; justify-content: {{ $reply->isFromAdmin() ? 'flex-end' : 'flex-start' }};">
                        <div style="max-width: 48rem; padding: 1rem; border-radius: 0.5rem; {{ $reply->isFromAdmin() ? 'background: #dbeafe;' : 'background: #f8fafc;' }}">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                <p style="font-weight: 500; font-size: 0.875rem; {{ $reply->isFromAdmin() ? 'color: #1e3a8a;' : 'color: #1e293b;' }}">
                                    {{ $reply->isFromAdmin() ? $reply->sender->name . ' (Admin)' : $reply->sender->full_name }}
                                </p>
                                <p style="font-size: 0.75rem; color: #64748b; margin-left: 1rem;">{{ $reply->created_at->format('M d, h:i A') }}</p>
                            </div>
                            <p style="color: #475569; white-space: pre-wrap;">{{ $reply->message }}</p>
                        </div>
                    </div>
                    @empty
                    <p style="color: #64748b; text-align: center; padding: 1rem;">No replies yet</p>
                    @endforelse
                </div>

                @if($ticket->status !== 'closed')
                <form method="POST" action="{{ route('admin.support.reply', $ticket) }}" style="border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
                    @csrf
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 0.5rem;">Add Reply</label>
                        <textarea name="message" rows="4" required placeholder="Type your response..." style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem;"></textarea>
                    </div>
                    <div style="margin-top: 1rem;">
                        <button type="submit" style="padding: 0.5rem 1.5rem; background: #2563eb; color: white; border: none; border-radius: 0.5rem; font-weight: 500; cursor: pointer;">
                            Send Reply
                        </button>
                    </div>
                </form>
                @else
                <div style="border-top: 1px solid #e2e8f0; padding-top: 1.5rem; text-align: center; color: #64748b;">
                    This ticket has been closed. Reopen it to continue the conversation.
                </div>
                @endif
            </div>
        </div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="page-card">
            <div class="page-card-header">
                <h4 style="font-weight: 600; margin-bottom: 1rem;">User Information</h4>
            </div>
            <div class="page-card-body">
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b;">Name</p>
                        <p style="font-weight: 500; margin: 0;">{{ $ticket->user?->full_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b;">Email</p>
                        <p style="font-weight: 500; margin: 0;">{{ $ticket->user?->email ?? '' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b;">Phone</p>
                        <p style="font-weight: 500; margin: 0;">{{ $ticket->user?->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b;">Tier</p>
                        <span style="padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; color: white;
                            @if($ticket->user?->currentTier?->slug === 'bronze') background: linear-gradient(135deg, #f59e0b, #d97706); @endif
                            @if($ticket->user?->currentTier?->slug === 'silver') background: linear-gradient(135deg, #64748b, #475569); @endif
                            @if($ticket->user?->currentTier?->slug === 'gold') background: linear-gradient(135deg, #eab308, #ca8a04); @endif
                            @if($ticket->user?->currentTier?->slug === 'emerald') background: linear-gradient(135deg, #10b981, #059669); @endif">
                            {{ $ticket->user?->currentTier?->name ?? 'N/A' }}
                        </span>
                    </div>
                </div>
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                    <a href="{{ route('admin.users.show', $ticket->user) }}" style="display: block; text-align: center; padding: 0.5rem 1rem; background: #f1f5f9; color: #475569; border-radius: 0.5rem; font-weight: 500; text-decoration: none;">
                        View User Profile
                    </a>
                </div>
            </div>
        </div>

        <div class="page-card">
            <div class="page-card-header">
                <h4 style="font-weight: 600; margin-bottom: 1rem;">Actions</h4>
            </div>
            <div class="page-card-body">
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @if(!$ticket->assignedTo)
                    <form method="POST" action="{{ route('admin.support.assign', $ticket) }}">
                        @csrf
                        <input type="hidden" name="admin_id" value="{{ auth()->guard('admin')->id() }}">
                        <button type="submit" style="width: 100%; padding: 0.5rem 1rem; background: #2563eb; color: white; border: none; border-radius: 0.5rem; font-weight: 500; cursor: pointer;">
                            Assign to Me
                        </button>
                    </form>
                    @endif

                    @if($ticket->status === 'in_progress')
                    <form method="POST" action="{{ route('admin.support.resolve', $ticket) }}">
                        @csrf
                        <button type="submit" style="width: 100%; padding: 0.5rem 1rem; background: #10b981; color: white; border: none; border-radius: 0.5rem; font-weight: 500; cursor: pointer;">
                            Mark as Resolved
                        </button>
                    </form>
                    @endif

                    @if($ticket->status !== 'closed')
                    <button type="button" onclick="showAdminCloseModal()" style="width: 100%; padding: 0.5rem 1rem; background: #64748b; color: white; border: none; border-radius: 0.5rem; font-weight: 500; cursor: pointer;">
                        Close Ticket
                    </button>
                    @endif

                    <a href="{{ route('admin.support.index') }}" style="display: block; text-align: center; padding: 0.5rem 1rem; background: #f1f5f9; color: #475569; border-radius: 0.5rem; font-weight: 500; text-decoration: none;">
                        Back to Tickets
                    </a>
                </div>
            </div>
        </div>

        <div class="page-card" style="background: #f8fafc;">
            <div class="page-card-header" style="background: #f8fafc;">
                <h4 style="font-weight: 600; margin-bottom: 1rem;">Ticket Details</h4>
            </div>
            <div class="page-card-body">
                <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.875rem;">
                    <div>
                        <p style="color: #64748b;">Ticket Number</p>
                        <p style="font-family: monospace; font-weight: 500;">{{ $ticket->ticket_number }}</p>
                    </div>
                    <div>
                        <p style="color: #64748b;">Created</p>
                        <p style="font-weight: 500;">{{ $ticket->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @if($ticket->assignedTo)
                    <div>
                        <p style="color: #64748b;">Assigned To</p>
                        <p style="font-weight: 500;">{{ $ticket->assignedTo->name }}</p>
                    </div>
                    @endif
                    @if($ticket->resolved_at)
                    <div>
                        <p style="color: #64748b;">Resolved At</p>
                        <p style="font-weight: 500;">{{ $ticket->resolved_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-overlay" id="adminCloseModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; padding: 2rem; max-width: 400px; width: 90%; box-shadow: 0 25px 80px rgba(0,0,0,0.3); text-align: center;">
        <div style="width: 56px; height: 56px; border-radius: 50%; background: #fef3c7; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 28px; height: 28px; color: #d97706;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem;">Close Ticket?</h3>
        <p style="font-size: 0.875rem; color: #64748b; margin: 0 0 1.5rem;">Are you sure you want to close this ticket? This action cannot be undone.</p>
        <div style="display: flex; gap: 0.75rem; justify-content: center;">
            <button onclick="hideAdminCloseModal()" class="btn btn-secondary" style="padding: 0.6rem 1.5rem; font-size: 0.875rem;">Cancel</button>
            <form action="{{ route('admin.support.close', $ticket) }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" style="padding: 0.6rem 1.5rem; font-size: 0.875rem; background: #dc2626; color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer;">Yes, Close Ticket</button>
            </form>
        </div>
    </div>
</div>

<script>
function showAdminCloseModal() { document.getElementById('adminCloseModal').style.display = 'flex'; }
function hideAdminCloseModal() { document.getElementById('adminCloseModal').style.display = 'none'; }
document.getElementById('adminCloseModal').addEventListener('click', function(e) { if (e.target === this) hideAdminCloseModal(); });
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') hideAdminCloseModal(); });
</script>
@endsection
