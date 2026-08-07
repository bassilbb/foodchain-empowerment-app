@extends('admin.layouts.app')

@section('title', $incident->incident_number . ' - Admin')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('admin.incidents.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: #10b981; font-weight: 500; text-decoration: none;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Incidents
    </a>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0;">Incident: {{ $incident->incident_number }}</h1>
        <p style="color: #64748b; margin: 0;">{{ $incident->title }}</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <span style="padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 600;
            @if($incident->priority === 'critical') background: #fecaca; color: #991b1b; @endif
            @if($incident->priority === 'high') background: #fee2e2; color: #dc2626; @endif
            @if($incident->priority === 'medium') background: #fef3c7; color: #d97706; @endif
            @if($incident->priority === 'low') background: #dbeafe; color: #2563eb; @endif">
            {{ ucfirst($incident->priority) }} Priority
        </span>
        <span style="padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 600;
            @if($incident->status === 'open') background: #dbeafe; color: #2563eb; @endif
            @if($incident->status === 'in_progress') background: #fef3c7; color: #d97706; @endif
            @if($incident->status === 'resolved') background: #dcfce7; color: #16a34a; @endif
            @if($incident->status === 'closed') background: #f1f5f9; color: #64748b; @endif">
            {{ ucfirst(str_replace('_', ' ', $incident->status)) }}
        </span>
    </div>
</div>

@if(session('success'))
<div style="padding: 1rem; background: #dcfce7; border: 1px solid #86efac; border-radius: 0.5rem; color: #166534; margin-bottom: 1.5rem;">
    {{ session('success') }}
</div>
@endif

<div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; @media(min-width: 992px) { grid-template-columns: 2fr 1fr; }">
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Incident Details</h3>
                <a href="{{ route('admin.incidents.edit', $incident) }}" style="padding: 0.375rem 0.75rem; background: linear-gradient(135deg, #10b981, #059669); color: white; border-radius: 6px; font-size: 0.875rem; font-weight: 500; text-decoration: none;">
                    Edit
                </a>
            </div>
            <div class="page-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase;">Branch</p>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $incident->branch?->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase;">Department</p>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $incident->department?->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase;">Category</p>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ ucwords(str_replace('_', ' ', $incident->category ?? 'N/A')) }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase;">Location</p>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $incident->location ?? 'Not specified' }}</p>
                    </div>
                    <div style="grid-column: span 2;">
                        <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase;">Description</p>
                        <p style="color: #475569; margin: 0;">{{ $incident->description }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($incident->resolution_notes)
        <div class="page-card" style="border-left: 4px solid #10b981;">
            <div class="page-card-header" style="background: #10b981;">
                <h3 class="page-card-title" style="color: white;">Resolution Notes</h3>
            </div>
            <div class="page-card-body">
                <p style="margin: 0;">{{ $incident->resolution_notes }}</p>
                @if($incident->resolved_at)
                    <small style="color: #64748b; margin-top: 0.5rem; display: block;">
                        Resolved on {{ $incident->resolved_at->format('M d, Y h:i A') }}
                    </small>
                @endif
            </div>
        </div>
        @endif

        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Escalation History</h3>
            </div>
            <div class="page-card-body">
                @forelse($incident->escalations as $escalation)
                    <div style="border-left: 4px solid #f59e0b; padding-left: 1rem; margin-bottom: 0.75rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <strong style="color: #1e293b;">{{ $escalation->escalatedTo?->adminUser?->name ?? 'Unknown' }}</strong>
                            <small style="color: #64748b;">{{ $escalation->escalated_at->format('M d, Y h:i A') }}</small>
                        </div>
                        <p style="margin: 0.25rem 0;">{{ $escalation->reason }}</p>
                        @if($escalation->acknowledged_at)
                            <small style="color: #16a34a;">
                                Acknowledged on {{ $escalation->acknowledged_at->format('M d, Y h:i A') }}
                            </small>
                        @else
                            <form action="{{ route('admin.incidents.acknowledge', $escalation) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" style="padding: 0.25rem 0.75rem; background: #f59e0b; color: white; border: none; border-radius: 6px; font-size: 0.875rem; cursor: pointer;">Acknowledge</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p style="color: #64748b; margin: 0;">No escalations yet</p>
                @endforelse
            </div>
        </div>

        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Comments</h3>
            </div>
            <div class="page-card-body">
                @forelse($incident->comments as $comment)
                    <div style="border-left: 3px solid #10b981; padding-left: 1rem; margin-bottom: 0.75rem; @if($comment->is_internal) background: #f8fafc; padding: 0.75rem; border-radius: 0 0.5rem 0.5rem 0; @endif">
                        <div style="display: flex; justify-content: space-between;">
                            <strong style="color: #1e293b;">{{ $comment->adminUser?->name ?? 'Unknown' }}</strong>
                            <small style="color: #64748b;">{{ $comment->created_at->format('M d, Y h:i A') }}</small>
                        </div>
                        @if($comment->is_internal)
                            <span style="padding: 0.125rem 0.5rem; background: #fef3c7; color: #92400e; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; display: inline-block; margin-bottom: 0.25rem;">Internal</span>
                        @endif
                        <p style="margin: 0; color: #475569;">{{ $comment->comment }}</p>
                    </div>
                @empty
                    <p style="color: #64748b; margin: 0;">No comments yet</p>
                @endforelse

                <hr style="border-top: 1px solid #f1f5f9; margin: 1.5rem 0;">
                <form action="{{ route('admin.incidents.comment', $incident) }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 0.5rem;">Add Comment</label>
                        <textarea name="comment" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;" required placeholder="Enter your comment..."></textarea>
                    </div>
                    <div style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" name="is_internal" id="is_internal" style="width: 16px; height: 16px;">
                        <label for="is_internal" style="font-size: 0.875rem; color: #475569;">Internal note (not visible to user)</label>
                    </div>
                    <button type="submit" style="padding: 0.5rem 1rem; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">
                        Add Comment
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Actions</h3>
            </div>
            <div class="page-card-body">
                @if($incident->isOpen())
                    <form action="{{ route('admin.incidents.assign', $incident) }}" method="POST" style="margin-bottom: 1rem;">
                        @csrf
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 0.5rem;">Assign To Staff</label>
                        <div style="display: flex; gap: 0.5rem;">
                            <select name="staff_id" style="flex: 1; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;" required>
                                <option value="">Select Staff</option>
                                @foreach($staff as $s)
                                    <option value="{{ $s->id }}">{{ $s->adminUser?->name ?? 'Unassigned' }} - {{ $s->position }}</option>
                                @endforeach
                            </select>
                            <button type="submit" style="padding: 0.5rem 1rem; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">Assign</button>
                        </div>
                    </form>

                    <form action="{{ route('admin.incidents.escalate', $incident) }}" method="POST" style="margin-bottom: 1rem;">
                        @csrf
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 0.5rem;">Escalate To</label>
                        <select name="staff_id" style="width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; margin-bottom: 0.5rem;" required>
                            <option value="">Select Staff</option>
                            @foreach($staff as $s)
                                <option value="{{ $s->id }}">{{ $s->adminUser?->name ?? 'Unassigned' }} - {{ $s->position }}</option>
                            @endforeach
                        </select>
                        <textarea name="reason" rows="2" style="width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; margin-bottom: 0.5rem;" required placeholder="Reason for escalation..."></textarea>
                        <button type="submit" style="width: 100%; padding: 0.5rem; background: #f59e0b; color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">
                            Escalate
                        </button>
                    </form>

                    <form action="{{ route('admin.incidents.resolve', $incident) }}" method="POST" style="margin-bottom: 1rem;">
                        @csrf
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 0.5rem;">Mark as Resolved</label>
                        <textarea name="resolution_notes" rows="3" style="width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; margin-bottom: 0.5rem;" required placeholder="Resolution notes..."></textarea>
                        <button type="submit" style="width: 100%; padding: 0.5rem; background: #10b981; color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">
                            Resolve
                        </button>
                    </form>
                @endif

                @if($incident->isResolved())
                    <form action="{{ route('admin.incidents.close', $incident) }}" method="POST">
                        @csrf
                        <button type="submit" style="width: 100%; padding: 0.5rem; background: #1e293b; color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">
                            Close Incident
                        </button>
                    </form>
                @endif

                @if($incident->isClosed() || $incident->isResolved())
                    <form action="{{ route('admin.incidents.reopen', $incident) }}" method="POST" style="margin-top: 0.5rem;">
                        @csrf
                        <button type="submit" style="width: 100%; padding: 0.5rem; background: #64748b; color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">
                            Reopen Incident
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Details</h3>
            </div>
            <div class="page-card-body">
                <div style="margin-bottom: 0.75rem;">
                    <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase;">Assigned To</p>
                    <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $incident->assignedTo?->name ?? 'Unassigned' }}</p>
                </div>
                <div style="margin-bottom: 0.75rem;">
                    <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase;">Reported By</p>
                    <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $incident->reporter?->full_name ?? 'N/A' }}</p>
                </div>
                <div style="margin-bottom: 0.75rem;">
                    <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase;">Created</p>
                    <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $incident->created_at->format('M d, Y h:i A') }}</p>
                </div>
                @if($incident->reported_at)
                    <div style="margin-bottom: 0.75rem;">
                        <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase;">Reported At</p>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $incident->reported_at->format('M d, Y h:i A') }}</p>
                    </div>
                @endif
                @if($incident->closed_at)
                    <div>
                        <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase;">Closed At</p>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $incident->closed_at->format('M d, Y h:i A') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="page-card" style="border: 1px solid #fecaca;">
            <div class="page-card-header" style="background: #fee2e2;">
                <h3 class="page-card-title" style="color: #dc2626;">Danger Zone</h3>
            </div>
            <div class="page-card-body">
                <form action="{{ route('admin.incidents.destroy', $incident) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="width: 100%; padding: 0.5rem; background: #dc2626; color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;"
                        data-confirm='{"title":"Delete Incident?","text":"Are you sure you want to delete this incident?","btnText":"Delete","btnClass":"confirm-danger","iconClass":"danger"}'>
                        Delete Incident
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
