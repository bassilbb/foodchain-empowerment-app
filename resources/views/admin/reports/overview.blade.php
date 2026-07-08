{{-- resources/views/admin/reports/overview.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports Overview')

@section('content')
<style>
    .report-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); padding: 32px; transition: all 0.3s ease; display: block; text-decoration: none; }
    .report-card:hover { transform: translateY(-4px); box-shadow: 0 20px 50px -12px rgba(16, 185, 129, 0.2); }
    .report-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; }
    .icon-users { background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); }
    .icon-payments { background: linear-gradient(135deg, #3b82f6, #2563eb); box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3); }
    .icon-inventory { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3); }
    .icon-distributions { background: linear-gradient(135deg, #8b5cf6, #7c3aed); box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3); }
    .report-title { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
    .report-desc { font-size: 14px; color: #64748b; margin: 0; }
    .quick-stats { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); padding: 32px; }
    .stats-title { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 16px; }
    .stats-desc { color: #64748b; font-size: 14px; }
    .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-top: 24px; }
    .stat-item { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border-radius: 12px; padding: 20px; text-align: center; }
    .stat-value { font-size: 24px; font-weight: 700; color: #10b981; }
    .stat-label { font-size: 12px; color: #64748b; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
</style>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 32px;">
    <a href="{{ route('admin.reports.users') }}" class="report-card">
        <div class="report-icon icon-users">
            <svg style="width: 28px; height: 28px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <h3 class="report-title">User Reports</h3>
        <p class="report-desc">View user statistics and analytics</p>
    </a>

    <a href="{{ route('admin.reports.payments') }}" class="report-card">
        <div class="report-icon icon-payments">
            <svg style="width: 28px; height: 28px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <h3 class="report-title">Payment Reports</h3>
        <p class="report-desc">Revenue and transaction reports</p>
    </a>

    <a href="{{ route('admin.reports.inventory') }}" class="report-card">
        <div class="report-icon icon-inventory">
            <svg style="width: 28px; height: 28px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
        </div>
        <h3 class="report-title">Inventory Reports</h3>
        <p class="report-desc">Stock levels and valuation</p>
    </a>

    <a href="{{ route('admin.reports.distributions') }}" class="report-card">
        <div class="report-icon icon-distributions">
            <svg style="width: 28px; height: 28px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
        </div>
        <h3 class="report-title">Distribution Reports</h3>
        <p class="report-desc">Food distribution analytics</p>
    </a>
</div>

<div class="quick-stats">
    <h3 class="stats-title">Quick Stats</h3>
    <p class="stats-desc">Select a report type above to view detailed analytics.</p>
    <div class="stat-grid">
        <div class="stat-item">
            <div class="stat-value">--</div>
            <div class="stat-label">Total Users</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">--</div>
            <div class="stat-label">Total Revenue</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">--</div>
            <div class="stat-label">Distributions</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">--</div>
            <div class="stat-label">Inventory Value</div>
        </div>
    </div>
</div>
@endsection