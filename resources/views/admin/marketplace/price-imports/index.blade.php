@extends('admin.layouts.app')

@section('title', 'Bulk Product Price Update')
@section('header', 'Bulk Product Price Update')
@section('subheader', 'Upload a supplier CSV or XML price list, review every match, then apply the changes safely.')

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

<div class="page-header">
    <div>
        <h1>Bulk Product Price Update</h1>
        <p>Two-stage workflow: upload and preview first, then confirm before any price changes.</p>
    </div>
    <div class="header-actions-btns">
        <a href="{{ route('admin.marketplace.price-imports.template', 'csv') }}" class="btn btn-secondary">Sample CSV</a>
        <a href="{{ route('admin.marketplace.price-imports.template', 'xml') }}" class="btn btn-secondary">Sample XML</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon sky">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        </div>
        <p class="stat-label">Upload Batches</p>
        <p class="stat-value">{{ number_format($stats['batches']) }}</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
        </div>
        <p class="stat-label">Records Processed</p>
        <p class="stat-value">{{ number_format($stats['processed']) }}</p>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Prices Updated</p>
        <p class="stat-value">{{ number_format($stats['updated']) }}</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon sky">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </div>
        <p class="stat-label">New Products Created</p>
        <p class="stat-value">{{ number_format($stats['created']) }}</p>
    </div>
</div>

<div class="page-card" style="margin-bottom: 1rem;">
    <div class="page-card-body" style="display: flex; flex-wrap: wrap; gap: 1rem 2rem; align-items: center; justify-content: space-between; font-size: 0.85rem; color: #475569;">
        <div>
            <strong>{{ number_format($stats['failed']) }}</strong> invalid / failed records reported
        </div>
        <div>
            @if($stats['last'])
                Last successful upload:
                <strong>{{ $stats['last']->applied_at?->format('d M Y, H:i') }}</strong>
                &middot; {{ $stats['last']->vendor?->name }}
                &middot; {{ number_format($stats['last']->applied_count) }} price(s) updated
            @else
                No completed upload yet.
            @endif
        </div>
    </div>
</div>

<div class="page-card" style="margin-bottom: 1rem;">
    <div class="page-card-header">
        <h3>Upload a supplier price list</h3>
        <p>CSV or XML, maximum 10&nbsp;MB. Prices are not changed until you review the preview and confirm.</p>
    </div>
    <div class="page-card-body">
        <form method="POST" action="{{ route('admin.marketplace.price-imports.store') }}" enctype="multipart/form-data">
            @csrf

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="vendor_id">Supplier <span style="color:#dc2626;">*</span></label>
                    <select name="vendor_id" id="vendor_id" class="form-select" required>
                        <option value="">Select a supplier</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }}{{ $vendor->business_name && $vendor->business_name !== $vendor->name ? ' — '.$vendor->business_name : '' }}
                                ({{ ucfirst($vendor->status) }})
                            </option>
                        @endforeach
                    </select>
                    <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.35rem;">The upload can only match and update this supplier's products.</p>
                </div>

                <div class="form-group">
                    <label class="form-label" for="file">Price list file <span style="color:#dc2626;">*</span></label>
                    <input type="file" name="file" id="file" class="form-input" accept=".csv,.xml,text/csv,application/xml,text/xml" required>
                    <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.35rem;">Accepted: .csv, .xml (max 10 MB). Files are stored privately and never executed.</p>
                </div>

                <div class="form-group">
                    <label class="form-label" for="default_category_id">Default category for new products</label>
                    <select name="default_category_id" id="default_category_id" class="form-select">
                        <option value="">Use the category in the file</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('default_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.35rem;">New products without a recognised category are flagged for manual completion.</p>
                </div>
            </div>

            <div class="form-group" style="margin-top: 0.5rem;">
                <label style="display: flex; gap: 0.6rem; align-items: flex-start; font-size: 0.875rem; color: #334155;">
                    <input type="checkbox" name="create_new_products" value="1" {{ old('create_new_products') ? 'checked' : '' }} style="margin-top: 0.2rem;">
                    <span>
                        <strong>Allow creation of new products</strong><br>
                        <span style="font-size: 0.8rem; color: #64748b;">When checked, unmatched records with a valid name, price and category are created as approved products when you apply. Leave unchecked to review them first.</span>
                    </span>
                </label>
            </div>

            <details style="margin-top: 0.75rem; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.75rem 1rem;">
                <summary style="cursor: pointer; font-weight: 600; color: #334155; font-size: 0.875rem;">Field mapping (optional — auto-detected)</summary>
                <p style="font-size: 0.78rem; color: #64748b; margin: 0.5rem 0 0.75rem;">Only fill these in when the file uses unusual column names. Enter the exact header or XML element name.</p>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.75rem;">
                    @foreach(\App\Services\Marketplace\PriceImport\FieldMapping::FIELDS as $field => $label)
                        <div class="form-group">
                            <label class="form-label" for="mapping_{{ $field }}">{{ $label }}</label>
                            <input type="text" name="mapping[{{ $field }}]" id="mapping_{{ $field }}" class="form-input" value="{{ old('mapping.'.$field) }}" placeholder="Auto-detect">
                        </div>
                    @endforeach
                </div>
            </details>

            <div style="display: flex; gap: 0.75rem; margin-top: 1rem;">
                <button type="submit" class="btn btn-primary">Upload &amp; Preview</button>
                <a href="{{ route('admin.marketplace.products.index') }}" class="btn btn-secondary">Back to products</a>
            </div>
        </form>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h3>Recent uploads</h3>
        <p>Every batch keeps its preview, applied changes and rollback state.</p>
    </div>
    <div class="page-card-body" style="padding: 0; overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th>File</th>
                    <th>Records</th>
                    <th>Updated</th>
                    <th>New</th>
                    <th>Failed</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($batches as $batch)
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: #1e293b;">{{ $batch->created_at?->format('d M Y') }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">{{ $batch->created_at?->format('H:i') }}</div>
                        </td>
                        <td>{{ $batch->vendor?->name ?? '—' }}</td>
                        <td>
                            <div style="font-weight: 600; color: #1e293b; max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $batch->original_filename }}</div>
                            <div style="font-size: 12px; color: #94a3b8; text-transform: uppercase;">{{ $batch->format }}</div>
                        </td>
                        <td>{{ number_format($batch->total_records) }}</td>
                        <td>{{ number_format($batch->applied_count) }}</td>
                        <td>{{ number_format($batch->created_count) }}</td>
                        <td>{{ number_format($batch->invalid_count + $batch->failed_count) }}</td>
                        <td><span class="badge {{ $batch->statusBadge() }}">{{ $batch->statusLabel() }}</span></td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('admin.marketplace.price-imports.show', $batch) }}" class="btn btn-secondary" style="padding: 0.35rem 0.7rem; font-size: 0.78rem;">Review</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 2rem; color: #64748b;">No price list has been uploaded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($batches->hasPages())
        <div class="page-card-body">{{ $batches->links() }}</div>
    @endif
</div>
@endsection
