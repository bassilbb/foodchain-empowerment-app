@extends('admin.layouts.app')

@section('title', 'Savings Plans')
@section('header', 'Savings Plans')
@section('subheader', 'Manage savings plans and contribution options')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif

<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <p style="color: #64748b; margin: 0;">Configure savings plans (Bronze, Silver, Gold, Emerald) with multiple duration options</p>
    </div>
    <a href="{{ route('admin.savings-plans.create') }}" class="btn-primary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; vertical-align: middle;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add New Plan
    </a>
</div>

<div style="display: grid; grid-template-columns: repeat(1, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    @forelse($plans as $plan)
    @php
        $colorMap = ['bronze' => ['bg' => 'linear-gradient(135deg, #f59e0b, #d97706)', 'glow' => '#f59e0b'], 'silver' => ['bg' => 'linear-gradient(135deg, #64748b, #475569)', 'glow' => '#64748b'], 'gold' => ['bg' => 'linear-gradient(135deg, #eab308, #ca8a04)', 'glow' => '#eab308'], 'emerald' => ['bg' => 'linear-gradient(135deg, #10b981, #059669)', 'glow' => '#10b981']];
        $colors = $colorMap[strtolower($plan->name)] ?? ['bg' => 'linear-gradient(135deg, #10b981, #059669)', 'glow' => '#10b981'];
    @endphp
    <div class="page-card" style="position: relative; overflow: hidden;">
        <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: {{ $colors['glow'] }}; border-radius: 50%; filter: blur(40px); opacity: 0.2;"></div>
        <div style="position: relative; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.15); background: {{ $colors['bg'] }};">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="badge badge-{{ $plan->is_active ? 'success' : 'dark' }}">
                    {{ $plan->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0 0 0.25rem 0;">{{ $plan->name }}</h3>
            <p style="font-size: 0.875rem; color: #64748b; margin: 0 0 0.75rem 0;">Target: ₦{{ number_format($plan->target_amount, 2) }}</p>

            <div style="background: #f8fafc; border-radius: 12px; padding: 0.75rem; margin-bottom: 1rem;">
                <p style="font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; margin: 0 0 0.5rem 0;">Contribution Options</p>
                @forelse($plan->options as $option)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem;">
                    <span style="font-weight: 500;">{{ $option->duration_weeks }} weeks</span>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-weight: 600; color: #10b981;">₦{{ number_format($option->weekly_contribution, 2) }}/wk</span>
                        <form action="{{ route('admin.savings-plans.toggle-option', $option) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-sm" style="border: none; {{ $option->is_active ? 'background: #d1fae5; color: #065f46;' : 'background: #f1f5f9; color: #94a3b8;' }}">
                                {{ $option->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <p style="color: #94a3b8; font-size: 0.8rem; margin: 0;">No options configured</p>
                @endforelse
            </div>

            <div style="display: flex; gap: 0.5rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                <a href="{{ route('admin.savings-plans.edit', $plan) }}" class="btn-primary btn-sm" style="flex: 1; text-align: center;">
                    Edit Plan
                </a>
                <form action="{{ route('admin.savings-plans.destroy', $plan) }}" method="POST" style="flex: 1;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger btn-sm" style="width: 100%;"
                        data-confirm='{"title":"Delete Plan?","text":"Delete this plan? Existing accounts will not be affected.","btnText":"Delete","btnClass":"confirm-danger","iconClass":"danger"}'>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state" style="grid-column: 1 / -1;">
        <p>No savings plans yet. Create your first plan.</p>
    </div>
    @endforelse
</div>
@endsection