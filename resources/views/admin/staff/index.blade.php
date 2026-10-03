@extends('admin.layouts.app')

@section('title', 'Staff')
@section('header', 'Staff')
@section('subheader', 'Manage staff members across branches and departments')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">All Staff</h3>
        <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Staff
        </a>
    </div>
    <div class="page-card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Staff Number</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Branch</th>
                        <th>Phone</th>
                        <th>Linked Account</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $member)
                    <tr>
                        <td style="font-weight: 600; color: #1e293b; font-family: monospace;">{{ $member->staff_number }}</td>
                        <td>{{ $member->position }}</td>
                        <td>{{ $member->department?->name ?? '-' }}</td>
                        <td>{{ $member->department?->branch?->name ?? '-' }}</td>
                        <td>{{ $member->phone ?? '-' }}</td>
                        <td>
                            @if($member->adminUser)
                            <span style="color: #475569;">{{ $member->adminUser->name }}</span>
                            @else
                            <span style="color: #94a3b8;">None</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $member->is_active ? 'badge-success' : 'badge-dark' }}">
                                {{ $member->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('admin.staff.show', $member) }}" class="action-link">View</a>
                            <a href="{{ route('admin.staff.edit', $member) }}" class="action-link" style="margin-left: 0.75rem;">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <p>No staff members found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($staff->hasPages())
<div class="pagination">{{ $staff->links() }}</div>
@endif
@endsection