@extends('admin.layouts.app')

@section('title', 'Add Staff')
@section('header', 'Add Staff')
@section('subheader', 'Create a new staff member')

@section('content')
@if(session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif

<div style="max-width: 48rem;">
    <form method="POST" action="{{ route('admin.staff.store') }}">
        @csrf
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Staff Information</h3>
            </div>
            <div class="page-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Staff Number <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="staff_number" value="{{ old('staff_number') }}" class="form-input" required placeholder="e.g. STF-0001">
                        @error('staff_number')<p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Position <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="position" value="{{ old('position') }}" class="form-input" required placeholder="e.g. Operations Officer">
                        @error('position')<p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Department <span style="color: #dc2626;">*</span></label>
                        <select name="department_id" class="form-select" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }} ({{ $department->branch?->name ?? 'No branch' }})
                            </option>
                            @endforeach
                        </select>
                        @error('department_id')<p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-input" placeholder="+234 800 000 0000">
                        @error('phone')<p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Linked Admin Account (Optional)</label>
                        <select name="admin_user_id" class="form-select">
                            <option value="">— None —</option>
                            @foreach($adminUsers as $adminUser)
                            <option value="{{ $adminUser->id }}" {{ old('admin_user_id') == $adminUser->id ? 'selected' : '' }}>
                                {{ $adminUser->name }} ({{ $adminUser->email }})
                            </option>
                            @endforeach
                        </select>
                        @error('admin_user_id')<p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 0.75rem;">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: #10b981;">
                            <span style="font-weight: 500; color: #475569;">Active</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Staff</button>
        </div>
    </form>
</div>
@endsection