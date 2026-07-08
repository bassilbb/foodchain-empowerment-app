@extends('admin.layouts.app')

@section('title', 'Edit Incident - Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Incident: {{ $incident->incident_number }}</h1>
            <p class="text-muted">Update incident details</p>
        </div>
        <a href="{{ route('admin.incidents.show', $incident) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <form action="{{ route('admin.incidents.update', $incident) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Incident Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Branch <span class="text-danger">*</span></label>
                                <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ $incident->branch_id == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }} ({{ $branch->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Department <span class="text-danger">*</span></label>
                                <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                                    <option value="{{ $incident->department_id }}">{{ $incident->department?->name }}</option>
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                    value="{{ old('title', $incident->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                    rows="5" required>{{ old('description', $incident->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ $incident->category == $category ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $category)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Priority <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                    @foreach($priorities as $priority)
                                        <option value="{{ $priority }}" {{ $incident->priority == $priority ? 'selected' : '' }}>
                                            {{ ucfirst($priority) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="open" {{ $incident->status == 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ $incident->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="escalated" {{ $incident->status == 'escalated' ? 'selected' : '' }}>Escalated</option>
                                    <option value="pending" {{ $incident->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="resolved" {{ $incident->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ $incident->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control" 
                                    value="{{ old('location', $incident->location) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Assigned To</label>
                                <select name="assigned_to" id="staff_id" class="form-select">
                                    <option value="">Unassigned</option>
                                    @foreach($staff as $s)
                                        <option value="{{ $s->admin_user_id }}" {{ $incident->assigned_to == $s->admin_user_id ? 'selected' : '' }}>
                                            {{ $s->adminUser?->name ?? 'Unknown' }} - {{ $s->position }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Reporter Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Reported By (User ID)</label>
                            <input type="number" name="reported_by" class="form-control" 
                                value="{{ old('reported_by', $incident->reported_by) }}">
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-save"></i> Update Incident
                        </button>
                        <a href="{{ route('admin.incidents.show', $incident) }}" class="btn btn-secondary w-100">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const selectedBranchId = '{{ $incident->branch_id }}';
    const selectedDepartmentId = '{{ $incident->department_id }}';
    
    // When branch changes, fetch departments
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
                        const selected = dept.id == selectedDepartmentId ? 'selected' : '';
                        $departmentSelect.append(`<option value="${dept.id}" ${selected}>${dept.name} (${dept.code})</option>`);
                    });
                    $departmentSelect.prop('disabled', false);
                } else {
                    $departmentSelect.html('<option value="">No departments available</option>');
                }
            });
        }
    });

    // When department changes, fetch staff
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
            });
        } else {
            $staffSelect.html('<option value="">Select Staff</option>').prop('disabled', true);
        }
    });
});
</script>
@endpush
@endsection
