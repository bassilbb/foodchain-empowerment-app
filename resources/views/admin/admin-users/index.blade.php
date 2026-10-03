{{-- resources/views/admin/admin-users/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Admin Users')
@section('page-title', 'Admin User Management')
@section('header', 'Admin Users')
@section('subheader', 'Manage administrator accounts')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="page-card-header">
    @if(auth()->guard('admin')->user()->isSuperAdmin())
    <a href="{{ route('admin.admin-users.create') }}" class="btn-primary">
        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add Admin User
    </a>
    @else
    <div></div>
    @endif
    <span>Total: <strong>{{ $admins->total() }}</strong> admin users</span>
</div>

<div class="page-card">
    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th class="table-header">Name & Email</th>
                    <th class="table-header">Phone</th>
                    <th class="table-header">Role</th>
                    <th class="table-header">Status</th>
                    <th class="table-header">Last Login</th>
                    <th class="table-header">Joined</th>
                    @if(auth()->guard('admin')->user()->isSuperAdmin())
                    <th class="table-header">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $admin)
                <tr>
                    <td>
                        <div style="font-weight: 600;">{{ $admin->name }}</div>
                        <div style="font-size: 13px; color: #94a3b8;">{{ $admin->email }}</div>
                    </td>
                    <td>{{ $admin->phone ?? '-' }}</td>
                    <td>
                        <span class="badge badge-{{ $admin->role === 'super_admin' ? 'error' : ($admin->role === 'inventory_manager' ? 'info' : 'success') }}">
                            {{ ucfirst(str_replace('_', ' ', $admin->role)) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $admin->is_active ? 'success' : 'dark' }}">
                            {{ $admin->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>{{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Never' }}</td>
                    <td>{{ $admin->created_at->format('M d, Y') }}</td>
                    @if(auth()->guard('admin')->user()->isSuperAdmin())
                    <td>
                        <a href="{{ route('admin.admin-users.edit', $admin) }}" class="btn-primary btn-sm">Edit</a>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <p>No admin users found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination">
    {{ $admins->links() }}
</div>

<div class="page-card">
    <div class="page-card-body">
        <h4 class="page-card-title">Admin Roles Explained:</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px;">
            <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 14px;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6; margin-top: 6px; flex-shrink: 0;"></div>
                <div><strong>Super Admin:</strong> Full access to all features including user management</div>
            </div>
            <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 14px;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6; margin-top: 6px; flex-shrink: 0;"></div>
                <div><strong>Inventory Manager:</strong> Manage inventory, food items, distributions, and view users</div>
            </div>
            <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 14px;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6; margin-top: 6px; flex-shrink: 0;"></div>
                <div><strong>Customer Support:</strong> View users, payments, distributions, and handle support tickets</div>
            </div>
        </div>
    </div>
</div>
@endsection
