@extends('admin.layouts.app')

@section('title', 'Marketplace Coupons')
@section('header', 'Marketplace Coupons')
@section('subheader', 'Create and manage discount codes for marketplace shoppers.')

@section('content')
<div class="page-header">
    <div>
        <h1>Coupons</h1>
        <p>{{ number_format($coupons->count()) }} coupon(s).</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 1rem;">{{ session('success') }}</div>
@endif

<!-- Create form -->
<div class="page-card" style="margin-bottom: 2rem;">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Add Coupon</h3>
            <p class="page-card-subtitle">Fixed or percentage discount codes with usage limits</p>
        </div>
    </div>
    <div class="page-card-body">
        <form method="POST" action="{{ route('admin.marketplace.coupons.store') }}" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
            @csrf
            <div style="min-width: 150px;">
                <label class="form-label">Code *</label>
                <input type="text" name="code" required class="form-input" placeholder="WELCOME10" maxlength="40">
            </div>
            <div style="min-width: 140px;">
                <label class="form-label">Type *</label>
                <select name="type" class="form-select">
                    <option value="fixed">Fixed (₦)</option>
                    <option value="percentage">Percentage (%)</option>
                </select>
            </div>
            <div style="min-width: 130px;">
                <label class="form-label">Value *</label>
                <input type="number" name="value" step="0.01" min="0" required class="form-input" placeholder="0">
            </div>
            <div style="min-width: 150px;">
                <label class="form-label">Min Order (₦)</label>
                <input type="number" name="min_order_amount" step="0.01" min="0" class="form-input">
            </div>
            <div style="min-width: 150px;">
                <label class="form-label">Max Discount (₦)</label>
                <input type="number" name="max_discount_amount" step="0.01" min="0" class="form-input">
            </div>
            <div style="min-width: 120px;">
                <label class="form-label">Usage Limit</label>
                <input type="number" name="usage_limit" min="1" class="form-input">
            </div>
            <div style="min-width: 120px;">
                <label class="form-label">Per User</label>
                <input type="number" name="per_user_limit" min="1" class="form-input" value="1">
            </div>
            <div style="min-width: 140px;">
                <label class="form-label">Starts At</label>
                <input type="datetime-local" name="starts_at" class="form-input">
            </div>
            <div style="min-width: 140px;">
                <label class="form-label">Expires At</label>
                <input type="datetime-local" name="expires_at" class="form-input">
            </div>
            <div style="display: flex; align-items: center; gap: 16px; padding-bottom: 10px;">
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                    <input type="checkbox" name="is_member_only" value="1">
                    <span style="font-size: 13px; font-weight: 500; color: #475569;">Members only</span>
                </label>
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span style="font-size: 13px; font-weight: 500; color: #475569;">Active</span>
                </label>
            </div>
            <button type="submit" class="btn btn-primary">Create Coupon</button>
        </form>
    </div>
</div>

