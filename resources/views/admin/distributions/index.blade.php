@extends('admin.layouts.app')

@section('title', 'Distributions')
@section('header', 'Food Distributions')
@section('subheader', 'Manage food distribution packages')

@section('content')
<div class="page-header">
    <div>
        <h1>@yield('header')</h1>
        <p>@yield('subheader')</p>
    </div>
    <div class="header-actions-btns">
        <a href="{{ route('admin.distributions.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Create Distribution
        </a>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">All Distributions</h3>
            <p class="page-card-subtitle">{{ $distributions->total() }} total</p>
        </div>
    </div>
    <div class="page-card-body" style="padding: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>User</th>
                    <th>Tier</th>
                    <th>Value</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($distributions as $distribution)
                <tr>
                    <td style="font-family: monospace; font-size: 13px; color: #1e293b;">{{ $distribution->distribution_number }}</td>
                    <td>
                        <div style="font-weight: 500; color: #1e293b;">{{ $distribution->user->full_name }}</div>
                        <div style="font-size: 13px; color: #64748b;">{{ $distribution->user->email }}</div>
                    </td>
                    <td>
                        <span class="badge emerald">{{ $distribution->tier->name }}</span>
                    </td>
                    <td style="font-weight: 600; color: #1e293b;">{{ format_currency($distribution->package_value) }}</td>
                    <td>
                        <span class="badge
                            @if($distribution->status === 'approved') badge-success
                            @elseif($distribution->status === 'pending') badge-warning
                            @elseif($distribution->status === 'dispatched') badge-info
                            @elseif($distribution->status === 'completed') badge-primary
                            @else badge-dark
                            @endif">
                            {{ ucfirst($distribution->status) }}
                        </span>
                    </td>
                    <td style="color: #94a3b8; font-size: 13px;">{{ $distribution->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display: flex; gap: 12px;">
                            <a href="{{ route('admin.distributions.edit', $distribution) }}" class="action-link">Edit</a>
                            <a href="{{ route('admin.distributions.show', $distribution) }}" class="action-link">View</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <p>No distributions found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination">
    {{ $distributions->links() }}
</div>
@endsection
