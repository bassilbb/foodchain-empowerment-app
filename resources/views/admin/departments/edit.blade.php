@extends('admin.layouts.app')

@section('title', 'Edit Department')
@section('header', 'Edit Department')
@section('subheader', 'Update department information')

@section('content')
<div style="max-width: 48rem;">
    <form method="POST" action="{{ route('admin.departments.update', $department) }}">
        @csrf
        @method('PUT')
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Department Information</h3>
            </div>
            <div class="page-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Branch <span style="color: #dc2626;">*</span></label>
                        <select name="branch_id" class="form-select" required>
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $department->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }} ({{ $branch->code }})</option>
                            @endforeach
                        </select>
                        @error('branch_id')<p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Code <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="code" value="{{ old('code', $department->code) }}" class="form-input" required>
                        @error('code')<p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Name <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $department->name) }}" class="form-input" required>
                        @error('name')<p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-input" rows="3">{{ old('description', $department->description) }}</textarea>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label style="display: flex; align-items: center; gap: 0.75rem;">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $department->is_active) ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: #10b981;">
                            <span style="font-weight: 500; color: #475569;">Active</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Department</button>
        </div>
    </form>
</div>
@endsection
