{{-- resources/views/admin/admin-users/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Edit Admin User')
@section('page-title', 'Edit Admin: ' . $adminUser->name)

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.admin-users.update', $adminUser) }}">
        @csrf
        @method('PUT')
        <div class="space-y-6">
            <!-- Account Info Section -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Account Information</h3>

                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $adminUser->name) }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $adminUser->email) }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Phone Number <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <input type="text" name="phone" value="{{ old('phone', $adminUser->phone) }}"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Role -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Admin Role <span class="text-red-500">*</span>
                    </label>
                    <select name="role" required
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ old('role', $adminUser->role) === $role ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $role)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Active Status -->
                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $adminUser->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">
                            Active Account
                        </span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1 ml-6">
                        Inactive accounts cannot login to the system
                    </p>
                </div>
            </div>

            <!-- Password Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Change Password</h3>
                <p class="text-sm text-gray-600 mb-4">Leave blank to keep the current password</p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            New Password
                        </label>
                        <input type="password" name="password"
                               placeholder="Enter new password (min. 8 characters)"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Confirm New Password
                        </label>
                        <input type="password" name="password_confirmation"
                               placeholder="Re-enter new password"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Account Stats -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Statistics</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Member Since</p>
                        <p class="font-semibold text-gray-900">{{ $adminUser->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Last Login</p>
                        <p class="font-semibold text-gray-900">
                            {{ $adminUser->last_login_at ? $adminUser->last_login_at->format('M d, Y h:i A') : 'Never' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-4 border-t">
                <div class="flex gap-3">
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition">
                        Update Admin User
                    </button>
                    <a href="{{ route('admin.admin-users.index') }}"
                       class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">
                        Cancel
                    </a>
                </div>

                @if($adminUser->id !== auth()->guard('admin')->id())
                <form method="POST" action="{{ route('admin.admin-users.destroy', $adminUser) }}"
                      onsubmit="return confirm('⚠️ Are you sure you want to delete this admin user? This action cannot be undone!');"
                      class="ml-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition">
                        Delete User
                    </button>
                </form>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Warning Box (if editing yourself) -->
@if($adminUser->id === auth()->guard('admin')->id())
<div class="max-w-2xl mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <h4 class="font-semibold text-blue-800 mb-2 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
        </svg>
        You're editing your own account
    </h4>
    <p class="text-sm text-blue-700">
        Be careful when changing your role or deactivating your account.
        You won't be able to delete your own account for security reasons.
    </p>
</div>
@endif
@endsection
