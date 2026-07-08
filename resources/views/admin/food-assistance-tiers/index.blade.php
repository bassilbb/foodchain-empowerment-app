@extends('admin.layouts.app')
@section('title', 'Food Assistance Tiers')
@section('header', 'Food Assistance Plan Tiers')
@section('subheader', 'Manage plan tiers and pricing')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 24px;">
    @forelse($tiers as $tier)
    <div class="page-card" style="text-align: center; padding: 24px; {{ $tier->is_active ? '' : 'opacity: 0.5;' }}">
        <div style="font-size: 1.1rem; font-weight: 600;">{{ $tier->name }}</div>
        <div style="font-size: 2rem; font-weight: 700; margin: 12px 0;">₦{{ number_format($tier->amount) }}</div>
        <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 12px;">{{ $tier->description ?? 'No description' }}</div>
        <span class="badge badge-{{ $tier->is_active ? 'success' : 'dark' }}">
            {{ $tier->is_active ? 'Active' : 'Inactive' }}
        </span>
    </div>
    @empty
    <div class="page-card" style="text-align: center; padding: 24px;">
        <p style="color: #94a3b8;">No tiers configured yet.</p>
    </div>
    @endforelse
</div>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title" style="margin: 0;">All Tiers</h3>
        <a href="{{ route('admin.food-assistance-tiers.create') }}" class="btn-primary btn-sm">+ Create Tier</a>
    </div>
    @if($tiers->count() > 0)
    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th class="table-header">Name</th>
                    <th class="table-header">Amount</th>
                    <th class="table-header">Plans</th>
                    <th class="table-header">Status</th>
                    <th class="table-header">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tiers as $tier)
                <tr>
                    <td style="font-weight: 600;">{{ $tier->name }}</td>
                    <td style="font-weight: 600;">₦{{ number_format($tier->amount) }}</td>
                    <td>{{ $tier->plans()->count() }}</td>
                    <td>
                        <span class="badge badge-{{ $tier->is_active ? 'success' : 'dark' }}">
                            {{ $tier->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.food-assistance-tiers.edit', $tier) }}" class="btn-primary btn-sm">Edit</a>
                        <form action="{{ route('admin.food-assistance-tiers.destroy', $tier) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this tier?')">
                            @csrf @method('DELETE')
                            <button class="btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <p>No tiers created yet. Click "Create Tier" to add one.</p>
    </div>
    @endif
</div>
@endsection
