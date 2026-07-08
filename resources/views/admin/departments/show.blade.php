@extends('admin.layouts.app')

@section('title', $department->name)
@section('header', $department->name)
@section('subheader', 'Department details')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Department Details</h3>
            <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">Edit</a>
        </div>
        <div class="page-card-body">
            <table style="width: 100%;">
                <tr><td style="padding: 0.5rem 0; color: #64748b; font-size: 0.875rem;">Code</td><td style="padding: 0.5rem 0; font-weight: 600; font-family: monospace;">{{ $department->code }}</td></tr>
                <tr><td style="padding: 0.5rem 0; color: #64748b; font-size: 0.875rem;">Branch</td><td style="padding: 0.5rem 0;">{{ $department->branch?->name ?? '-' }}</td></tr>
                <tr><td style="padding: 0.5rem 0; color: #64748b; font-size: 0.875rem;">Description</td><td style="padding: 0.5rem 0;">{{ $department->description ?? '-' }}</td></tr>
                <tr><td style="padding: 0.5rem 0; color: #64748b; font-size: 0.875rem;">Status</td><td style="padding: 0.5rem 0;"><span class="badge {{ $department->is_active ? 'badge-success' : 'badge-dark' }}">{{ $department->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
            </table>
        </div>
    </div>
</div>

<div style="margin-top: 1rem;">
    <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">← Back to Departments</a>
</div>
@endsection
