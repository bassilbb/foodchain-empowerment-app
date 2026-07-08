{{-- resources/views/admin/admin-users/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Edit Admin User')
@section('page-title', 'Edit Admin: ' . $adminUser->name)
@section('header', 'Admin Users')
@section('subheader', 'Update administrator account')

@section('content')
<style>
    .form-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); padding: 32px; }
    .input-field { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; font-size: 14px; transition: all 0.3s ease; width: 100%; box-sizing: border-box; }
    .input-field:focus { background: #ffffff; border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); outline: none; }
    .input-field.error { border-color: #ef4444; box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1); }
    .label { display: block; font-size: 14px; font-weight: 500; color: #475569; margin-bottom: 8px; }
    .required { color: #ef4444; }
    .optional { color: #94a3b8; font-size: 12px; }
    .error-text { color: #ef4444; font-size: 12px; margin-top: 6px; }
    .checkbox-label { display: flex; align-items: center; gap: 12px; font-size: 14px; color: #475569; cursor: pointer; margin-bottom: 8px; }
    .checkbox-label input { width: 20px; height: 20px; accent-color: #10b981; }
    .checkbox-desc { font-size: 12px; color: #94a3b8; margin-left: 32px; }
    .section-title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .stat-card { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; }
    .stat-label { font-size: 12px; color: #64748b; margin-bottom: 4px; }
    .stat-value { font-size: 16px; font-weight: 600; color: #1e293b; }
    .info-card { background: linear-gradient(135deg, #dbeafe, #bfdbfe); border: 1px solid #93c5fd; border-radius: 16px; padding: 20px; }
    .info-title { font-size: 14px; font-weight: 700; color: #1e40af; }
    .info-text { font-size: 13px; color: #1e3a8a; margin-top: 8px; }
</style>

<div style="max-width: 800px; margin: 0 auto;">
    <div class="form-card">
        <form method="POST" action="{{ route('admin.admin-users.update', $adminUser) }}">
            @csrf
            @method('PUT')
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <!-- Account Info Section -->
                <div>
                    <h3 class="section-title">Account Information</h3>
                    
                    <div class="form-row">
                        <!-- Name -->
                        <div>
                            <label class="label">Full Name <span class="required">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $adminUser->name) }}" required class="input-field {{ $errors->has('name') ? 'error' : '' }}">
                            @error('name')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="label">Email Address <span class="required">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $adminUser->email) }}" required class="input-field {{ $errors->has('email') ? 'error' : '' }}">
                            @error('email')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row" style="margin-top: 16px;">
                        <!-- Phone -->
                        <div>
                            <label class="label">Phone Number <span class="optional">(Optional)</span></label>
                            <input type="text" name="phone" value="{{ old('phone', $adminUser->phone) }}" class="input-field">
                        </div>

                        <!-- Role -->
                        <div>
                            <label class="label">Admin Role <span class="required">*</span></label>
                            <select name="role" required class="input-field">
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ old('role', $adminUser->role) === $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div style="margin-top: 16px;">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $adminUser->is_active) ? 'checked' : '' }}>
                            <span>Active Account</span>
                        </label>
                        <p class="checkbox-desc">Inactive accounts cannot login to the system</p>
                    </div>
                </div>

                <!-- Password Section -->
                <div style="border-top: 1px solid #e2e8f0; padding-top: 24px;">
                    <h3 class="section-title">Change Password</h3>
                    <p style="font-size: 14px; color: #64748b; margin-bottom: 16px;">Leave blank to keep the current password</p>
                    
                    <div class="form-row">
                        <div>
                            <label class="label">New Password</label>
                            <input type="password" name="password" placeholder="Enter new password (min. 8 characters)" class="input-field {{ $errors->has('password') ? 'error' : '' }}">
                            @error('password')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" placeholder="Re-enter new password" class="input-field">
                        </div>
                    </div>
                </div>

                <!-- Account Stats -->
                <div style="border-top: 1px solid #e2e8f0; padding-top: 24px;">
                    <h3 class="section-title">Account Statistics</h3>
                    <div class="form-row">
                        <div class="stat-card">
                            <p class="stat-label">Member Since</p>
                            <p class="stat-value">{{ $adminUser->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="stat-card">
                            <p class="stat-label">Last Login</p>
                            <p class="stat-value">{{ $adminUser->last_login_at ? $adminUser->last_login_at->format('M d, Y h:i A') : 'Never' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 16px; border-top: 1px solid #e2e8f0;">
                    <div style="display: flex; gap: 12px;">
                        <button type="submit" class="btn-primary">Update Admin User</button>
                        <a href="{{ route('admin.admin-users.index') }}" class="btn-secondary">Cancel</a>
                    </div>

                    @if($adminUser->id !== auth()->guard('admin')->id())
                    <form method="POST" action="{{ route('admin.admin-users.destroy', $adminUser) }}" onsubmit="return confirm('Are you sure you want to delete this admin user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger">Delete User</button>
                    </form>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Warning Box (if editing yourself) -->
    @if($adminUser->id === auth()->guard('admin')->id())
    <div class="info-card" style="margin-top: 24px;">
        <h4 class="info-title">You're editing your own account</h4>
        <p class="info-text">Be careful when changing your role or deactivating your account. You won't be able to delete your own account for security reasons.</p>
    </div>
    @endif
</div>
@endsection