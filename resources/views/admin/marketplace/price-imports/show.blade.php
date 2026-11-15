@extends('admin.layouts.app')

@section('title', 'Price Upload #'.$batch->id)
@section('header', 'Bulk Price Upload #'.$batch->id)
@section('subheader', $batch->original_filename.' — '.($batch->vendor?->name ?? 'Supplier'))

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="alert alert-error">
    <ul style="margin: 0; padding-left: 1.1rem;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@php
    $money = fn ($value) => $value === null ? '—' : '₦'.number_format((float) $value, 2);
    $options = $batch->options ?? [];
@endphp

<div class="page-header">
    <div>
        <h1>{{ $batch->original_filename }}</h1>
        <p>
            {{ strtoupper($batch->format) }} &middot; {{ $batch->vendor?->name ?? 'Unknown supplier' }}
            &middot; uploaded {{ $batch->created_at?->format('d M Y, H:i') }}
            by {{ $batch->admin?->name ?? 'admin' }}
        </p>
    </div>
    <div class="header-actions-btns">
        <a href="{{ route('admin.marketplace.price-imports.index') }}" class="btn btn-secondary">All uploads</a>
        <a href="{{ route('admin.marketplace.price-imports.export', [$batch, 'items']) }}" class="btn btn-secondary">Export records</a>
        <a href="{{ route('admin.marketplace.price-imports.export', [$batch, 'errors']) }}" class="btn btn-secondary">Error report</a>
        <a href="{{ route('admin.marketplace.price-imports.export', [$batch, 'history']) }}" class="btn btn-secondary">Price changes</a>
    </div>
</div>

