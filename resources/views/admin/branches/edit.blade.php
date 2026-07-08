@extends('admin.layouts.app')

@section('title', 'Edit Branch')
@section('header', 'Edit Branch')
@section('subheader', 'Update branch information')

@section('content')
<div style="max-width: 48rem;">
    <form method="POST" action="{{ route('admin.branches.update', $branch) }}">
        @csrf
        @method('PUT')
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Branch Information</h3>
            </div>
            <div class="page-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Name <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $branch->name) }}" class="form-input" required>
                        @error('name')<p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Code <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="code" value="{{ old('code', $branch->code) }}" class="form-input" required>
                        @error('code')<p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-input" rows="2">{{ old('address', $branch->address) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" name="city" value="{{ old('city', $branch->city) }}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">State</label>
                        <input type="text" name="state" value="{{ old('state', $branch->state) }}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $branch->email) }}" class="form-input">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label style="display: flex; align-items: center; gap: 0.75rem;">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $branch->is_active) ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: #10b981;">
                            <span style="font-weight: 500; color: #475569;">Active</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Branch</button>
        </div>
    </form>
</div>
@endsection
