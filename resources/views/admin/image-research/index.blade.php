@extends('admin.layouts.app')

@section('title', 'Image Research')
@section('header', 'Packaging Image Research')
@section('subheader', 'Find, verify and attach genuine product packaging images — nothing is published without administrator approval')

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
    $badge = function ($status) {
        return match ($status) {
            'verified' => 'badge-info',
            'downloaded' => 'badge-success',
            'pending_verification', 'candidate_found' => 'badge-warning',
            'rejected' => 'badge-error',
            default => 'badge-dark',
        };
    };
@endphp

<!-- Progress -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon sky">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
        </div>
        <p class="stat-label">Outstanding Products</p>
        <p class="stat-value">{{ number_format($report['outstanding']) }}</p>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Pending Verification</p>
        <p class="stat-value">{{ number_format($report['pending_verification']) }}</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Verified / Downloaded</p>
        <p class="stat-value">{{ number_format($report['verified'] + $report['downloaded']) }}</p>
    </div>
    <div class="stat-card danger">
        <div class="stat-icon red">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path></svg>
        </div>
        <p class="stat-label">Not Found / Failed</p>
        <p class="stat-value">{{ number_format($report['not_found'] + $report['failed_downloads']) }}</p>
    </div>
</div>

<div class="page-card" style="margin-bottom: 1rem;">
    <div class="page-card-body" style="display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: center; justify-content: space-between;">
        <div style="font-size: 0.85rem; color: #475569;">
            <strong>{{ number_format($report['total_products']) }}</strong> products in vendor {{ $report['vendor_id'] }} ·
            <strong>{{ number_format($report['with_real_images']) }}</strong> already have real images ·
            Provider: <strong>{{ $report['provider'] }}</strong>
            @if($report['provider_configured'])
                <span class="badge badge-success">Configured</span>
            @else
                <span class="badge badge-warning">Manual mode</span>
            @endif
        </div>
        <form method="POST" action="{{ route('admin.image-research.audit') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Refresh Research Queue</button>
        </form>
    </div>
</div>

<!-- Filters -->
<div class="filter-card">
    <form method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
        <div class="form-group" style="flex: 1; min-width: 220px;">
            <label class="form-label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="SKU, product name or source site" class="form-input">
        </div>
        <div class="form-group" style="min-width: 190px;">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                @foreach(\App\Models\ProductImageResearch::STATUSES as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="min-width: 220px;">
            <label class="form-label">Coverage</label>
            <label style="display: flex; gap: 0.5rem; align-items: center; font-size: 0.85rem; color: #475569;">
                <input type="checkbox" name="without_image" value="1" {{ request()->boolean('without_image') ? 'checked' : '' }}>
                Only products without a verified image
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request()->hasAny(['search', 'status', 'without_image']))
            <a href="{{ route('admin.image-research.index') }}" class="btn" style="background: #f1f5f9; color: #475569;">Reset</a>
        @endif
    </form>
</div>

<!-- Queue -->
<div class="page-card">
    <div class="page-card-body" style="padding: 0; overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Product</th>
                    <th>Status</th>
                    <th>Candidate Source</th>
                    <th>Download</th>
                    <th>Verified By</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                <tr>
                    <td><strong>{{ $record->sku }}</strong></td>
                    <td>
                        <div>{{ $record->product_name }}</div>
                        @if($record->product)
                            <div style="font-size: 0.75rem; color: #94a3b8;">Product #{{ $record->product_id }}</div>
                        @else
                            <div style="font-size: 0.75rem; color: #dc2626;">Product missing</div>
                        @endif
                    </td>
                    <td><span class="badge {{ $badge($record->status) }}">{{ $record->status_label }}</span></td>
                    <td>
                        @if($record->candidate_source_site)
                            <div>{{ $record->candidate_source_site }}</div>
                        @endif
                        @if($record->candidate_source_url)
                            <a href="{{ $record->candidate_source_url }}" target="_blank" rel="noopener" style="font-size: 0.75rem; color: #2563eb;">View source</a>
                        @else
                            <span style="font-size: 0.75rem; color: #94a3b8;">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $record->download_status === 'downloaded' ? 'badge-success' : ($record->download_status === 'failed' ? 'badge-error' : 'badge-dark') }}">
                            {{ ucwords(str_replace('_', ' ', $record->download_status)) }}
                        </span>
                    </td>
                    <td>
                        @if($record->verifier)
                            <div style="font-size: 0.8rem;">{{ $record->verifier->name }}</div>
                            <div style="font-size: 0.72rem; color: #94a3b8;">{{ optional($record->verified_at)->format('d M Y H:i') }}</div>
                        @else
                            <span style="font-size: 0.75rem; color: #94a3b8;">—</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('admin.image-research.show', $record) }}" class="btn btn-sm btn-primary">Review</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: #64748b;">
                        No research records match these filters. Run “Refresh Research Queue” to populate products without images.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination">{{ $records->links() }}</div>
@endsection
