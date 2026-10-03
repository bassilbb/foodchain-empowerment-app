@extends('layouts.user')

@section('title', 'Report Incident')
@section('header', 'Report Incident')
@section('subheader', 'Submit a new incident report')

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Report New Incident</h3>
            <p class="page-card-subtitle">Help us improve by reporting any issues you encounter</p>
        </div>
        <a href="{{ route('user.incidents.index') }}" class="btn btn-secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back
        </a>
    </div>
    <div class="page-card-body">
        <form action="{{ route('user.incidents.store') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                <div>
                    <div class="page-card" style="margin: 0;">
                        <div class="page-card-header">
                            <h4 class="page-card-title" style="margin: 0;">Incident Details</h4>
                        </div>
                        <div class="page-card-body">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label class="form-label">Branch <span style="color: #dc2626;">*</span></label>
                                    <select name="branch_id" id="branch_id" class="form-select" required>
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }} ({{ $branch->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Department</label>
                                    <select name="department_id" id="department_id" class="form-select" data-old="{{ old('department_id') }}">
                                        <option value="">Select Department (Optional)</option>
                                    </select>
                                    @error('department_id')
                                        <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group" style="grid-column: span 2;">
                                    <label class="form-label">Title <span style="color: #dc2626;">*</span></label>
                                    <input type="text" name="title" class="form-input" value="{{ old('title') }}" required placeholder="Brief description of the incident">
                                    @error('title')
                                        <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group" style="grid-column: span 2;">
                                    <label class="form-label">Description <span style="color: #dc2626;">*</span></label>
                                    <textarea name="description" class="form-input-textarea" rows="5" required placeholder="Please provide as much detail as possible">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Category <span style="color: #dc2626;">*</span></label>
                                    <select name="category" class="form-select" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                                {{ ucwords(str_replace('_', ' ', $category)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Priority <span style="color: #dc2626;">*</span></label>
                                    <select name="priority" class="form-select" required>
                                        <option value="">Select Priority</option>
                                        @foreach($priorities as $priority)
                                            <option value="{{ $priority }}" {{ old('priority') == $priority ? 'selected' : '' }}>
                                                {{ ucfirst($priority) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('priority')
                                        <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group" style="grid-column: span 2;">
                                    <label class="form-label">Location</label>
                                    <input type="text" name="location" class="form-input" value="{{ old('location') }}" placeholder="Specific location of the incident (optional)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="page-card" style="margin: 0;">
                        <div class="page-card-header">
                            <h4 class="page-card-title" style="margin: 0;">Tips</h4>
                        </div>
                        <div class="page-card-body">
                            <ul style="color: #64748b; font-size: 0.875rem; padding-left: 1.25rem; margin: 0 0 1rem 0;">
                                <li style="margin-bottom: 0.5rem;">Be specific about what happened</li>
                                <li style="margin-bottom: 0.5rem;">Include the date and time</li>
                                <li style="margin-bottom: 0.5rem;">Mention any staff involved</li>
                                <li style="margin-bottom: 0;">Provide your contact details if needed</li>
                            </ul>
                            <hr style="border-color: #f1f5f9; margin: 1.5rem 0;">
                            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-bottom: 0.75rem;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                Submit Report
                            </button>
                            <a href="{{ route('user.incidents.index') }}" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var branchSelect = document.getElementById('branch_id');
    var departmentSelect = document.getElementById('department_id');

    if (!branchSelect) return;

    var oldDeptId = departmentSelect.getAttribute('data-old');

    if (branchSelect.value) {
        loadDepartments(branchSelect.value, oldDeptId);
    }

    branchSelect.addEventListener('change', function() {
        loadDepartments(this.value, null);
    });

    function loadDepartments(branchId, selectedDeptId) {
        departmentSelect.innerHTML = '<option value="">Loading...</option>';
        departmentSelect.disabled = true;

        if (!branchId) {
            departmentSelect.innerHTML = '<option value="">Select Department (Optional)</option>';
            departmentSelect.disabled = true;
            return;
        }

        fetch('/api/v1/incidents/branches/' + branchId + '/departments')
            .then(function(r) { return r.json(); })
            .then(function(response) {
                if (response.success && response.data.length > 0) {
                    var html = '<option value="">Select Department (Optional)</option>';
                    response.data.forEach(function(dept) {
                        var sel = selectedDeptId && String(dept.id) === String(selectedDeptId) ? ' selected' : '';
                        html += '<option value="' + dept.id + '"' + sel + '>' + dept.name + ' (' + dept.code + ')</option>';
                    });
                    departmentSelect.innerHTML = html;
                    departmentSelect.disabled = false;
                } else {
                    departmentSelect.innerHTML = '<option value="">No departments available</option>';
                }
            })
            .catch(function() {
                departmentSelect.innerHTML = '<option value="">Error loading departments</option>';
            });
    }
});
</script>
@endpush
@endsection