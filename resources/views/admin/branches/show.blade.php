@extends('admin.layouts.app')

@section('title', $branch->name)
@section('header', $branch->name)
@section('subheader', 'Branch details and departments')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Branch Details</h3>
            <a href="{{ route('admin.branches.edit', $branch) }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">Edit</a>
        </div>
        <div class="page-card-body">
            <table style="width: 100%;">
                <tr><td style="padding: 0.5rem 0; color: #64748b; font-size: 0.875rem;">Code</td><td style="padding: 0.5rem 0; font-weight: 600; font-family: monospace;">{{ $branch->code }}</td></tr>
                <tr><td style="padding: 0.5rem 0; color: #64748b; font-size: 0.875rem;">Address</td><td style="padding: 0.5rem 0;">{{ $branch->address ?? '-' }}</td></tr>
                <tr><td style="padding: 0.5rem 0; color: #64748b; font-size: 0.875rem;">City</td><td style="padding: 0.5rem 0;">{{ $branch->city ?? '-' }}</td></tr>
                <tr><td style="padding: 0.5rem 0; color: #64748b; font-size: 0.875rem;">State</td><td style="padding: 0.5rem 0;">{{ $branch->state ?? '-' }}</td></tr>
                <tr><td style="padding: 0.5rem 0; color: #64748b; font-size: 0.875rem;">Phone</td><td style="padding: 0.5rem 0;">{{ $branch->phone ?? '-' }}</td></tr>
                <tr><td style="padding: 0.5rem 0; color: #64748b; font-size: 0.875rem;">Email</td><td style="padding: 0.5rem 0;">{{ $branch->email ?? '-' }}</td></tr>
                <tr><td style="padding: 0.5rem 0; color: #64748b; font-size: 0.875rem;">Status</td><td style="padding: 0.5rem 0;"><span class="badge {{ $branch->is_active ? 'badge-success' : 'badge-dark' }}">{{ $branch->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
            </table>
        </div>
    </div>

    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Departments</h3>
            <a href="{{ route('admin.departments.create') }}?branch_id={{ $branch->id }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add
            </a>
        </div>
        <div class="page-card-body" style="padding: 0;">
            @forelse($branch->departments as $dept)
            <div class="list-item">
                <div>
                    <div style="font-weight: 600; color: #1e293b;">{{ $dept->name }}</div>
                    <div style="font-size: 0.8rem; color: #64748b;">{{ $dept->code }}</div>
                </div>
                <span class="badge {{ $dept->is_active ? 'badge-success' : 'badge-dark' }}">{{ $dept->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
            @empty
            <div class="empty-state">
                <p>No departments for this branch</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<div style="margin-top: 1rem;">
    <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">← Back to Branches</a>
</div>
@endsection
