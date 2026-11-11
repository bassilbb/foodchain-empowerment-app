@extends('admin.layouts.app')

@section('title', 'Payment Settings')
@section('page-title', 'Payment Settings')

@section('header', 'Payment Settings')
@section('subheader', 'Choose which payment methods customers can use and configure the bank transfer account')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="alert alert-error">
    <ul style="margin: 0; padding-left: 1.1rem;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Summary -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Enabled Methods</p>
        <p class="stat-value">{{ $enabledCount }} / {{ $totalMethods }}</p>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Pending Verifications</p>
        <p class="stat-value">{{ number_format($stats['pending']) }}</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon sky">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Amount Awaiting Verification</p>
        <p class="stat-value">{{ format_currency($stats['awaiting_amount']) }}</p>
    </div>
</div>

<!-- Payment methods -->
<form method="POST" action="{{ route('admin.payment-settings.methods') }}">
    @csrf
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Available Payment Methods</h3>
                <p style="margin: 4px 0 0; font-size: 13px; color: #64748b;">Disabled methods disappear from checkout and are rejected server-side, even if a customer replays an old request.</p>
            </div>
            <button type="submit" class="btn-primary">Save Methods</button>
        </div>
        <div class="page-card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
                @foreach($methods as $method)
                <div style="border: 1px solid {{ $method['enabled'] ? '#a7f3d0' : '#e2e8f0' }}; background: {{ $method['enabled'] ? '#f0fdf4' : '#fff' }}; border-radius: 14px; padding: 16px;">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;">
                        <div>
                            <p style="font-weight: 700; color: #1e293b; margin: 0;">{{ $method['label'] }}</p>
                            <p style="font-size: 12px; color: #64748b; margin: 4px 0 0;">{{ $method['description'] }}</p>
                        </div>
                        <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; white-space: nowrap;">
                            <input type="checkbox" name="methods[{{ $method['key'] }}]" value="1"
                                {{ $method['enabled'] ? 'checked' : '' }}
                                style="width: 18px; height: 18px; accent-color: #10b981;">
                            <span style="font-size: 12px; font-weight: 600; color: {{ $method['enabled'] ? '#059669' : '#94a3b8' }};">
                                {{ $method['enabled'] ? 'Enabled' : 'Disabled' }}
                            </span>
                        </label>
                    </div>
                    @if($method['enabled'] && ! $method['available'])
                    <p style="margin: 10px 0 0; font-size: 12px; color: #b45309; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 6px 10px;">
                        Enabled, but not shown at checkout until its configuration is complete.
                    </p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</form>

<!-- Bank transfer configuration -->
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Bank Transfer Account</h3>
            <p style="margin: 4px 0 0; font-size: 13px; color: #64748b;">These are the exact details customers see at checkout and on their order page. They are never hard-coded.</p>
        </div>
        <button type="button" class="btn" style="background: #eef2ff; color: #3730a3;" onclick="previewSettings()">Preview Customer View</button>
    </div>
    <div class="page-card-body">
        <form method="POST" action="{{ route('admin.payment-settings.bank-transfer') }}">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $bankAccount->bank_name ?? '') }}" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Account Name</label>
                    <input type="text" name="account_name" value="{{ old('account_name', $bankAccount->account_name ?? '') }}" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Account Number</label>
                    <input type="text" name="account_number" value="{{ old('account_number', $bankAccount->account_number ?? '') }}" class="form-input" required>
                </div>
            </div>

            <div class="form-group" style="margin-top: 8px;">
                <label class="form-label">Transfer Instructions (optional)</label>
                <textarea name="instructions" rows="3" class="form-input" placeholder="e.g. Use your order number as the transfer narration.">{{ old('instructions', $bankAccount->instructions ?? '') }}</textarea>
            </div>

            <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; margin: 8px 0 18px;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $bankAccount->is_active ?? true) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #10b981;">
                <span style="font-size: 13px; font-weight: 600; color: #334155;">Bank transfer account enabled</span>
            </label>

            <div>
                <button type="submit" class="btn-primary">Save Bank Transfer Account</button>
            </div>
        </form>
    </div>
</div>

<!-- Preview panel -->
<div id="settings-preview" class="page-card" style="display: none;">
    <div class="page-card-header">
        <h3 class="page-card-title">Customer Preview</h3>
        <button type="button" class="btn" style="background: #f1f5f9; color: #475569;" onclick="document.getElementById('settings-preview').style.display='none'">Close</button>
    </div>
    <div class="page-card-body">
        <pre id="settings-preview-body" style="background: #0f172a; color: #e2e8f0; padding: 16px; border-radius: 12px; overflow-x: auto; font-size: 12px; margin: 0;"></pre>
    </div>
</div>

@push('scripts')
<script>
    async function previewSettings() {
        const panel = document.getElementById('settings-preview');
        const body = document.getElementById('settings-preview-body');

        body.textContent = 'Loading…';
        panel.style.display = 'block';

        try {
            const response = await fetch('{{ route('admin.payment-settings.preview') }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            const data = await response.json();
            body.textContent = JSON.stringify(data, null, 2);
        } catch (e) {
            body.textContent = 'Could not load preview.';
        }
    }
</script>
@endpush
@endsection
