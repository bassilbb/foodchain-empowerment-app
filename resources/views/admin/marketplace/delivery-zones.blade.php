@extends('admin.layouts.app')

@section('title', 'Marketplace Delivery Zones')
@section('header', 'Marketplace Delivery Zones')
@section('subheader', 'Define delivery fees and estimates per state and LGA.')

@section('content')
<div class="page-header">
    <div>
        <h1>Delivery Zones</h1>
        <p>{{ number_format($zones->count()) }} zone(s) configured.</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 1rem;">{{ session('success') }}</div>
@endif

<!-- Create form -->
<div class="page-card" style="margin-bottom: 2rem;">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Add Delivery Zone</h3>
            <p class="page-card-subtitle">Base fee and estimated delivery time for an area</p>
        </div>
    </div>
    <div class="page-card-body">
        <form method="POST" action="{{ route('admin.marketplace.delivery-zones.store') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
            @csrf
            <div style="flex: 1; min-width: 160px;">
                <label class="form-label">Name *</label>
                <input type="text" name="name" required class="form-input" placeholder="e.g., Ikeja Mainland">
            </div>
            <div style="min-width: 140px;">
                <label class="form-label">State *</label>
                <input type="text" name="state" required class="form-input" placeholder="Lagos">
            </div>
            <div style="min-width: 140px;">
                <label class="form-label">LGA</label>
                <input type="text" name="lga" class="form-input" placeholder="Ikeja">
            </div>
            <div style="min-width: 130px;">
                <label class="form-label">Fee (₦) *</label>
                <input type="number" name="delivery_fee" step="0.01" min="0" required class="form-input" placeholder="1500">
            </div>
            <div style="min-width: 130px;">
                <label class="form-label">Min Order (₦)</label>
                <input type="number" name="min_order_amount" step="0.01" min="0" class="form-input" placeholder="Free above ₦50,000">
            </div>
            <div style="min-width: 110px;">
                <label class="form-label">Est. Days *</label>
                <input type="number" name="estimated_days" min="1" max="14" required class="form-input" value="1">
            </div>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding-bottom: 10px;">
                <input type="checkbox" name="is_active" value="1" checked>
                <span style="font-weight: 500; color: #475569;">Active</span>
            </label>
            <button type="submit" class="btn btn-primary">Add Zone</button>
        </form>
    </div>
</div>

<!-- List -->
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">All Zones</h3>
            <p class="page-card-subtitle">Grouped by state; click Edit to modify a zone</p>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Zone</th>
                <th>State</th>
                <th>LGA</th>
                <th>Fee</th>
                <th>Min Order</th>
                <th>Est. Days</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($zones as $zone)
            <tr>
                <td style="font-weight: 600; color: #1e293b;">{{ $zone->name }}</td>
                <td>{{ $zone->state }}</td>
                <td>{{ $zone->lga ?? '—' }}</td>
                <td style="font-weight: 700; color: #059669;">{{ format_currency($zone->delivery_fee) }}</td>
                <td>{{ $zone->min_order_amount ? format_currency($zone->min_order_amount) : '—' }}</td>
                <td>{{ $zone->estimated_days }} day(s)</td>
                <td><span class="badge {{ $zone->is_active ? 'badge-success' : 'badge-error' }}">{{ $zone->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td><button type="button" class="action-link" onclick="toggleEdit({{ $zone->id }})">Edit</button></td>
            </tr>
            <tr id="edit-{{ $zone->id }}" style="display: none; background: #f8fafc;">
                <td colspan="8">
                    <form method="POST" action="{{ route('admin.marketplace.delivery-zones.update', $zone) }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
                        @csrf
                        @method('PUT')
                        <div><label class="form-label">Name</label><input type="text" name="name" value="{{ $zone->name }}" required class="form-input" style="width: 150px;"></div>
                        <div><label class="form-label">State</label><input type="text" name="state" value="{{ $zone->state }}" required class="form-input" style="width: 110px;"></div>
                        <div><label class="form-label">LGA</label><input type="text" name="lga" value="{{ $zone->lga }}" class="form-input" style="width: 110px;"></div>
                        <div><label class="form-label">Fee</label><input type="number" name="delivery_fee" step="0.01" min="0" value="{{ $zone->delivery_fee }}" required class="form-input" style="width: 100px;"></div>
                        <div><label class="form-label">Min Order</label><input type="number" name="min_order_amount" step="0.01" min="0" value="{{ $zone->min_order_amount }}" class="form-input" style="width: 100px;"></div>
                        <div><label class="form-label">Est. Days</label><input type="number" name="estimated_days" min="1" max="14" value="{{ $zone->estimated_days }}" required class="form-input" style="width: 80px;"></div>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; padding-bottom: 10px;">
                            <input type="checkbox" name="is_active" value="1" {{ $zone->is_active ? 'checked' : '' }}>
                            <span style="font-size: 12px;">Active</span>
                        </label>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEdit({{ $zone->id }})">Cancel</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8"><div class="empty-state">No delivery zones yet. Add one above.</div></td></tr>
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
