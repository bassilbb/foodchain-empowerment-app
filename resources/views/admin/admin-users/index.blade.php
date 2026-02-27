{{-- resources/views/admin/admin-users/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Admin Users')
@section('page-title', 'Admin User Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    @if(auth()->guard('admin')->user()->isSuperAdmin())
    <a href="{{ route('admin.admin-users.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
        + Add Admin User
    </a>
    @else
    <div></div>
    @endif

    <div class="text-sm text-gray-600">
        Total: <span class="font-semibold">{{ $admins->total() }}</span> admin users
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name & Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                @if(auth()->guard('admin')->user()->isSuperAdmin())
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($admins as $admin)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <div>
                        <div class="font-medium text-gray-900">{{ $admin->name }}</div>
                        <div class="text-sm text-gray-500">{{ $admin->email }}</div>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $admin->phone ?? '-' }}
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                        {{ $admin->role === 'super_admin' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $admin->role === 'inventory_manager' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $admin->role === 'customer_support' ? 'bg-green-100 text-green-800' : '' }}">
                        {{ ucfirst(str_replace('_', ' ', $admin->role)) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $admin->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $admin->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Never' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $admin->created_at->format('M d, Y') }}
                </td>
                @if(auth()->guard('admin')->user()->isSuperAdmin())
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="{{ route('admin.admin-users.edit', $admin) }}"
                       class="text-blue-600 hover:text-blue-900 font-medium">
                        Edit
                    </a>
                </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center">
                    <div class="text-gray-400">
                        <svg class="mx-auto h-12 w-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <p class="text-sm">No admin users found</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $admins->links() }}
</div>

<!-- Info Box -->
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <h4 class="font-semibold text-blue-800 mb-2">Admin Roles Explained:</h4>
    <ul class="text-sm text-blue-700 space-y-1">
        <li><strong>Super Admin:</strong> Full access to all features including user management</li>
        <li><strong>Inventory Manager:</strong> Manage inventory, food items, distributions, and view users</li>
        <li><strong>Customer Support:</strong> View users, payments, distributions, and handle support tickets</li>
    </ul>
</div>
@endsection
