@extends('admin.layouts.app')

@section('title', 'Marketplace Flash Sales')
@section('header', 'Marketplace Flash Sales')
@section('subheader', 'Schedule time-boxed discount events with special pricing.')

@section('content')
<div class="page-header">
    <div>
        <h1>Flash Sales</h1>
        <p>{{ number_format($flashSales->count()) }} flash sale event(s).</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 1rem;">{{ session('success') }}</div>
@endif

<!-- Create form -->
<div class="page-card" style="margin-bottom: 2rem;">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Create Flash Sale</h3>
            <p class="page-card-subtitle">Set the event window; add discounted products afterwards</p>
        </div>
    </div>
    <div class="page-card-body">
        <form method="POST" action="{{ route('admin.marketplace.flash-sales.store') }}" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
            @csrf
            <div style="flex: 1; min-width: 180px;">
                <label class="form-label">Name *</label>
                <input type="text" name="name" required class="form-input" placeholder="e.g., Weekend Mega Sale">
            </div>
            <div style="min-width: 160px;">
                <label class="form-label">Starts At *</label>
                <input type="datetime-local" name="starts_at" required class="form-input" value="{{ now()->format('Y-m-d\TH:i') }}">
            </div>
            <div style="min-width: 160px;">
                <label class="form-label">Ends At *</label>
                <input type="datetime-local" name="ends_at" required class="form-input" value="{{ now()->addDays(2)->format('Y-m-d\TH:i') }}">
            </div>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding-bottom: 10px;">
                <input type="checkbox" name="is_active" value="1" checked>
                <span style="font-weight: 500; color: #475569;">Active</span>
            </label>
            <button type="submit" class="btn btn-primary">Create Flash Sale</button>
        </form>
    </div>
</div>

<!-- List -->
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Flash Sale Events</h3>
            <p class="page-card-subtitle">Review and manage scheduled sales</p>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Window</th>
                <th>Products</th>
                <th>State</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($flashSales as $flashSale)
            <tr>
                <td style="font-weight: 600; color: #1e293b;">{{ $flashSale->name }}</td>
                <td style="font-size: 13px; white-space: nowrap;">
                    {{ $flashSale->starts_at->format('M d, Y H:i') }}<br>
                    <span style="color: #94a3b8;">→ {{ $flashSale->ends_at->format('M d, Y H:i') }}</span>
                </td>
                <td><span class="badge badge-primary">{{ number_format($flashSale->items_count) }} items</span></td>
                <td>
                    @if($flashSale->isActive())
                    <span class="badge badge-success">Live now</span>
                    @elseif($flashSale->ends_at && $flashSale->ends_at->isPast())
                    <span class="badge badge-dark">Ended</span>
                    @elseif($flashSale->starts_at && $flashSale->starts_at->isFuture())
                    <span class="badge badge-warning">Scheduled</span>
                    @else
                    <span class="badge badge-dark">Inactive</span>
                    @endif
                </td>
                <td><span class="badge {{ $flashSale->is_active ? 'badge-success' : 'badge-error' }}">{{ $flashSale->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td style="white-space: nowrap;">
                    <a href="{{ route('admin.marketplace.flash-sales.show', $flashSale) }}" class="action-link">Manage</a>
                    <form method="POST" action="{{ route('admin.marketplace.flash-sales.toggle', $flashSale) }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="action-link" style="border: none; background: none; cursor: pointer; padding: 0; color: #2563eb;">{{ $flashSale->is_active ? 'Deactivate' : 'Activate' }}</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state">No flash sales yet. Create one above.</div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
