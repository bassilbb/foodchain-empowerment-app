@extends('admin.layouts.app')

@section('title', 'Marketplace Bulk Orders')
@section('header', 'Marketplace Bulk Orders')
@section('subheader', 'Manage community bulk-purchase campaigns.')

@section('content')
<div class="page-header">
    <div>
        <h1>Bulk Orders</h1>
        <p>{{ number_format($bulkOrders->total()) }} campaign(s).</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 1rem;">{{ session('success') }}</div>
@endif

<!-- Filters -->
<div class="filter-card">
    <form method="GET" action="{{ route('admin.marketplace.bulk-orders.index') }}" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
        <div style="min-width: 180px;">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                @foreach(\App\Models\Marketplace\BulkOrder::STATUSES as $key => $label)
                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.marketplace.bulk-orders.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="page-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Campaign</th>
                <th>Creator</th>
                <th>Items</th>
                <th>Progress</th>
                <th>Value</th>
                <th>Discount</th>
                <th>Target Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bulkOrders as $bulkOrder)
            <tr>
                <td>
                    <div style="font-weight: 600; color: #1e293b;">{{ $bulkOrder->title }}</div>
                    @if($bulkOrder->description)
                    <div style="font-size: 12px; color: #94a3b8; max-width: 240px;">{{ Str::limit($bulkOrder->description, 60) }}</div>
                    @endif
                </td>
                <td style="white-space: nowrap;">
                    <div style="font-weight: 600; color: #475569;">{{ $bulkOrder->creator?->name ?? 'Unknown' }}</div>
                    <div style="font-size: 12px; color: #94a3b8;">{{ $bulkOrder->total_contributors }} contributors</div>
                </td>
                <td>
                    <span class="badge badge-primary">{{ $bulkOrder->items->count() }} item(s)</span>
                    <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;">
                        @php
                            $bulkItemsList = $bulkOrder->items->take(2)->pluck('product.name')->implode(', ');
                            $bulkExtraCount = $bulkOrder->items->count() - 2;
                        @endphp
                        {{ $bulkItemsList }}@if($bulkExtraCount > 0)
                            +{{ $bulkExtraCount }} more
                        @endif
                    </div>
                </td>
                <td style="min-width: 140px;">
                    @php $pct = $bulkOrder->total_required_qty > 0 ? min(100, ($bulkOrder->total_contributed_qty / $bulkOrder->total_required_qty) * 100) : 0; @endphp
                    <div style="display: flex; justify-content: space-between; font-size: 12px; color: #64748b; margin-bottom: 4px;">
                        <span>{{ number_format($bulkOrder->total_contributed_qty) }} / {{ number_format($bulkOrder->total_required_qty) }}</span>
                        <span>{{ number_format($pct, 0) }}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill {{ $pct >= 100 ? 'emerald' : 'amber' }}" style="width: {{ $pct }}%;"></div>
                    </div>
                </td>
                <td style="font-weight: 700; color: #059669; white-space: nowrap;">{{ format_currency($bulkOrder->total_value) }}</td>
                <td>{{ $bulkOrder->discount_percentage ? $bulkOrder->discount_percentage.'%' : '—' }}</td>
                <td style="white-space: nowrap; font-size: 13px;">{{ $bulkOrder->target_date?->format('M d, Y') ?? '—' }}</td>
                <td>
                    <span class="badge
                        @if($bulkOrder->status === 'completed') badge-success
                        @elseif($bulkOrder->status === 'cancelled') badge-error
                        @elseif($bulkOrder->status === 'in_progress') badge-info
                        @else badge-warning @endif">{{ ucfirst($bulkOrder->status) }}</span>
                </td>
                <td>
                    <button type="button" class="action-link" onclick="toggleEdit({{ $bulkOrder->id }})">Update</button>
                </td>
            </tr>
            <tr id="edit-{{ $bulkOrder->id }}" style="display: none; background: #f8fafc;">
                <td colspan="9">
                    <form method="POST" action="{{ route('admin.marketplace.bulk-orders.status', $bulkOrder) }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
                        @csrf
                        <div>
                            <label class="form-label">Update Status</label>
                            <select name="status" class="form-select" style="min-width: 160px;">
                                @foreach(\App\Models\Marketplace\BulkOrder::STATUSES as $key => $label)
                                <option value="{{ $key }}" {{ $bulkOrder->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEdit({{ $bulkOrder->id }})">Cancel</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="9"><div class="empty-state">No bulk orders found</div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination">
    {{ $bulkOrders->links() }}
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
