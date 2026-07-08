@extends('layouts.user')
@section('title', 'Ticket #' . $ticket->id)
@section('header', 'Support Ticket')
@section('subheader', $ticket->subject)

@section('content')
<div style="margin-bottom: 1rem;">
    <a href="{{ route('support.index') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.25rem;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Tickets
    </a>
</div>

<div class="page-card">
    <div class="page-card-header">
        <div>
            <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                <span class="badge"
                    @if($ticket->status === 'open') style="background: #dbeafe; color: #2563eb;" @endif
                    @if($ticket->status === 'in_progress') style="background: #fef3c7; color: #d97706;" @endif
                    @if($ticket->status === 'resolved') style="background: #dcfce7; color: #16a34a;" @endif
                    @if($ticket->status === 'closed') style="background: #f1f5f9; color: #64748b;" @endif>
                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                </span>
                @if($ticket->priority)
                <span class="badge"
                    @if($ticket->priority === 'urgent') style="background: #fee2e2; color: #dc2626;" @endif
                    @if($ticket->priority === 'high') style="background: #ffedd5; color: #ea580c;" @endif
                    @if($ticket->priority === 'medium') style="background: #fef3c7; color: #d97706;" @endif
                    @if($ticket->priority === 'low') style="background: #f3f4f6; color: #4b5563;" @endif>
                    {{ ucfirst($ticket->priority) }}
                </span>
                @endif
            </div>
            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: #1e293b;">{{ $ticket->subject }}</h3>
            <p style="font-size: 0.8rem; color: #94a3b8; margin-top: 0.25rem;">
                @if($ticket->category)Category: {{ ucfirst($ticket->category) }} &bull; @endif
                Created {{ $ticket->created_at->format('M d, Y \a\t g:i A') }}
            </p>
        </div>
        @if(!in_array($ticket->status, ['resolved', 'closed']))
            <button type="button" onclick="showCloseModal()" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">
                Close Ticket
            </button>
        @endif
    </div>
    <div class="page-card-body">
        <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
            <div style="display: flex; gap: 0.75rem;">
                <div style="width: 2rem; height: 2rem; border-radius: 9999px; background: #94a3b8; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem; font-weight: 700; flex-shrink: 0;">
                    {{ substr(Auth::user()->first_name, 0, 1) }}
                </div>
                <div style="flex: 1;">
                    <div style="background: #f8fafc; border-radius: 0.5rem; padding: 1rem;">
                        <p style="margin: 0; color: #475569; white-space: pre-wrap;">{{ $ticket->message }}</p>
                        @if($ticket->attachment_path)
                        <div style="margin-top: 0.75rem;">
                            <a href="{{ asset('storage/'.$ticket->attachment_path) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background: white; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; font-size: 0.8rem; color: #2563eb; font-weight: 500;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                View Attachment
                            </a>
                        </div>
                        @endif
                    </div>
                    <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">You &bull; {{ $ticket->created_at->format('M d, Y \a\t g:i A') }}</p>
                </div>
            </div>

            @foreach($ticket->replies as $reply)
            <div style="display: flex; gap: 0.75rem; {{ $reply->isFromAdmin() ? 'flex-direction: row-reverse;' : '' }}">
                <div style="width: 2rem; height: 2rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem; font-weight: 700; flex-shrink: 0; {{ $reply->isFromAdmin() ? 'background: #8b5cf6;' : 'background: #94a3b8;' }}">
                    {{ $reply->isFromAdmin() ? 'S' : substr(Auth::user()->first_name, 0, 1) }}
                </div>
                <div style="flex: 1; {{ $reply->isFromAdmin() ? 'text-align: right;' : '' }}">
                    <div style="border-radius: 0.5rem; padding: 1rem; {{ $reply->isFromAdmin() ? 'background: #f3e8ff;' : 'background: #f8fafc;' }} {{ $reply->isFromAdmin() ? '' : '' }}">
                        <p style="margin: 0; color: #475569; white-space: pre-wrap;">{{ $reply->message }}</p>
                    </div>
                    <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">
                        {{ $reply->isFromAdmin() ? 'Support Team' : 'You' }} &bull; {{ $reply->created_at->format('M d, Y \a\t g:i A') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        @if(in_array($ticket->status, ['open', 'in_progress']))
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 1.5rem 0;">
        <h4 style="font-weight: 600; color: #1e293b; margin: 0 0 1rem 0;">Add a Reply</h4>
        <form action="{{ route('support.reply', $ticket) }}" method="POST">
            @csrf
            <div class="form-group">
                <textarea name="message" rows="4" class="form-input" placeholder="Type your message...">{{ old('message') }}</textarea>
                @error('message')<p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Send Reply</button>
        </form>
        @endif
    </div>
</div>
<div class="modal-overlay" id="closeModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; padding: 2rem; max-width: 400px; width: 90%; box-shadow: 0 25px 80px rgba(0,0,0,0.3); text-align: center;">
        <div style="width: 56px; height: 56px; border-radius: 50%; background: #fef3c7; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 28px; height: 28px; color: #d97706;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem;">Close Ticket?</h3>
        <p style="font-size: 0.875rem; color: #64748b; margin: 0 0 1.5rem;">Are you sure you want to close this ticket? You won't be able to reply once it's closed.</p>
        <div style="display: flex; gap: 0.75rem; justify-content: center;">
            <button onclick="hideCloseModal()" class="btn btn-secondary" style="padding: 0.6rem 1.5rem; font-size: 0.875rem;">Cancel</button>
            <form action="{{ route('support.close', $ticket) }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn" style="padding: 0.6rem 1.5rem; font-size: 0.875rem; background: #dc2626; color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer;">Yes, Close Ticket</button>
            </form>
        </div>
    </div>
</div>

<script>
function showCloseModal() { document.getElementById('closeModal').style.display = 'flex'; }
function hideCloseModal() { document.getElementById('closeModal').style.display = 'none'; }
document.getElementById('closeModal').addEventListener('click', function(e) { if (e.target === this) hideCloseModal(); });
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') hideCloseModal(); });
</script>
@endsection