<div class="page-card" style="margin-bottom: 1rem;">
    <div class="page-card-body" style="display: flex; flex-wrap: wrap; gap: 1rem 2rem; align-items: center; justify-content: space-between;">
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem 1.5rem; align-items: center; font-size: 0.85rem; color: #475569;">
            <span class="badge {{ $batch->statusBadge() }}">{{ $batch->statusLabel() }}</span>
            <span><strong>{{ number_format($batch->total_records) }}</strong> records in file</span>
            <span><strong>{{ number_format($batch->missing_count) }}</strong> missing from upload</span>
            <span><strong>{{ number_format($batch->invalid_count + $batch->ambiguous_count) }}</strong> need attention</span>
        </div>
        @if($batch->last_error)
            <div style="font-size: 0.8rem; color: #b91c1c; max-width: 560px;">{{ $batch->last_error }}</div>
        @endif
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <p class="stat-label">Matched</p>
        <p class="stat-value">{{ number_format($batch->matched_count) }}</p>
    </div>
    <div class="stat-card warning">
        <p class="stat-label">Price Increased</p>
        <p class="stat-value">{{ number_format($batch->increased_count) }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Price Reduced</p>
        <p class="stat-value">{{ number_format($batch->reduced_count) }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Unchanged</p>
        <p class="stat-value">{{ number_format($batch->unchanged_count) }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">New Products</p>
        <p class="stat-value">{{ number_format($batch->new_count) }}</p>
    </div>
    <div class="stat-card danger">
        <p class="stat-label">Invalid Records</p>
        <p class="stat-value">{{ number_format($batch->invalid_count) }}</p>
    </div>
    <div class="stat-card danger">
        <p class="stat-label">Matching Required</p>
        <p class="stat-value">{{ number_format($batch->ambiguous_count) }}</p>
    </div>
    <div class="stat-card danger">
        <p class="stat-label">Missing From Upload</p>
        <p class="stat-value">{{ number_format($batch->missing_count) }}</p>
    </div>
</div>

@if($batch->isProcessing())
    <div class="page-card" style="margin-bottom: 1rem;">
        <div class="page-card-body">
            <h3 style="margin-top: 0; color: #1e293b;">Processing in the background…</h3>
            <p style="color: #475569; font-size: 0.9rem;">The page refreshes automatically when the batch finishes. You can safely leave it open.</p>
            <div style="height: 10px; background: #e2e8f0; border-radius: 999px; overflow: hidden; margin: 1rem 0;">
                <div id="pi-progress-bar" style="height: 100%; width: 0%; background: linear-gradient(90deg, #0ea5e9, #22c55e); transition: width .4s ease;"></div>
            </div>
            <p style="font-size: 0.85rem; color: #475569;">
                <strong id="pi-processed">{{ number_format($batch->processed_count) }}</strong>
                of {{ number_format($batch->total_records) }} records processed
                &middot; <span id="pi-applied">{{ number_format($batch->applied_count) }}</span> updated
                &middot; <span id="pi-created">{{ number_format($batch->created_count) }}</span> created
                &middot; <span id="pi-failed">{{ number_format($batch->failed_count) }}</span> failed
            </p>
        </div>
    </div>
@endif

@if($batch->isAwaitingConfirmation() && $batch->items()->exists())
    <div class="page-card" style="margin-bottom: 1rem; border: 1px solid #bae6fd;">
        <div class="page-card-header">
            <h3>Confirm and apply</h3>
            <p>Review the tables below first. Nothing has been changed yet.</p>
        </div>
        <div class="page-card-body">
            <form method="POST" action="{{ route('admin.marketplace.price-imports.apply', $batch) }}" onsubmit="return confirm('Apply these price updates now? Every change will be recorded in the price history.');">
                @csrf

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label style="display: flex; gap: 0.6rem; align-items: flex-start; font-size: 0.875rem; color: #334155;">
                            <input type="checkbox" name="create_new_products" value="1" {{ data_get($options, 'create_new_products') ? 'checked' : '' }} style="margin-top: 0.2rem;">
                            <span>
                                <strong>Create new products</strong><br>
                                <span style="font-size: 0.8rem; color: #64748b;">Create the {{ number_format($batch->new_count) }} unmatched product(s) that have valid details. Records flagged for manual completion are skipped.</span>
                            </span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="default_category_id">Default category for new products</label>
                        <select name="default_category_id" id="default_category_id" class="form-select">
                            <option value="">Use the category in the file</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ data_get($options, 'default_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if($missing->isNotEmpty())
                    <div style="margin-top: 0.75rem; padding: 0.9rem 1rem; border: 1px solid #fde68a; background: #fffbeb; border-radius: 10px;">
                        <label style="display: flex; gap: 0.6rem; align-items: flex-start; font-size: 0.875rem; color: #92400e;">
                            <input type="checkbox" name="deactivate_missing" value="1" style="margin-top: 0.2rem;">
                            <span><strong>Deactivate the {{ number_format($missing->count()) }} product(s) missing from this upload</strong><br>
                            <span style="font-size: 0.8rem;">Only do this if the file is the supplier's complete catalogue. Products are never deleted, and stock, images and order history are kept.</span></span>
                        </label>
                        <label style="display: flex; gap: 0.6rem; align-items: flex-start; font-size: 0.875rem; color: #92400e; margin-top: 0.6rem;">
                            <input type="checkbox" name="confirm_complete_catalogue" value="1" style="margin-top: 0.2rem;">
                            <span><strong>I confirm this upload is a complete supplier catalogue.</strong></span>
                        </label>
                    </div>
                @endif

                <label style="display: flex; gap: 0.6rem; align-items: flex-start; font-size: 0.875rem; color: #334155; margin-top: 1rem;">
                    <input type="checkbox" name="confirm_review" value="1" required style="margin-top: 0.2rem;">
                    <span>I have reviewed the matches, price differences and invalid records below.</span>
                </label>

                <div style="display: flex; gap: 0.75rem; margin-top: 1rem;">
                    <button type="submit" class="btn btn-primary">Apply Price Updates</button>
                </div>
            </form>
        </div>
    </div>
@endif

@if($batch->isCompleted())
    <div class="page-card" style="margin-bottom: 1rem; border: 1px solid #bbf7d0;">
        <div class="page-card-header">
            <h3>Applied</h3>
            <p>
                {{ number_format($batch->applied_count) }} price(s) updated,
                {{ number_format($batch->created_count) }} product(s) created,
                {{ number_format($batch->failed_count) }} failed
                @if($batch->applied_at) &middot; {{ $batch->applied_at->format('d M Y, H:i') }} @endif
                @if($batch->deactivated_count > 0) &middot; {{ number_format($batch->deactivated_count) }} deactivated @endif
            </p>
        </div>
        @if($batch->canRollback())
            <div class="page-card-body">
                <form method="POST" action="{{ route('admin.marketplace.price-imports.rollback', $batch) }}" onsubmit="return confirm('Roll back this batch? Prices will be restored to their previous values. Orders and stock are not changed.');">
                    @csrf
                    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end;">
                        <div class="form-group" style="flex: 1; min-width: 260px;">
                            <label class="form-label" for="reason">Rollback reason <span style="color:#dc2626;">*</span></label>
                            <input type="text" name="reason" id="reason" class="form-input" required minlength="5" maxlength="500" placeholder="e.g. Wrong supplier file uploaded">
                        </div>
                        <label style="display: flex; gap: 0.5rem; align-items: center; font-size: 0.85rem; color: #475569;">
                            <input type="checkbox" name="confirm_rollback" value="1" required>
                            Confirm rollback
                        </label>
                        <button type="submit" class="btn" style="background: #dc2626; color: white;">Rollback Prices</button>
                    </div>
                    <p style="font-size: 0.78rem; color: #64748b; margin: 0.5rem 0 0;">Only prices are restored. Orders, stock, images and products created by this batch are left untouched.</p>
                </form>
            </div>
        @elseif($batch->status === \App\Models\Marketplace\PriceImportBatch::STATUS_ROLLED_BACK)
            <div class="page-card-body">
                <p style="font-size: 0.85rem; color: #475569; margin: 0;">
                    Rolled back {{ $batch->rolled_back_at?->format('d M Y, H:i') }}
                    by {{ $batch->rollbackAdmin?->name ?? 'admin' }}.
                    @if($batch->rollback_reason) Reason: {{ $batch->rollback_reason }}. @endif
                </p>
            </div>
        @endif
    </div>
@endif

<div class="page-card" style="margin-bottom: 1rem;">
    <div class="page-card-header" style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; justify-content: space-between;">
        <div>
            <h3>Uploaded records</h3>
            <p>Price changes and validation results for every row in the file.</p>
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 0.4rem;">
            <a href="{{ route('admin.marketplace.price-imports.show', $batch) }}" class="btn {{ ! $changeType ? 'btn-primary' : 'btn-secondary' }}" style="padding: 0.35rem 0.7rem; font-size: 0.78rem;">All ({{ number_format($batch->total_records) }})</a>
            @foreach(\App\Models\Marketplace\PriceImportItem::CHANGE_LABELS as $value => $label)
                <a href="{{ route('admin.marketplace.price-imports.show', [$batch, 'change_type' => $value]) }}" class="btn {{ $changeType === $value ? 'btn-primary' : 'btn-secondary' }}" style="padding: 0.35rem 0.7rem; font-size: 0.78rem;">
                    {{ $label }} ({{ number_format($counts[$value] ?? 0) }})
                </a>
            @endforeach
        </div>
    </div>
    <div class="page-card-body" style="padding: 0; overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Row</th>
                    <th>Product</th>
                    <th>Current</th>
                    <th>New</th>
                    <th>Difference</th>
                    <th>Status</th>
                    <th>Match</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td style="color: #94a3b8; font-size: 12px;">{{ $item->row_number }}</td>
                        <td>
                            <div style="font-weight: 600; color: #1e293b;">{{ $item->product_name ?: '—' }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">
                                {{ $item->sku ?: 'no SKU' }}
                                @if($item->supplier_product_code) &middot; code {{ $item->supplier_product_code }} @endif
                                @if($item->barcode) &middot; barcode {{ $item->barcode }} @endif
                            </div>
                            @if($item->product_id && $item->product)
                                <div style="font-size: 12px; color: #0ea5e9;">Matched product #{{ $item->product_id }} — {{ $item->product->name }}</div>
                            @endif
                        </td>
                        <td>{{ $money($item->current_price) }}</td>
                        <td style="font-weight: 600; color: #1e293b;">{{ $money($item->new_price) }}</td>
                        <td>
                            @if($item->difference !== null)
                                <span style="color: {{ $item->difference > 0 ? '#b45309' : ($item->difference < 0 ? '#0369a1' : '#64748b') }}; font-weight: 600;">
                                    {{ $item->difference > 0 ? '+' : '' }}{{ $money($item->difference) }}
                                </span>
                                @if($item->percent_change !== null)
                                    <div style="font-size: 11px; color: #94a3b8;">{{ $item->percent_change > 0 ? '+' : '' }}{{ number_format((float) $item->percent_change, 2) }}%</div>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $item->changeBadge() }}">{{ $item->changeLabel() }}</span>
                            @if($item->applied)
                                <div style="font-size: 11px; color: #16a34a; margin-top: 3px;">applied</div>
                            @endif
                        </td>
                        <td style="font-size: 12px; color: #64748b;">{{ $item->match_method ? ucfirst(str_replace('_', ' ', $item->match_method)) : '—' }}</td>
                        <td style="font-size: 12px; max-width: 320px;">
                            @foreach($item->errors ?? [] as $error)
                                <div style="color: #b91c1c;">{{ $error }}</div>
                            @endforeach
                            @foreach($item->warnings ?? [] as $warning)
                                <div style="color: #92400e;">{{ $warning }}</div>
                            @endforeach
                            @if($item->requires_manual_completion)
                                <div style="color: #b91c1c; font-weight: 600;">Manual completion required before creation.</div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem; color: #64748b;">No records match this filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
        <div class="page-card-body">{{ $items->links() }}</div>
    @endif
</div>

<div class="page-card" style="margin-bottom: 1rem;">
    <div class="page-card-header">
        <h3>Products Not Found in Latest Upload</h3>
        <p>These products belong to this supplier's previous catalogue but were absent from this file. They are never deleted automatically.</p>
    </div>
    <div class="page-card-body" style="padding: 0; overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Current price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($missing as $row)
                    <tr>
                        <td style="font-weight: 600; color: #1e293b;">{{ $row->product_name }}</td>
                        <td style="font-size: 12px; color: #64748b;">{{ $row->sku ?: '—' }}</td>
                        <td>{{ $money($row->current_price) }}</td>
                        <td>
                            <span class="badge {{ $row->action === 'deactivated' ? 'badge-error' : ($row->action === 'kept' ? 'badge-dark' : 'badge-warning') }}">
                                {{ ucfirst($row->action) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem; color: #64748b;">No missing products detected.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h3>Price change history for this batch</h3>
        <p>Every applied change, rollback and product creation is recorded here.</p>
    </div>
    <div class="page-card-body" style="padding: 0; overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Old price</th>
                    <th>New price</th>
                    <th>Difference</th>
                    <th>Source</th>
                    <th>Admin</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($histories as $history)
                    <tr>
                        <td style="font-size: 12px; color: #64748b;">{{ $history->created_at?->format('d M Y, H:i') }}</td>
                        <td>
                            <div style="font-weight: 600; color: #1e293b;">{{ $history->product_name }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">{{ $history->sku ?: '—' }}</div>
                        </td>
                        <td>{{ $money($history->old_price) }}</td>
                        <td style="font-weight: 600;">{{ $money($history->new_price) }}</td>
                        <td>{{ $history->difference === null ? '—' : ($history->difference > 0 ? '+' : '').$money($history->difference) }}</td>
                        <td style="font-size: 12px; color: #64748b;">{{ $history->sourceLabel() }}</td>
                        <td style="font-size: 12px; color: #64748b;">{{ $history->admin?->name ?? '—' }}</td>
                        <td><span class="badge {{ $history->status === 'rolled_back' ? 'badge-error' : ($history->status === 'created' ? 'badge-success' : 'badge-info') }}">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem; color: #64748b;">No price changes recorded for this batch yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($histories->hasPages())
        <div class="page-card-body">{{ $histories->links() }}</div>
    @endif
</div>

@if($batch->isProcessing())
<script>
(function () {
    const statusUrl = @json(route('admin.marketplace.price-imports.status', $batch));
    const total = {{ (int) $batch->total_records }};

    const timer = setInterval(async function () {
        try {
            const response = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
            if (!response.ok) {
                return;
            }

            const data = await response.json();

            document.getElementById('pi-processed').textContent = Number(data.processed).toLocaleString();
            document.getElementById('pi-applied').textContent = Number(data.applied).toLocaleString();
            document.getElementById('pi-created').textContent = Number(data.created).toLocaleString();
            document.getElementById('pi-failed').textContent = Number(data.failed).toLocaleString();

            if (total > 0) {
                const percent = Math.min(100, Math.round((Number(data.processed) / total) * 100));
                document.getElementById('pi-progress-bar').style.width = percent + '%';
            }

            if (data.finished) {
                clearInterval(timer);
                window.location.reload();
            }
        } catch (error) {
            // Keep polling; a transient network error should not stop the batch.
        }
    }, 3000);
})();
</script>
@endif
@endsection
