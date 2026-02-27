@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-bold text-slate-800">Welcome back, {{ auth()->guard('admin')->user()->name ?? 'Admin' }}! 👋</h1>
        <p class="text-slate-500 mt-1">Here's what's happening with your FoodChain platform today.</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 font-medium rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            View Users
        </a>
        <a href="{{ route('admin.distributions.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-medium rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Distribution
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Users -->
    <div class="stat-card group">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-3">
                    <div class="p-2 rounded-xl bg-primary-100 group-hover:bg-primary-200 transition-colors">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-slate-500">Total Users</span>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ number_format($totalUsers) }}</p>
                <div class="flex items-center gap-1 mt-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-700">
                        {{ number_format($activeUsers) }} active
                    </span>
                    <span class="text-xs text-slate-400 ml-2">+{{ $newUsersThisMonth }} this month</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="stat-card accent group">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-3">
                    <div class="p-2 rounded-xl bg-accent-100 group-hover:bg-accent-200 transition-colors">
                        <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-slate-500">Total Revenue</span>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ format_currency($totalRevenue) }}</p>
                <div class="flex items-center gap-1 mt-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-accent-100 text-accent-700">
                        {{ format_currency($revenueThisMonth) }} this month
                    </span>
                    <span class="text-xs text-slate-400 ml-2">{{ format_currency($revenueToday) }} today</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Distributions -->
    <div class="stat-card warning group">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-3">
                    <div class="p-2 rounded-xl bg-purple-100 group-hover:bg-purple-200 transition-colors">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-slate-500">Distributions</span>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ number_format($distributionsThisMonth) }}</p>
                <div class="flex items-center gap-1 mt-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                        {{ $pendingDistributions }} pending
                    </span>
                    <a href="{{ route('admin.distributions.index', ['status' => 'pending']) }}" class="text-xs text-primary-600 hover:underline ml-2">View →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Support Tickets -->
    <div class="stat-card info group">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-3">
                    <div class="p-2 rounded-xl bg-pink-100 group-hover:bg-pink-200 transition-colors">
                        <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-slate-500">Open Tickets</span>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ number_format($openTickets) }}</p>
                <div class="flex items-center gap-1 mt-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                        Support requests
                    </span>
                    <a href="{{ route('admin.support.index') }}" class="text-xs text-primary-600 hover:underline ml-2">View →</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Alert -->
@if(count($lowStockItems) > 0)
<div class="card-3d p-4 mb-8 border-l-4 border-yellow-400 bg-gradient-to-r from-yellow-50 to-white">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <div class="p-2 rounded-full bg-yellow-100">
                <svg class="h-5 w-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>
        <div class="ml-4">
            <p class="text-sm text-slate-700">
                <strong class="text-yellow-700">{{ count($lowStockItems) }} items</strong> are running low on stock.
                <a href="{{ route('admin.inventory.low-stock') }}" class="font-medium text-primary-600 hover:text-primary-700 underline">View details →</a>
            </p>
        </div>
    </div>
