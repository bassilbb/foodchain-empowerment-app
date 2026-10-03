@extends('admin.layouts.app')

@section('title', 'Departments')
@section('header', 'Departments')
@section('subheader', 'Manage departments within branches')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">All Departments</h3>
        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Department
        </a>
    </div>
    <div class="page-card-body" style="padding: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Branch</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $dept)
                <tr>
                    <td style="font-weight: 600; color: #1e293b;">{{ $dept->name }}</td>
                    <td><span style="font-family: monospace;">{{ $dept->code }}</span></td>
                    <td>{{ $dept->branch?->name ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $dept->is_active ? 'badge-success' : 'badge-dark' }}">
                            {{ $dept->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('admin.departments.show', $dept) }}" class="action-link">View</a>
                        <a href="{{ route('admin.departments.edit', $dept) }}" class="action-link" style="margin-left: 0.75rem;">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <p>No departments found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($departments->hasPages())
<div class="pagination">{{ $departments->links() }}</div>
@endif
@endsection
