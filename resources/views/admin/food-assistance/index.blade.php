@extends('admin.layouts.app')
@section('title', 'Food Assistance Plans')
@section('header', 'Food Assistance Plans')
@section('subheader', 'Manage and track all food assistance plans')

@section('content')
@php
$badgeColors = ['pending' => 'warning', 'processing_fee_paid' => 'info', 'first_installment_paid' => 'purple', 'active' => 'success', 'completed' => 'success', 'cancelled' => 'error'];
@endphp

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="stats-grid">
    <div class="stat-card" style="border-left: 4px solid #f59e0b;"><div class="stat-value">{{ $stats['pending'] }}</div><div class="stat-label">Pending</div></div>
    <div class="stat-card" style="border-left: 4px solid #3b82f6;"><div class="stat-value">{{ $stats['processing_fee_paid'] }}</div><div class="stat-label">Fee Paid</div></div>
    <div class="stat-card" style="border-left: 4px solid #8b5cf6;"><div class="stat-value">{{ $stats['first_installment_paid'] }}</div><div class="stat-label">30% Paid</div></div>
    <div class="stat-card" style="border-left: 4px solid #10b981;"><div class="stat-value">{{ $stats['active'] }}</div><div class="stat-label">Active</div></div>
    <div class="stat-card" style="border-left: 4px solid #059669;"><div class="stat-value">{{ $stats['completed'] }}</div><div class="stat-label">Completed</div></div>
</div>

<div style="margin-bottom: 24px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
    <div style="background: linear-gradient(135deg, #10b981, #059669); border-radius: 16px; padding: 24px; color: white;">
        <div style="font-size: 0.75rem; opacity: 0.8; margin-bottom: 8px;">Total Disbursed (Food Value)</div>
        <div style="font-size: 1.5rem; font-weight: 700;">₦{{ number_format($stats['total_disbursed'], 2) }}</div>
    </div>
    <div style="background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 16px; padding: 24px; color: white;">
        <div style="font-size: 0.75rem; opacity: 0.8; margin-bottom: 8px;">Total Processing Fees</div>
        <div style="font-size: 1.5rem; font-weight: 700;">₦{{ number_format($stats['total_processing_fees'], 2) }}</div>
    </div>
</div>

<div class="data-table">
    <div class="table-header" style="background: linear-gradient(135deg, #10b981, #059669);">
        <h3 style="color: white; font-weight: 700; font-size: 1.1rem; margin: 0;">All Plans</h3>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('admin.food-assistance.index') }}" class="btn-sm" style="background: {{ !request('status') ? 'white' : 'rgba(255,255,255,0.1)' }}; color: {{ !request('status') ? '#10b981' : 'rgba(255,255,255,0.8)' }}; text-decoration: none;">All</a>
            <a href="{{ route('admin.food-assistance.index', ['status' => 'pending']) }}" class="btn-sm" style="background: {{ request('status') == 'pending' ? 'white' : 'rgba(255,255,255,0.1)' }}; color: {{ request('status') == 'pending' ? '#10b981' : 'rgba(255,255,255,0.8)' }}; text-decoration: none;">Pending</a>
            <a href="{{ route('admin.food-assistance.index', ['status' => 'active']) }}" class="btn-sm" style="background: {{ request('status') == 'active' ? 'white' : 'rgba(255,255,255,0.1)' }}; color: {{ request('status') == 'active' ? '#10b981' : 'rgba(255,255,255,0.8)' }}; text-decoration: none;">Active</a>
            <a href="{{ route('admin.food-assistance.index', ['status' => 'completed']) }}" class="btn-sm" style="background: {{ request('status') == 'completed' ? 'white' : 'rgba(255,255,255,0.1)' }}; color: {{ request('status') == 'completed' ? '#10b981' : 'rgba(255,255,255,0.8)' }}; text-decoration: none;">Completed</a>
        </div>
    </div>
    @if($plans->count() > 0)
    <table>
        <thead><tr><th class="table-header">Reference</th><th class="table-header">User</th><th class="table-header">Tier</th><th class="table-header">Total</th><th class="table-header">Paid</th><th class="table-header">Progress</th><th class="table-header">Status</th><th class="table-header">Actions</th></tr></thead>
        <tbody>
            @foreach($plans as $plan)
            <tr>
                <td style="font-family: monospace; font-weight: 600;">{{ $plan->reference }}</td>
                <td>
                    <div style="font-weight: 500;">{{ $plan->user?->full_name ?? 'N/A' }}</div>
                    <div style="font-size: 0.75rem; color: #94a3b8;">{{ $plan->user?->email ?? '' }}</div>
                </td>
                <td>{{ $plan->tier?->name ?? 'N/A' }}</td>
                <td style="font-weight: 600; color: #1e293b; font-family: monospace;">₦{{ number_format($plan->total_amount, 2) }}</td>
                <td style="font-weight: 600; color: #1e293b; font-family: monospace;">₦{{ number_format($plan->total_paid, 2) }}</td>
                <td style="min-width: 100px;">
                    <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 9999px; overflow: hidden;"><div style="width: {{ $plan->getProgress() }}%; height: 100%; background: linear-gradient(135deg, #10b981, #059669); border-radius: 9999px;"></div></div>
                    <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 4px;">{{ round($plan->getProgress()) }}%</div>
                </td>
                <td><span class="badge badge-{{ $badgeColors[$plan->status] ?? 'dark' }}">{{ str_replace('_', ' ', $plan->status) }}</span></td>
                <td><a href="{{ route('admin.food-assistance.show', $plan) }}" class="btn-primary btn-sm">View</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pagination">{{ $plans->withQueryString()->links() }}</div>
    @else
    <div class="empty-state"><p>No plans found.</p></div>
    @endif
</div>
@endsection