<!-- List -->
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">All Coupons</h3>
            <p class="page-card-subtitle">Click Edit to modify a coupon</p>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Type</th>
                <th>Value</th>
                <th>Min / Max</th>
                <th>Usage</th>
                <th>Validity</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($coupons as $coupon)
            <tr>
                <td>
                    <span style="font-weight: 700; color: #1e293b; letter-spacing: 0.5px;">{{ $coupon->code }}</span>
                    @if($coupon->name)<div style="font-size: 12px; color: #94a3b8;">{{ $coupon->name }}</div>@endif
                </td>
                <td>{{ ucfirst($coupon->type) }}</td>
                <td style="font-weight: 600; color: #059669;">{{ $coupon->type === 'fixed' ? format_currency($coupon->value) : $coupon->value.'%' }}</td>
                <td style="font-size: 13px;">
                    {{ $coupon->min_order_amount ? 'Min '.format_currency($coupon->min_order_amount) : '—' }}
                    @if($coupon->max_discount_amount)<div style="color: #94a3b8;">Cap {{ format_currency($coupon->max_discount_amount) }}</div>@endif
                </td>
                <td style="font-size: 13px;">
                    {{ number_format($coupon->used_count) }} / {{ $coupon->usage_limit ?? '∞' }}
                    @if($coupon->per_user_limit)<div style="color: #94a3b8;">{{ $coupon->per_user_limit }} per user</div>@endif
                </td>
                <td style="font-size: 13px;">
                    @if($coupon->starts_at || $coupon->expires_at)
                        @if($coupon->starts_at)<div>{{ $coupon->starts_at->format('M d, Y') }} →</div>@endif
                        {{ $coupon->expires_at?->format('M d, Y') ?? 'No expiry' }}
                    @else <span style="color: #94a3b8;">Always</span> @endif
                </td>
                <td>
                    <span class="badge {{ $coupon->is_active ? 'badge-success' : 'badge-error' }}">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</span>
                    @if($coupon->is_member_only)<div style="font-size: 11px; color: #7c3aed; margin-top: 2px;">Member only</div>@endif
                </td>
                <td><button type="button" class="action-link" onclick="toggleEdit({{ $coupon->id }})">Edit</button></td>
            </tr>
            <tr id="edit-{{ $coupon->id }}" style="display: none; background: #f8fafc;">
                <td colspan="8">
                    <form method="POST" action="{{ route('admin.marketplace.coupons.update', $coupon) }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
                        @csrf
                        @method('PUT')
                        <div><label class="form-label">Code</label><input type="text" name="code" value="{{ $coupon->code }}" class="form-input" style="width: 130px;"></div>
                        <div>
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" style="width: 130px;">
                                <option value="fixed" {{ $coupon->type === 'fixed' ? 'selected' : '' }}>Fixed (₦)</option>
                                <option value="percentage" {{ $coupon->type === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                            </select>
                        </div>
                        <div><label class="form-label">Value</label><input type="number" name="value" step="0.01" min="0" value="{{ $coupon->value }}" class="form-input" style="width: 100px;"></div>
                        <div><label class="form-label">Min Order</label><input type="number" name="min_order_amount" step="0.01" min="0" value="{{ $coupon->min_order_amount }}" class="form-input" style="width: 110px;"></div>
                        <div><label class="form-label">Max Discount</label><input type="number" name="max_discount_amount" step="0.01" min="0" value="{{ $coupon->max_discount_amount }}" class="form-input" style="width: 110px;"></div>
                        <div><label class="form-label">Usage Limit</label><input type="number" name="usage_limit" min="1" value="{{ $coupon->usage_limit }}" class="form-input" style="width: 90px;"></div>
                        <div><label class="form-label">Per User</label><input type="number" name="per_user_limit" min="1" value="{{ $coupon->per_user_limit }}" class="form-input" style="width: 90px;"></div>
                        <div><label class="form-label">Starts</label><input type="datetime-local" name="starts_at" value="{{ $coupon->starts_at?->format('Y-m-d\TH:i') }}" class="form-input" style="width: 180px;"></div>
                        <div><label class="form-label">Expires</label><input type="datetime-local" name="expires_at" value="{{ $coupon->expires_at?->format('Y-m-d\TH:i') }}" class="form-input" style="width: 180px;"></div>
                        <div style="display: flex; align-items: center; gap: 12px; padding-bottom: 10px;">
                            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;"><input type="checkbox" name="is_member_only" value="1" {{ $coupon->is_member_only ? 'checked' : '' }}><span style="font-size: 12px;">Members</span></label>
                            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;"><input type="checkbox" name="is_active" value="1" {{ $coupon->is_active ? 'checked' : '' }}><span style="font-size: 12px;">Active</span></label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEdit({{ $coupon->id }})">Cancel</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8"><div class="empty-state">No coupons yet. Create one above.</div></td></tr>
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
