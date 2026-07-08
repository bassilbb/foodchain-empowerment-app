{{-- resources/views/admin/admin-users/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Create Admin User')
@section('page-title', 'Add New Admin User')
@section('header', 'Admin Users')
@section('subheader', 'Create new administrator account')

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
    .btn-primary { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-weight: 600; padding: 14px 28px; border: none; border-radius: 12px; cursor: pointer; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35); transition: all 0.3s ease; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.45); }
    .btn-secondary { background: linear-gradient(180deg, #f1f5f9, #e2e8f0); color: #64748b; font-weight: 600; padding: 14px 28px; border: 1px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
    .btn-secondary:hover { background: #e2e8f0; }
    .section-title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 8px; }
    .info-card { background: linear-gradient(135deg, #fef3c7, #fde68a); border: 1px solid #fbbf24; border-radius: 16px; padding: 20px; }
    .info-card-title { font-size: 14px; font-weight: 700; color: #92400e; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    .info-item { font-size: 13px; color: #78350f; margin-bottom: 6px; }
    .info-item strong { font-weight: 600; }
    .security-card { background: linear-gradient(135deg, #fee2e2, #fecaca); border: 1px solid #f87171; border-radius: 16px; padding: 20px; }
    .security-title { font-size: 14px; font-weight: 700; color: #991b1b; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    .security-text { font-size: 13px; color: #b91c1c; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
</style>

<div style="max-width: 720px; margin: 0 auto;">
    <div class="form-card">
        <form method="POST" action="{{ route('admin.admin-users.store') }}">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <!-- Name -->
                <div>
                    <label class="label">Full Name <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="John Doe" class="input-field {{ $errors->has('name') ? 'error' : '' }}">
                    @error('name')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="label">Email Address <span class="required">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="admin@example.com" class="input-field {{ $errors->has('email') ? 'error' : '' }}">
                    @error('email')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="label">Phone Number <span class="optional">(Optional)</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+234 800 000 0000" class="input-field">
                </div>

                <!-- Role -->
                <div>
                    <label class="label">Admin Role <span class="required">*</span></label>
                    <select name="role" required class="input-field {{ $errors->has('role') ? 'error' : '' }}">
                        <option value="">Select a role...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-row">
                    <!-- Password -->
                    <div>
                        <label class="label">Password <span class="required">*</span></label>
                        <input type="password" name="password" required placeholder="Minimum 8 characters" class="input-field {{ $errors->has('password') ? 'error' : '' }}">
                        @error('password')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="label">Confirm Password <span class="required">*</span></label>
                        <input type="password" name="password_confirmation" required placeholder="Re-enter password" class="input-field">
                    </div>
                </div>

                <!-- Role Permissions Info -->
                <div class="info-card">
                    <h4 class="info-card-title">
                        <svg style="width: 18px; height: 18px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                        Role Permissions
                    </h4>
                    <div class="info-item"><strong>Super Admin:</strong> Full access - user management, settings, all modules</div>
                    <div class="info-item"><strong>Inventory Manager:</strong> Inventory, food items, distributions, view users</div>
                    <div class="info-item"><strong>Customer Support:</strong> View users/payments, handle support tickets</div>
                </div>

                <!-- Submit Buttons -->
                <div style="display: flex; gap: 16px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
                    <button type="submit" class="btn-primary">Create Admin User</button>
                    <a href="{{ route('admin.admin-users.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Security Notice -->
    <div class="security-card" style="margin-top: 24px;">
        <h4 class="security-title">
            <svg style="width: 18px; height: 18px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
            Security Notice
        </h4>
        <p class="security-text">Admin users have elevated privileges. Only create accounts for trusted personnel. All admin actions are logged for security purposes.</p>
    </div>
</div>
@endsection