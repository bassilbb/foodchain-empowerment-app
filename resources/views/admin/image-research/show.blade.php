@extends('admin.layouts.app')

@section('title', 'Review Image Candidate')
@section('header', 'Review Image Candidate')
@section('subheader', $research->sku.' · '.$research->product_name)

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
    $statusBadge = match ($research->status) {
        'verified' => 'badge-info',
        'downloaded' => 'badge-success',
        'pending_verification', 'candidate_found' => 'badge-warning',
        'rejected' => 'badge-error',
        default => 'badge-dark',
    };
@endphp

<div style="margin-bottom: 1rem;">
    <a href="{{ route('admin.image-research.index') }}" class="btn btn-secondary btn-sm">&larr; Back to queue</a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <p class="stat-label">Status</p>
        <p style="margin-top: 0.5rem;"><span class="badge {{ $statusBadge }}">{{ $research->status_label }}</span></p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Download</p>
        <p style="margin-top: 0.5rem;"><span class="badge {{ $research->download_status === 'downloaded' ? 'badge-success' : ($research->download_status === 'failed' ? 'badge-error' : 'badge-dark') }}">{{ ucwords(str_replace('_', ' ', $research->download_status)) }}</span></p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Research Attempts</p>
        <p class="stat-value">{{ $research->research_attempts }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Last Researched</p>
        <p style="margin-top: 0.5rem; font-size: 0.9rem; color: #475569;">{{ optional($research->last_researched_at)->format('d M Y H:i') ?? 'Never' }}</p>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">Product</h3>
    </div>
    <div class="page-card-body">
        <table class="data-table">
            <tbody>
                <tr><th style="width: 220px;">SKU</th><td><strong>{{ $research->sku }}</strong></td></tr>
                <tr><th>Product name</th><td>{{ $research->product_name }}</td></tr>
                <tr><th>Product ID</th><td>{{ $research->product_id }}</td></tr>
                <tr><th>Vendor</th><td>{{ $research->vendor_id }}</td></tr>
                <tr><th>Existing images</th><td>
                    @if($research->product && $research->product->images->isNotEmpty())
                        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                            @foreach($research->product->images as $image)
                                <div style="text-align: center;">
                                    <img src="{{ $image->url }}" alt="" style="height: 80px; width: 80px; object-fit: contain; border: 1px solid #e2e8f0; border-radius: 6px; background: #fff;">
                                    <div style="font-size: 0.7rem; color: #94a3b8;">{{ $image->is_placeholder ? 'Placeholder' : ($image->is_primary ? 'Primary' : 'Image') }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <span style="color: #94a3b8;">No images attached.</span>
                    @endif
                </td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">Candidate Image</h3>
    </div>
    <div class="page-card-body">
        @if($research->candidate_image_url)
            <div style="display: grid; grid-template-columns: minmax(220px, 320px) 1fr; gap: 1.5rem; align-items: start;">
                <div>
                    <img src="{{ $research->candidate_image_url }}" alt="Candidate packaging" style="max-width: 100%; max-height: 320px; object-fit: contain; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; padding: 0.5rem;">
                </div>
                <div style="font-size: 0.9rem; color: #475569; line-height: 1.7;">
                    <p><strong>Candidate URL:</strong><br><a href="{{ $research->candidate_image_url }}" target="_blank" rel="noopener" style="color: #2563eb; word-break: break-all;">{{ $research->candidate_image_url }}</a></p>
                    @if($research->candidate_source_url)
                        <p><strong>Source page:</strong><br><a href="{{ $research->candidate_source_url }}" target="_blank" rel="noopener" style="color: #2563eb; word-break: break-all;">{{ $research->candidate_source_url }}</a></p>
                    @endif
                    <p><strong>Source site:</strong> {{ $research->candidate_source_site ?? 'Unknown' }}</p>
                    @if($research->candidate_title)
                        <p><strong>Result title:</strong> {{ $research->candidate_title }}</p>
                    @endif
                    @if($research->candidate_query)
                        <p><strong>Search query:</strong> {{ $research->candidate_query }}</p>
                    @endif
                </div>
            </div>
        @else
            <p style="color: #64748b;">No candidate image is stored yet. Run research (if an image-search provider is configured) or upload a verified image below.</p>
        @endif

        @if($research->download_error)
            <div class="alert alert-error" style="margin-top: 1rem;">{{ $research->download_error }}</div>
        @endif

        @if($research->downloaded_path)
            <p style="margin-top: 1rem; font-size: 0.85rem; color: #16a34a;">
                Downloaded to <strong>{{ $research->downloaded_path }}</strong>
                @if($research->downloaded_at) on {{ $research->downloaded_at->format('d M Y H:i') }} @endif
            </p>
        @endif
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem;">

    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Verify or Reject</h3>
        </div>
        <div class="page-card-body">
            <p style="font-size: 0.82rem; color: #64748b; margin-bottom: 1rem;">
                Confirm the brand, variant and pack size match the real product before verifying. Verifying does not publish the image by itself — download it afterwards.
            </p>

            <form method="POST" action="{{ route('admin.image-research.verify', $research) }}" style="margin-bottom: 1.25rem;">
                @csrf
                <div class="form-group">
                    <label class="form-label">Verification notes (optional)</label>
                    <input type="text" name="notes" class="form-input" maxlength="2000" placeholder="e.g. Brand, variant and 500ml x12 pack confirmed">
                </div>
                <button type="submit" class="btn btn-primary" @disabled(! $research->candidate_image_url)>Approve Candidate</button>
            </form>

            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 1rem 0;">

            <form method="POST" action="{{ route('admin.image-research.reject', $research) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Rejection reason (required)</label>
                    <input type="text" name="reason" class="form-input" maxlength="2000" placeholder="e.g. Wrong pack size" required>
                </div>
                <button type="submit" class="btn btn-danger">Reject Candidate</button>
            </form>
        </div>
    </div>

    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Download &amp; Attach</h3>
        </div>
        <div class="page-card-body">
            <p style="font-size: 0.82rem; color: #64748b; margin-bottom: 1rem;">
                Downloads the approved candidate, validates MIME type and dimensions, converts it to WebP and attaches it to this product only. A failed download leaves existing images untouched.
            </p>

            <form method="POST" action="{{ route('admin.image-research.download', $research) }}" style="margin-bottom: 1.25rem;">
                @csrf
                <label style="display: flex; gap: 0.5rem; align-items: center; font-size: 0.82rem; color: #475569; margin-bottom: 0.75rem;">
                    <input type="checkbox" name="replace" value="1">
                    Replace an existing image on this product
                </label>
                <button type="submit" class="btn btn-primary" @disabled($research->status !== 'verified')>Download &amp; Attach Image</button>
            </form>

            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 1rem 0;">

            <form method="POST" action="{{ route('admin.image-research.upload', $research) }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">Upload verified replacement</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" class="form-input" required>
                </div>
                <button type="submit" class="btn btn-secondary">Upload &amp; Attach</button>
            </form>
        </div>
    </div>

    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Research</h3>
        </div>
        <div class="page-card-body">
            <p style="font-size: 0.82rem; color: #64748b; margin-bottom: 1rem;">
                Re-run the approved image-search API for this product. Candidates are never auto-approved.
            </p>

            <form method="POST" action="{{ route('admin.image-research.research', $research) }}">
                @csrf
                <button type="submit" class="btn btn-secondary">Retry Research</button>
            </form>

            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 1rem 0;">

            <div style="font-size: 0.82rem; color: #475569; line-height: 1.8;">
                <p><strong>Verification:</strong> {{ ucfirst($research->verification_status) }}</p>
                <p><strong>Verified by:</strong> {{ $research->verifier->name ?? '—' }}</p>
                <p><strong>Verified at:</strong> {{ optional($research->verified_at)->format('d M Y H:i') ?? '—' }}</p>
                @if($research->reviewer_notes)
                    <p><strong>Notes:</strong> {{ $research->reviewer_notes }}</p>
                @endif
                @if($research->rejection_reason)
                    <p><strong>Rejection reason:</strong> {{ $research->rejection_reason }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
