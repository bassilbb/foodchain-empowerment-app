@extends('admin.layouts.app')

@section('title', 'Branches')
@section('header', 'Branches')
@section('subheader', 'Manage branch locations')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">All Branches</h3>
        <a href="{{ route('admin.branches.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Branch
        </a>
    </div>
    <div class="page-card-body" style="padding: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($branches as $branch)
                <tr>
                    <td style="font-weight: 600; color: #1e293b;">{{ $branch->name }}</td>
                    <td><span style="font-family: monospace;">{{ $branch->code }}</span></td>
                    <td>{{ $branch->city ?? '-' }}</td>
                    <td>{{ $branch->state ?? '-' }}</td>
                    <td>{{ $branch->phone ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $branch->is_active ? 'badge-success' : 'badge-dark' }}">
                            {{ $branch->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('admin.branches.show', $branch) }}" class="action-link">View</a>
                        <a href="{{ route('admin.branches.edit', $branch) }}" class="action-link" style="margin-left: 0.75rem;">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <p>No branches found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($branches->hasPages())
<div class="pagination">{{ $branches->links() }}</div>
@endif
@endsection