</div>
@endif

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Users by Tier -->
    <div class="card-3d p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Users by Tier</h3>
                <p class="text-sm text-slate-500">Membership distribution</p>
            </div>
            <div class="p-2 rounded-xl bg-primary-50">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
        <div class="space-y-4">
            @php
                $tierColors = [
                    'bronze' => 'from-amber-500 to-amber-600',
                    'silver' => 'from-slate-400 to-slate-500',
                    'gold' => 'from-yellow-400 to-yellow-500',
                    'emerald' => 'from-emerald-500 to-emerald-600',
                ];
                $tierBgColors = [
                    'bronze' => 'bg-amber-500',
                    'silver' => 'bg-slate-400',
                    'gold' => 'bg-yellow-400',
                    'emerald' => 'bg-emerald-500',
                ];
            @endphp
            @foreach($usersByTier as $tier)
            <div class="group">
                <div class="flex justify-between items-center mb-2">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-slate-700">{{ $tier['tier'] }}</span>
                    </div>
                    <span class="text-sm font-bold text-slate-800">{{ number_format($tier['count']) }}</span>
                </div>
                <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-1000 ease-out group-hover:shadow-lg {{ $tierBgColors[strtolower($tier['tier'])] ?? 'bg-primary-500' }}" style="width: {{ $totalUsers > 0 ? ($tier['count'] / $totalUsers) * 100 : 0 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Monthly Revenue Chart -->
    <div class="card-3d p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Revenue Trend</h3>
                <p class="text-sm text-slate-500">Last 6 months</p>
            </div>
            <div class="p-2 rounded-xl bg-accent-50">
                <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
        </div>
        <canvas id="revenueChart" class="w-full h-64"></canvas>
    </div>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Users -->
    <div class="card-3d overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Recent Registrations</h3>
                <p class="text-sm text-slate-500">New users this week</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">View all →</a>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach($recentUsers as $user)
            <div class="px-6 py-4 hover:bg-slate-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-400 to-accent-400 flex items-center justify-center text-white font-bold shadow-md">
                            {{ substr($user->full_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800">{{ $user->full_name }}</p>
                            <p class="text-sm text-slate-500">{{ $user->email }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium mt-1 {{ tier_badge_color($user->currentTier->slug) }} text-white">
                                {{ $user->currentTier->name }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-slate-500">{{ $user->created_at->format('M d, Y') }}</p>
                        <a href="{{ route('admin.users.show', ['user' => $user->id]) }}" class="text-xs text-primary-600 hover:underline">View →</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="card-3d overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Recent Payments</h3>
                <p class="text-sm text-slate-500">Latest transactions</p>
            </div>
            <a href="{{ route('admin.payments.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">View all →</a>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach($recentPayments as $payment)
            <div class="px-6 py-4 hover:bg-slate-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-md
                            {{ $payment->status === 'successful' ? 'bg-green-100' : '' }}
                            {{ $payment->status === 'pending' ? 'bg-yellow-100' : '' }}
                            {{ $payment->status === 'failed' ? 'bg-red-100' : '' }}">
                            <svg class="w-6 h-6 
                                {{ $payment->status === 'successful' ? 'text-green-600' : '' }}
                                {{ $payment->status === 'pending' ? 'text-yellow-600' : '' }}
                                {{ $payment->status === 'failed' ? 'text-red-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800">{{ $payment->user->full_name }}</p>
                            <p class="text-sm text-slate-500">{{ ucfirst(str_replace('_', ' ', $payment->type)) }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium mt-1
                                {{ $payment->status === 'successful' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $payment->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-slate-800">{{ format_currency($payment->amount) }}</p>
                        <p class="text-xs text-slate-400">{{ $payment->created_at->format('M d, h:i A') }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-8">
    <h3 class="text-lg font-bold text-slate-800 mb-4">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <a href="{{ route('admin.users.index') }}" class="card-3d p-4 text-center hover:border-primary-300 border-2 border-transparent">
            <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-blue-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            </div>
            <p class="text-sm font-medium text-slate-700">Add User</p>
        </a>
        <a href="{{ route('admin.payments.index') }}" class="card-3d p-4 text-center hover:border-primary-300 border-2 border-transparent">
            <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-green-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="text-sm font-medium text-slate-700">View Payments</p>
        </a>
        <a href="{{ route('admin.manual-payments.index') }}" class="card-3d p-4 text-center hover:border-primary-300 border-2 border-transparent">
            <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-yellow-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <p class="text-sm font-medium text-slate-700">Verify Payments</p>
        </a>
        <a href="{{ route('admin.distributions.index') }}" class="card-3d p-4 text-center hover:border-primary-300 border-2 border-transparent">
            <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-purple-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            </div>
            <p class="text-sm font-medium text-slate-700">Distributions</p>
        </a>
        <a href="{{ route('admin.inventory.index') }}" class="card-3d p-4 text-center hover:border-primary-300 border-2 border-transparent">
            <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-orange-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <p class="text-sm font-medium text-slate-700">Inventory</p>
        </a>
        <a href="{{ route('admin.support.index') }}" class="card-3d p-4 text-center hover:border-primary-300 border-2 border-transparent">
            <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-pink-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <p class="text-sm font-medium text-slate-700">Support</p>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');

const gradient = ctx.createLinearGradient(0, 0, 0, 256);
gradient.addColorStop(0, 'rgba(14, 165, 233, 0.3)');
gradient.addColorStop(1, 'rgba(14, 165, 233, 0)');

const revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($monthlyRevenue->pluck('month')),
        datasets: [{
            label: 'Revenue',
            data: @json($monthlyRevenue->pluck('total')),
            borderColor: '#0ea5e9',
            backgroundColor: gradient,
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#0ea5e9',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointShadowBlur: 10,
            pointShadowColor: 'rgba(14, 165, 233, 0.5)',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                titleColor: '#fff',
                bodyColor: '#cbd5e1',
                padding: 12,
                cornerRadius: 8,
                displayColors: false,
                callbacks: {
                    label: function(context) {
                        return '₦' + context.parsed.y.toLocaleString();
                    }
                }
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: '#64748b',
                    font: {
                        family: 'Inter',
                        size: 12
                    }
                },
                border: {
                    display: false
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(148, 163, 184, 0.1)',
                    drawBorder: false
                },
                ticks: {
                    color: '#64748b',
                    font: {
                        family: 'Inter',
                        size: 12
                    },
                    callback: function(value) {
                        return '₦' + (value / 1000) + 'k';
                    }
                },
                border: {
                    display: false
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index',
        },
    }
});
</script>
@endpush
