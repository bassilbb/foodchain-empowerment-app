@extends('admin.layouts.app')
@section('title', 'Create Tier')
@section('header', 'Create Plan Tier')
@section('subheader', 'Add a new food assistance plan tier')

@section('content')
<style>
    .form-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; padding: 32px; max-width: 600px; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .form-input { width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.9rem; transition: all 0.2s; box-sizing: border-box; }
    .form-input:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.1); }
    .form-textarea { width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.9rem; min-height: 100px; resize: vertical; box-sizing: border-box; }
    .btn-save { background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.9rem; }
    .btn-save:hover { opacity: 0.9; }
    .btn-back { color: #64748b; text-decoration: none; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 4px; margin-bottom: 16px; }
</style>

<a href="{{ route('admin.food-assistance-tiers.index') }}" class="btn-back">← Back to Tiers</a>

<div class="form-card">
    <form action="{{ route('admin.food-assistance-tiers.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label">Tier Name</label>
            <input type="text" name="name" class="form-input" value="{{ old('name') }}" required placeholder="e.g. Basic, Standard, Premium">
            @error('name') <span style="color: #ef4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Amount (₦)</label>
            <input type="number" name="amount" class="form-input" value="{{ old('amount') }}" required min="0" step="0.01" placeholder="e.g. 50000">
            @error('amount') <span style="color: #ef4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-textarea" placeholder="What does this tier include?">{{ old('description') }}</textarea>
            @error('description') <span style="color: #ef4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="btn-save">Create Tier</button>
    </form>
</div>
@endsection
