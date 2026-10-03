@extends('admin.layouts.app')

@section('title', $staff->staff_number)
@section('header', $staff->position)
@section('subheader', 'Staff member details')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary btn-sm">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Staff
    </a>
    <a href="{{ route('admin.staff.edit', $staff) }}" class="btn btn-primary btn-sm">Edit Staff</a>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">Staff Details</h3>
        <span class="badge {{ $staff->is_active ? 'badge-success' : 'badge-dark' }}">{{ $staff->is_active ? 'Active' : 'Inactive' }}</span>
    </div>
    <div class="page-card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
            <div>
                <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Staff Number</label>
                <p style="font-weight: 600; color: #1e293b; margin: 0; font-family: monospace;">{{ $staff->staff_number }}</p>
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Position</label>
                <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $staff->position }}</p>
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Department</label>
                <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $staff->department?->name ?? '-' }}</p>
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Branch</label>
                <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $staff->department?->branch?->name ?? '-' }}</p>
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Phone</label>
                <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $staff->phone ?? 'N/A' }}</p>
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Linked Admin Account</label>
                @if($staff->adminUser)
                <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $staff->adminUser->name }} <span style="color: #94a3b8; font-weight: 400;">({{ $staff->adminUser->email }})</span></p>
                @else
                <p style="font-weight: 500; color: #94a3b8; margin: 0;">None</p>
                @endif
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Joined</label>
                <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $staff->created_at->format('M d, Y') }}</p>
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Total Assigned Incidents</label>
                <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $staff->incidents()->count() }}</p>
            </div>
        </div>
    </div>
</div>
@endsection