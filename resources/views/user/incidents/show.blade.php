@extends('layouts.user')

@section('title', 'Incident Details')
@section('header', 'Incident Details')
@section('subheader', 'View incident report details')

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <a href="{{ route('user.incidents.index') }}" class="btn btn-secondary" style="margin-bottom: 0.5rem; padding: 0.5rem 1rem; font-size: 0.75rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Back to Incidents
            </a>
            <h3 class="page-card-title" style="margin: 0;">{{ $incident->title }}</h3>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <span class="badge" style="background: {{ $incident->priority_color }}20; color: {{ $incident->priority_color }};">
                {{ ucfirst($incident->priority) }} Priority
            </span>
            <span class="badge" style="background: {{ $incident->status_color }}20; color: {{ $incident->status_color }};">
                {{ ucfirst(str_replace('_', ' ', $incident->status)) }}
            </span>
        </div>
    </div>
    <div class="page-card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
            <div>
                <div class="page-card" style="margin: 0 0 1.5rem 0;">
                    <div class="page-card-header">
                        <h4 class="page-card-title" style="margin: 0;">Description</h4>
                    </div>
                    <div class="page-card-body">
                        <p style="margin: 0; color: #475569;">{{ $incident->description }}</p>
                    </div>
                </div>

                <div class="page-card" style="margin: 0 0 1.5rem 0;">
                    <div class="page-card-header">
                        <h4 class="page-card-title" style="margin: 0;">Comments & Updates</h4>
                    </div>
                    <div class="page-card-body">
                        @forelse($incident->comments->sortBy('created_at') as $comment)
                        <div style="padding-bottom: 1rem; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <strong style="color: #10b981;">{{ $comment->commenter_name }}</strong>
                                <span style="font-size: 0.75rem; color: #94a3b8;">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p style="margin: 0; color: #64748b;">{{ $comment->comment }}</p>
                        </div>
                        @empty
                        <p style="text-align: center; color: #94a3b8; margin: 0;">No comments or updates yet.</p>
                        @endforelse

                        @if(!in_array($incident->status, ['resolved', 'closed']))
                        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 1.5rem 0;">
                        <form action="{{ route('user.incidents.comment', $incident) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Add Comment</label>
                                <textarea name="comment" rows="3" class="form-input" placeholder="Share an update or additional details..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.5rem;">Post Comment</button>
                        </form>
                        @endif
                    </div>
                </div>

                @if($incident->escalations->count() > 0)
                <div class="page-card" style="margin: 0;">
                    <div class="page-card-header">
                        <h4 class="page-card-title" style="margin: 0;">Escalation History</h4>
                    </div>
                    <div class="page-card-body">
                        @foreach($incident->escalations as $escalation)
                        <div style="padding-bottom: 1rem; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <strong style="color: #dc2626;">Escalated</strong>
                                <span style="font-size: 0.75rem; color: #94a3b8;">{{ $escalation->escalated_at->diffForHumans() }}</span>
                            </div>
                            <p style="margin: 0; color: #64748b;">{{ $escalation->reason }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div>
                <div class="page-card" style="margin: 0;">
                    <div class="page-card-header">
                        <h4 class="page-card-title" style="margin: 0;">Incident Details</h4>
                    </div>
                    <div class="page-card-body" style="padding: 0;">
                        <table class="data-table" style="font-size: 0.875rem;">
                            <tr>
                                <td style="color: #64748b;">Category</td>
                                <td><span class="badge badge-info">{{ ucwords(str_replace('_', ' ', $incident->category)) }}</span></td>
                            </tr>
                            <tr>
                                <td style="color: #64748b;">Branch</td>
                                <td>{{ $incident->branch?->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="color: #64748b;">Department</td>
                                <td>{{ $incident->department?->name ?? 'N/A' }}</td>
                            </tr>
                            @if($incident->location)
                            <tr>
                                <td style="color: #64748b;">Location</td>
                                <td>{{ $incident->location }}</td>
                            </tr>
                            @endif
                            @if($incident->assignedTo)
                            <tr>
                                <td style="color: #64748b;">Assigned To</td>
                                <td>{{ $incident->assignedTo->adminUser?->name ?? 'N/A' }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td style="color: #64748b;">Reported</td>
                                <td>{{ $incident->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            @if($incident->resolved_at)
                            <tr>
                                <td style="color: #64748b;">Resolved</td>
                                <td>{{ $incident->resolved_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            @endif
                            @if($incident->closed_at)
                            <tr>
                                <td style="color: #64748b;">Closed</td>
                                <td>{{ $incident->closed_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($incident->status === 'resolved')
                <div class="page-card" style="margin-top: 1.5rem;">
                    <div class="page-card-header">
                        <h4 class="page-card-title" style="margin: 0;">Resolution</h4>
                    </div>
                    <div class="page-card-body">
                        <p style="color: #16a34a; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Issue Resolved
                        </p>
                        @if($incident->resolution_notes)
                        <p style="font-size: 0.875rem; color: #64748b; margin: 0;">{{ $incident->resolution_notes }}</p>
                        @endif
                        <p style="font-size: 0.875rem; color: #64748b; margin-top: 1rem;">If your issue still exists, please contact support.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection