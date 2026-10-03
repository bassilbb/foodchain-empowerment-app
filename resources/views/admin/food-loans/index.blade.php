@extends('admin.layouts.app')

@section('title', 'Food Loans')
@section('page-title', 'Loan Management')
@section('header', 'Food Loans')
@section('subheader', 'Manage and track all food loans')

@section('content')
@php
$badgeColors = ['pending' => 'warning', 'approved' => 'primary', 'disbursed' => 'purple', 'repaying' => 'success', 'completed' => 'success', 'defaulted' => 'error'];
@endphp

@if(session('success'))
<div class="alert alert-success">
    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-error">
    {{ session('error') }}
</div>
@endif

<div class="stats-grid">
    <div class="stat-card" style="border-left: 4px solid #f59e0b;">
        <div class="stat-value">{{ $stats['pending'] }}</div>
        <div class="stat-label">Pending</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #3b82f6;">
        <div class="stat-value">{{ $stats['approved'] }}</div>
        <div class="stat-label">Approved</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #8b5cf6;">
        <div class="stat-value">{{ $stats['disbursed'] }}</div>
        <div class="stat-label">Disbursed</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #10b981;">
        <div class="stat-value">{{ $stats['repaying'] }}</div>
        <div class="stat-label">Repaying</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #059669;">
        <div class="stat-value">{{ $stats['completed'] }}</div>
        <div class="stat-label">Completed</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #ef4444;">
        <div class="stat-value">{{ $stats['defaulted'] }}</div>
        <div class="stat-label">Defaulted</div>
    </div>
</div>

<div style="margin-bottom: 24px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
    <div style="background: linear-gradient(135deg, #10b981, #059669); border-radius: 16px; padding: 24px; color: white;">
        <div style="font-size: 0.75rem; opacity: 0.8; margin-bottom: 8px;">Total Disbursed</div>
        <div style="font-size: 1.5rem; font-weight: 700;">₦{{ number_format($stats['total_disbursed'], 2) }}</div>
    </div>
    <div style="background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 16px; padding: 24px; color: white;">
        <div style="font-size: 0.75rem; opacity: 0.8; margin-bottom: 8px;">Total Repaid</div>
        <div style="font-size: 1.5rem; font-weight: 700;">₦{{ number_format($stats['total_repaid'], 2) }}</div>
    </div>
    <div style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 16px; padding: 24px; color: white;">
        <div style="font-size: 0.75rem; opacity: 0.8; margin-bottom: 8px;">Outstanding</div>
        <div style="font-size: 1.5rem; font-weight: 700;">₦{{ number_format($stats['total_disbursed'] - $stats['total_repaid'], 2) }}</div>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header" style="background: linear-gradient(135deg, #10b981, #059669); color: white;">
        <h3 class="page-card-title" style="color: white;">All Food Loans</h3>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('admin.food-loans.index') }}" class="btn btn-sm" style="padding: 6px 14px; font-size: 0.8rem; font-weight: 500; color: rgba(255,255,255,0.8); background: rgba(255,255,255,0.1); border-radius: 8px; text-decoration: none; {{ !request('status') ? 'background: white; color: #10b981;' : '' }}">All</a>
            <a href="{{ route('admin.food-loans.index', ['status' => 'pending']) }}" class="btn btn-sm" style="padding: 6px 14px; font-size: 0.8rem; font-weight: 500; color: rgba(255,255,255,0.8); background: rgba(255,255,255,0.1); border-radius: 8px; text-decoration: none; {{ request('status') == 'pending' ? 'background: white; color: #10b981;' : '' }}">Pending</a>
            <a href="{{ route('admin.food-loans.index', ['status' => 'repaying']) }}" class="btn btn-sm" style="padding: 6px 14px; font-size: 0.8rem; font-weight: 500; color: rgba(255,255,255,0.8); background: rgba(255,255,255,0.1); border-radius: 8px; text-decoration: none; {{ request('status') == 'repaying' ? 'background: white; color: #10b981;' : '' }}">Repaying</a>
            <a href="{{ route('admin.food-loans.index', ['status' => 'completed']) }}" class="btn btn-sm" style="padding: 6px 14px; font-size: 0.8rem; font-weight: 500; color: rgba(255,255,255,0.8); background: rgba(255,255,255,0.1); border-radius: 8px; text-decoration: none; {{ request('status') == 'completed' ? 'background: white; color: #10b981;' : '' }}">Completed</a>
        </div>
    </div>

    @if($loans->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>Reference</th>
                <th>Union</th>
                <th>Amount</th>
                <th>Repaid</th>
                <th>Progress</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loans as $loan)
            <tr>
                <td style="font-family: monospace; font-weight: 600;">{{ $loan->reference }}</td>
                <td>
                    <div style="font-weight: 500; color: #1e293b;">{{ $loan->union?->name ?? 'N/A' }}</div>
                    <div style="font-size: 0.75rem; color: #94a3b8;">{{ $loan->union?->code ?? '' }}</div>
                </td>
                <td style="font-weight: 600; color: #1e293b; font-family: monospace;">₦{{ number_format($loan->loan_amount_approved, 2) }}</td>
                <td>
                    <div style="font-weight: 600; color: #1e293b; font-family: monospace;">₦{{ number_format($loan->amount_repaid, 2) }}</div>
                    <div style="font-size: 0.7rem; color: #94a3b8;">{{ $loan->installments_paid }}/{{ $loan->total_installments }} installments</div>
                </td>
                <td style="min-width: 120px;">
                    <div class="progress-bar" style="height: 6px;">
                        <div class="progress-fill emerald" style="width: {{ $loan->getRepaymentProgress() }}%;"></div>
                    </div>
                    <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 4px;">{{ round($loan->getRepaymentProgress()) }}%</div>
                </td>
                <td>
                    <span class="badge badge-{{ $badgeColors[$loan->status] ?? 'dark' }}">{{ $loan->status }}</span>
                </td>
                <td>
                    <a href="{{ route('admin.food-loans.show', $loan) }}" class="btn btn-sm" style="padding: 6px 12px; font-size: 0.75rem; font-weight: 500; background: #10b981; color: white; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        View
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pagination">
        {{ $loans->withQueryString()->links() }}
    </div>
    @else
    <div class="empty-state">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        <p>No loans found</p>
        <p>Food loans will appear here when unions apply for them.</p>
    </div>
    @endif
</div>
@endsection
