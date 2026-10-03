@extends('admin.layouts.app')

@section('title', 'Food Items')
@section('page-title', 'Food Items Management')

@section('content')
<div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
    <div></div>
    <a href="{{ route('admin.food-items.create') }}" class="btn btn-primary">
        Add Food Item
    </a>
</div>

<div class="page-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Unit</th>
                <th>Unit Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($foodItems as $item)
            <tr>
                <td>
                    <div style="font-weight: 600; color: #1e293b;">{{ $item->name }}</div>
                    @if($item->description)
                    <div style="font-size: 13px; color: #94a3b8;">{{ Str::limit($item->description, 50) }}</div>
                    @endif
                </td>
                <td style="white-space: nowrap;">{{ $item->unit }}</td>
                <td>
                    <span style="font-weight: 600; color: #10b981;">{{ format_currency($item->unit_price) }}</span>
                </td>
                <td>
                    <span class="badge {{ $item->is_active ? 'badge-success' : 'badge-error' }}">
                        {{ $item->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td style="white-space: nowrap;">
                    <a href="{{ route('admin.food-items.edit', $item) }}" class="action-link">Edit</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">
                    <div class="empty-state">No food items found</div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination">
    {{ $foodItems->links() }}
</div>
@endsection
