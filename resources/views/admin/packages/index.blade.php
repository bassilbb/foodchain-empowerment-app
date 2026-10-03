@extends('admin.layouts.app')

@section('title', 'Packages')
@section('page-title', 'Food Packages')

@section('content')
<div style="display: flex; justify-content: flex-end; margin-bottom: 24px;">
    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">Create Package</a>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 24px;">
    @forelse($packages as $package)
    <div class="page-card">
        <div class="page-card-body">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                <div>
                    <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">{{ $package->name }}</h3>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="badge badge-success">{{ $package->tier?->name ?? 'N/A' }}</span>
                        @if($package->is_default)
                            <span class="badge badge-success">Default</span>
                        @endif
                    </div>
                </div>
                <p style="font-size: 28px; font-weight: 700; color: #1e293b;">{{ format_currency($package->total_value) }}</p>
            </div>

            @if($package->description)
            <p style="color: #64748b; font-size: 14px; margin-bottom: 16px;">{{ $package->description }}</p>
            @endif

            <div style="border-top: 1px solid #e2e8f0; padding-top: 16px;">
                <h4 style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">Package Items:</h4>
                <div>
                    @foreach($package->items as $item)
                    <div style="display: flex; justify-content: space-between; font-size: 14px; color: #475569; padding: 8px 0; border-bottom: 1px dashed #f1f5f9;">
                        <span>{{ $item->foodItem?->name ?? 'N/A' }}</span>
                        <span style="color: #64748b;">{{ $item->quantity }} {{ $item->foodItem?->unit ?? '' }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 16px;">
                <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-primary" style="flex: 1;">Edit</a>
                <a href="{{ route('admin.packages.show', $package) }}" class="btn btn-secondary" style="flex: 1;">View</a>
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <p>No packages found. Create your first package!</p>
    </div>
    @endforelse
</div>

<div class="pagination">{{ $packages->links() }}</div>
@endsection
