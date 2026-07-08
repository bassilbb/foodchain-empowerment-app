@extends('admin.layouts.app')

@section('title', 'Create Incident - Admin')

@section('content')
<style>
    .header-card { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.2); }
    .header-title { font-size: 24px; font-weight: 700; color: white; margin: 0 0 8px 0; }
    .header-subtitle { color: rgba(255,255,255,0.7); margin: 0; font-size: 14px; }
    .btn-back { background: linear-gradient(180deg, #f1f5f9, #e2e8f0); color: #64748b; font-weight: 600; padding: 12px 24px; border: none; border-radius: 12px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease; }
    .btn-back:hover { background: #e2e8f0; transform: translateX(-4px); }
    .form-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); padding: 32px; }
    .input-field { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; font-size: 14px; transition: all 0.3s ease; width: 100%; box-sizing: border-box; }
    .input-field:focus { background: #ffffff; border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); outline: none; }
    .input-field:disabled { background: #e2e8f0; cursor: not-allowed; opacity: 0.7; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
    .label { display: block; font-size: 14px; font-weight: 500; color: #475569; margin-bottom: 8px; }
    .required { color: #ef4444; }
    .section-title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0; }
    .btn-primary { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-weight: 600; padding: 14px 28px; border: none; border-radius: 12px; cursor: pointer; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35); transition: all 0.3s ease; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.45); }
    .btn-secondary { background: linear-gradient(180deg, #f1f5f9, #e2e8f0); color: #64748b; font-weight: 600; padding: 14px 28px; border: 1px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: block; text-align: center; }
    .btn-secondary:hover { background: #e2e8f0; }
    .reporter-card { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; }
    .divider { height: 1px; background: #e2e8f0; margin: 24px 0; }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div class="header-card" style="flex: 1; margin-right: 24px; margin-bottom: 0;">
        <h1 class="header-title">Create New Incident</h1>
        <p class="header-subtitle">Fill in the details to report a new incident</p>
    </div>
    <a href="{{ route('admin.incidents.index') }}" class="btn-back">
        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back
    </a>
</div>

<form action="{{ route('admin.incidents.store') }}" method="POST">
    @csrf
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <div class="form-card">
            <h3 class="section-title">Incident Details</h3>
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div class="grid-2">
                    <div>
                        <label class="label">Branch <span class="required">*</span></label>
                        <select name="branch_id" id="branch_id" class="input-field" required>
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }} ({{ $branch->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label">Department <span class="required">*</span></label>
                        <select name="department_id" id="department_id" class="input-field" required disabled>
                            <option value="">Select Department</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="label">Title <span class="required">*</span></label>
                    <input type="text" name="title" class="input-field" value="{{ old('title') }}" required placeholder="Brief description of the incident">
                </div>

                <div>
                    <label class="label">Description <span class="required">*</span></label>
                    <textarea name="description" class="input-field" rows="4" required placeholder="Detailed description of the incident">{{ old('description') }}</textarea>
                </div>

                <div class="grid-2">
                    <div>
                        <label class="label">Category <span class="required">*</span></label>
                        <select name="category" class="input-field" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $category)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label">Priority <span class="required">*</span></label>
                        <select name="priority" class="input-field" required>
                            <option value="">Select Priority</option>
                            @foreach($priorities as $priority)
                                <option value="{{ $priority }}" {{ old('priority') == $priority ? 'selected' : '' }}>
                                    {{ ucfirst($priority) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label class="label">Location</label>
                        <input type="text" name="location" class="input-field" value="{{ old('location') }}" placeholder="Specific location of the incident">
                    </div>

                    <div>
                        <label class="label">Assign To</label>
                        <select name="assigned_to" id="staff_id" class="input-field" disabled>
                            <option value="">Select Staff</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="reporter-card">
                <h3 class="section-title" style="margin-bottom: 16px;">Reporter Information</h3>
                <div>
                    <label class="label">Reported By (User ID)</label>
                    <input type="number" name="reported_by" class="input-field" value="{{ old('reported_by') }}" placeholder="Optional - User ID">
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                <button type="submit" class="btn-primary">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Create Incident
                </button>
                <a href="{{ route('admin.incidents.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
$(document).ready(function() {
    $('#branch_id').on('change', function() {
        const branchId = $(this).val();
        const $departmentSelect = $('#department_id');
        const $staffSelect = $('#staff_id');
        
        $departmentSelect.html('<option value="">Loading...</option>').prop('disabled', true);
        $staffSelect.html('<option value="">Select Staff</option>').prop('disabled', true);
        
        if (branchId) {
            $.get(`/api/v1/incidents/branches/${branchId}/departments`, function(response) {
                if (response.success && response.data.length > 0) {
                    $departmentSelect.html('<option value="">Select Department</option>');
                    response.data.forEach(function(dept) {
                        $departmentSelect.append(`<option value="${dept.id}">${dept.name} (${dept.code})</option>`);
                    });
                    $departmentSelect.prop('disabled', false);
                } else {
                    $departmentSelect.html('<option value="">No departments available</option>');
                }
            }).fail(function() {
                $departmentSelect.html('<option value="">Error loading departments</option>');
            });
        } else {
            $departmentSelect.html('<option value="">Select Department</option>').prop('disabled', true);
        }
    });

    $('#department_id').on('change', function() {
        const departmentId = $(this).val();
        const $staffSelect = $('#staff_id');
        
        $staffSelect.html('<option value="">Loading...</option>').prop('disabled', true);
        
        if (departmentId) {
            $.get(`/api/v1/incidents/departments/${departmentId}/staff`, function(response) {
                if (response.success && response.data.length > 0) {
                    $staffSelect.html('<option value="">Select Staff</option>');
                    response.data.forEach(function(staff) {
                        $staffSelect.append(`<option value="${staff.id}">${staff.name} - ${staff.position}</option>`);
                    });
                    $staffSelect.prop('disabled', false);
                } else {
                    $staffSelect.html('<option value="">No staff available</option>');
                }
            }).fail(function() {
                $staffSelect.html('<option value="">Error loading staff</option>');
            });
        } else {
            $staffSelect.html('<option value="">Select Staff</option>').prop('disabled', true);
        }
    });
});
</script>
@endpush
@endsection