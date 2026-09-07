@extends('admin.layouts.app')

@section('title', 'Marketplace Categories')
@section('header', 'Marketplace Categories')
@section('subheader', 'Organise the storefront into browsable food categories.')

@section('content')
<div class="page-header">
    <div>
        <h1>Categories</h1>
        <p>{{ number_format($categories->count()) }} category(ies).</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 1rem;">{{ session('success') }}</div>
@endif

<!-- Create form -->
<div class="page-card" style="margin-bottom: 2rem;">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Add Category</h3>
            <p class="page-card-subtitle">Create a top-level or child food category</p>
        </div>
    </div>
    <div class="page-card-body">
        <form method="POST" action="{{ route('admin.marketplace.categories.store') }}" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
            @csrf
            <div style="flex: 1; min-width: 180px;">
                <label class="form-label">Name *</label>
                <input type="text" name="name" required class="form-input" placeholder="e.g., Grains">
            </div>
            <div style="min-width: 180px;">
                <label class="form-label">Parent</label>
                <select name="parent_id" class="form-select">
                    <option value="">— None (Top level) —</option>
                    @foreach($parents as $parent)
                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 140px;">
                <label class="form-label">Icon</label>
                <input type="text" name="icon" maxlength="50" class="form-input" placeholder="emoji / icon">
            </div>
            <div style="min-width: 120px;">
                <label class="form-label">Sort Order</label>
                <input type="number" name="sort_order" class="form-input" value="0">
            </div>
            <div style="min-width: 240px; flex: 1;">
                <label class="form-label">Description</label>
                <input type="text" name="description" class="form-input" placeholder="Short description">
            </div>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding-bottom: 10px;">
                <input type="checkbox" name="is_active" value="1" checked>
                <span style="font-weight: 500; color: #475569;">Active</span>
            </label>
            <button type="submit" class="btn btn-primary">Create Category</button>
        </form>
    </div>
</div>

<!-- List -->
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">All Categories</h3>
            <p class="page-card-subtitle">Click Edit to modify an existing category</p>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Category</th>
                <th>Parent</th>
                <th>Products</th>
                <th>Sort</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
            <tr>
                <td>
                    <div style="font-weight: 600; color: #1e293b;">{{ $category->icon ? $category->icon.' ' : '' }}{{ $category->name }}</div>
                    @if($category->description)<div style="font-size: 12px; color: #94a3b8;">{{ Str::limit($category->description, 40) }}</div>@endif
                </td>
                <td>{{ $category->parent?->name ?? '—' }}</td>
                <td><span class="badge badge-primary">{{ number_format($category->products_count ?? $category->products->count()) }}</span></td>
                <td>{{ $category->sort_order }}</td>
                <td><span class="badge {{ $category->is_active ? 'badge-success' : 'badge-error' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td>
                    <button type="button" class="action-link" onclick="toggleEdit({{ $category->id }})">Edit</button>
                </td>
            </tr>
            <tr id="edit-{{ $category->id }}" style="display: none; background: #f8fafc;">
                <td colspan="6">
                    <form method="POST" action="{{ route('admin.marketplace.categories.update', $category) }}" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
                        @csrf
                        @method('PUT')
                        <div style="flex: 1; min-width: 160px;">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" value="{{ $category->name }}" required class="form-input">
                        </div>
                        <div style="min-width: 160px;">
                            <label class="form-label">Parent</label>
                            <select name="parent_id" class="form-select">
                                <option value="">— None —</option>
                                @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" {{ $category->parent_id === $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="min-width: 110px;">
                            <label class="form-label">Icon</label>
                            <input type="text" name="icon" maxlength="50" value="{{ $category->icon }}" class="form-input">
                        </div>
                        <div style="min-width: 100px;">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" value="{{ $category->sort_order }}" class="form-input">
                        </div>
                        <div style="min-width: 220px; flex: 1;">
                            <label class="form-label">Description</label>
                            <input type="text" name="description" value="{{ $category->description }}" class="form-input">
                        </div>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding-bottom: 10px;">
                            <input type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }}>
                            <span style="font-weight: 500; color: #475569;">Active</span>
                        </label>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEdit({{ $category->id }})">Cancel</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state">No categories yet. Create one above.</div></td></tr>
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
