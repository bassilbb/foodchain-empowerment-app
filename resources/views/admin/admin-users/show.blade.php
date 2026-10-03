{{-- resources/views/admin/admin-users/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', $adminUser->name)
@section('header', $adminUser->name)
@section('subheader', 'Admin user details')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif

<div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
    <a href="{{ route('admin.admin-users.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: #64748b; text-decoration: none; font-size: 0.875rem; width: fit-content;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Admin Users
    </a>
</div>

<div class="grid-2" style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Admin Information</h3>
                <a href="{{ route('admin.admin-users.edit', $adminUser) }}" class="btn btn-secondary btn-sm">Edit</a>
            </div>
            <div class="page-card-body">
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Name</label>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $adminUser->name }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Email</label>
                        <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $adminUser->email }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Phone</label>
                        <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $adminUser->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Role</label>
                        <span class="badge badge-{{ $adminUser->role === 'super_admin' ? 'error' : ($adminUser->role === 'inventory_manager' ? 'info' : 'success') }}">
                            {{ ucfirst(str_replace('_', ' ', $adminUser->role)) }}
                        </span>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Status</label>
                        <span class="badge {{ $adminUser->is_active ? 'badge-success' : 'badge-dark' }}">
                            {{ $adminUser->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Last Login</label>
                        <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $adminUser->last_login_at?->diffForHumans() ?? 'Never' }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Joined</label>
                        <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $adminUser->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->guard('admin')->user()->isSuperAdmin() && auth()->guard('admin')->id() !== $adminUser->id)
        <div class="page-card">
            <div class="page-card-body">
                <form method="POST" action="{{ route('admin.admin-users.destroy', $adminUser) }}" onsubmit="return confirm('Are you sure you want to delete this admin user?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="width: 100%;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Delete Admin User
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Approved Distributions</h3>
                <span class="badge badge-info">{{ $adminUser->approvedDistributions->count() }}</span>
            </div>
            <div style="padding: 0;">
                @forelse($adminUser->approvedDistributions as $distribution)
                <div class="list-item">
                    <div>
                        <div style="font-weight: 600; color: #1e293b;">{{ $distribution->distribution_number }}</div>
                        <div style="font-size: 0.8rem; color: #64748b;">{{ $distribution->created_at->format('M d, Y') }}</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: 700; color: #1e293b;">{{ format_currency($distribution->package_value) }}</div>
                        <span class="badge badge-{{ $distribution->status === 'delivered' ? 'success' : 'info' }}">{{ ucfirst($distribution->status) }}</span>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <p>No approved distributions</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Inventory Transactions</h3>
                <span class="badge badge-info">{{ $adminUser->inventoryTransactions->count() }}</span>
            </div>
            <div style="padding: 0;">
                @forelse($adminUser->inventoryTransactions->take(10) as $transaction)
                <div class="list-item">
                    <div>
                        <div style="font-weight: 600; color: #1e293b;">{{ $transaction->type === 'stock_in' ? 'Stock In' : 'Stock Out' }} — {{ $transaction->foodItem?->name ?? 'Food Item' }}</div>
                        <div style="font-size: 0.8rem; color: #64748b;">{{ $transaction->reference ?? 'No reference' }} · {{ $transaction->created_at->format('M d, Y') }}</div>
                    </div>
                    <div style="text-align: right;">
                        <span class="badge {{ $transaction->isStockIn() ? 'badge-success' : 'badge-error' }}">{{ $transaction->quantity }}</span>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <p>No inventory transactions</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Assigned Support Tickets</h3>
                <span class="badge badge-info">{{ $adminUser->assignedTickets->count() }}</span>
            </div>
            <div style="padding: 0;">
                @forelse($adminUser->assignedTickets->take(10) as $ticket)
                <div class="list-item">
                    <div>
                        <div style="font-weight: 600; color: #1e293b;">{{ $ticket->subject }}</div>
                        <div style="font-size: 0.8rem; color: #64748b;">{{ $ticket->ticket_number }} · {{ $ticket->created_at->format('M d, Y') }}</div>
                    </div>
                    <span class="badge badge-{{ $ticket->status === 'resolved' ? 'success' : ($ticket->status === 'open' ? 'error' : 'warning') }}">{{ ucfirst($ticket->status) }}</span>
                </div>
                @empty
                <div class="empty-state">
                    <p>No assigned support tickets</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection