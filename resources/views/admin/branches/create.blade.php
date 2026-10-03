@extends('admin.layouts.app')

@section('title', 'Create Branch')
@section('header', 'Create Branch')
@section('subheader', 'Add a new branch location')

@section('content')
<div style="max-width: 48rem;">
    <form method="POST" action="{{ route('admin.branches.store') }}">
        @csrf
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Branch Information</h3>
            </div>
            <div class="page-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Name <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-input" required placeholder="e.g. Main Branch">
                        @error('name')<p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Code <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="code" value="{{ old('code') }}" class="form-input" required placeholder="e.g. MAIN">
                        @error('code')<p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-input" rows="2" placeholder="Full address">{{ old('address') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" name="city" value="{{ old('city') }}" class="form-input" placeholder="e.g. Lagos">
                    </div>
                    <div class="form-group">
                        <label class="form-label">State</label>
                        <input type="text" name="state" value="{{ old('state') }}" class="form-input" placeholder="e.g. Lagos State">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-input" placeholder="e.g. +234 800 000 0000">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="branch@example.com">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
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
            <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Branch</button>
        </div>
    </form>
</div>
@endsection
