@extends('admin.layouts.app')

@section('title', 'Marketplace Reviews')
@section('header', 'Marketplace Reviews')
@section('subheader', 'Moderate customer product reviews before they go live.')

@section('content')
<div class="page-header">
    <div>
        <h1>Reviews</h1>
        <p>{{ number_format($reviews->total()) }} review(s).</p>
    </div>
    <div class="header-actions-btns">
        <a href="{{ route('admin.marketplace.reviews.index', ['pending' => 1]) }}" class="btn {{ request('pending') ? 'btn-primary' : 'btn-secondary' }}">Pending Only</a>
        <a href="{{ route('admin.marketplace.reviews.index') }}" class="btn {{ request('pending') ? 'btn-secondary' : 'btn-primary' }}">All Reviews</a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 1rem;">{{ session('success') }}</div>
@endif

<div class="page-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Customer</th>
                <th>Rating</th>
                <th>Review</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $review)
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        @if($review->product && $review->product->image)
                        <img src="{{ $review->product->image }}" alt="" style="width: 36px; height: 36px; border-radius: 8px; object-fit: cover;">
                        @endif
                        <div>
                            <div style="font-weight: 600; color: #1e293b;">{{ $review->product?->name ?? 'Deleted product' }}</div>
                            @if($review->product)
                            <a href="{{ route('admin.marketplace.products.show', $review->product) }}" class="action-link" style="font-size: 12px;">View product</a>
                            @endif
                        </div>
                    </div>
                </td>
                <td>
                    <div style="font-weight: 600; color: #475569;">{{ $review->user?->name ?? 'Unknown' }}</div>
                    <div style="font-size: 12px; color: #94a3b8;">{{ $review->user?->email ?? '' }}</div>
                </td>
                <td style="white-space: nowrap; color: #f59e0b;">
                    @for($i = 1; $i <= 5; $i++)
                    <span style="{{ $i <= $review->rating ? '' : 'color: #e2e8f0;' }}">★</span>
                    @endfor
                </td>
                <td style="max-width: 320px;">
                    @if($review->title)<div style="font-weight: 600; color: #1e293b;">{{ $review->title }}</div>@endif
                    <div style="color: #475569; font-size: 13px;">{{ Str::limit($review->comment ?? '—', 100) }}</div>
                </td>
                <td style="white-space: nowrap; font-size: 13px; color: #64748b;">{{ $review->created_at->format('M d, Y') }}</td>
                <td>
                    <span class="badge {{ $review->is_approved ? 'badge-success' : 'badge-warning' }}">
                        {{ $review->is_approved ? 'Approved' : 'Pending' }}
                    </span>
                    @if($review->is_featured)<div style="font-size: 11px; color: #7c3aed;">Featured</div>@endif
                </td>
                <td style="white-space: nowrap;">
                    <form method="POST" action="{{ route('admin.marketplace.reviews.toggle', $review) }}">
                        @csrf
                        <button type="submit" class="btn {{ $review->is_approved ? 'btn-warning' : 'btn-primary' }} btn-sm">
                            {{ $review->is_approved ? 'Unapprove' : 'Approve' }}
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="empty-state">No reviews found</div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination">
    {{ $reviews->links() }}
</div>
@endsection
