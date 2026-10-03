@extends('admin.layouts.app')

@section('title', 'Marketplace Brands')
@section('header', 'Marketplace Brands')
@section('subheader', 'Manage the brands attached to marketplace products.')

@section('content')
<div class="page-header">
    <div>
        <h1>Brands</h1>
        <p>{{ number_format($brands->count()) }} brand(s).</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 1rem;">{{ session('success') }}</div>
@endif

<!-- Create form -->
<div class="page-card" style="margin-bottom: 2rem;">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Add Brand</h3>
            <p class="page-card-subtitle">Create a new product brand</p>
        </div>
    </div>
    <div class="page-card-body">
        <form method="POST" action="{{ route('admin.marketplace.brands.store') }}" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
            @csrf
            <div style="flex: 1; min-width: 200px;">
                <label class="form-label">Name *</label>
                <input type="text" name="name" required class="form-input" placeholder="e.g., Golden Penny">
            </div>
            <div style="flex: 2; min-width: 280px;">
                <label class="form-label">Description</label>
                <input type="text" name="description" class="form-input" placeholder="Short brand description">
            </div>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding-bottom: 10px;">
                <input type="checkbox" name="is_active" value="1" checked>
                <span style="font-weight: 500; color: #475569;">Active</span>
            </label>
            <button type="submit" class="btn btn-primary">Create Brand</button>
        </form>
    </div>
</div>

<!-- List -->
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">All Brands</h3>
            <p class="page-card-subtitle">Click Edit to modify a brand</p>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Brand</th>
                <th>Description</th>
                <th>Products</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($brands as $brand)
            <tr>
                <td style="font-weight: 600; color: #1e293b;">{{ $brand->name }}</td>
                <td style="color: #64748b;">{{ $brand->description ?? '—' }}</td>
                <td><span class="badge badge-primary">{{ number_format($brand->products_count) }}</span></td>
                <td><span class="badge {{ $brand->is_active ? 'badge-success' : 'badge-error' }}">{{ $brand->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td><button type="button" class="action-link" onclick="toggleEdit({{ $brand->id }})">Edit</button></td>
            </tr>
            <tr id="edit-{{ $brand->id }}" style="display: none; background: #f8fafc;">
                <td colspan="5">
                    <form method="POST" action="{{ route('admin.marketplace.brands.update', $brand) }}" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
                        @csrf
                        @method('PUT')
                        <div style="flex: 1; min-width: 180px;">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" value="{{ $brand->name }}" required class="form-input">
                        </div>
                        <div style="flex: 2; min-width: 260px;">
                            <label class="form-label">Description</label>
                            <input type="text" name="description" value="{{ $brand->description }}" class="form-input">
                        </div>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding-bottom: 10px;">
                            <input type="checkbox" name="is_active" value="1" {{ $brand->is_active ? 'checked' : '' }}>
                            <span style="font-weight: 500; color: #475569;">Active</span>
                        </label>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEdit({{ $brand->id }})">Cancel</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5"><div class="empty-state">No brands yet. Create one above.</div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
function toggleEdit(id) {
    const row = document.getElementById('edit-' + id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
</script>
@endpush
