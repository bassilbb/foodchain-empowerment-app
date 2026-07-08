@extends('admin.layouts.app')

@section('title', 'Eligibility Rules')
@section('page-title', 'Loan Eligibility Configuration')
@section('header', 'Eligibility Rules')
@section('subheader', 'Configure who can apply for food loans')

@section('content')
<style>
    .page-header { background: linear-gradient(135deg, #10b981, #059669); border-radius: 20px; padding: 32px; color: white; margin-bottom: 24px; box-shadow: 0 10px 40px -10px rgba(16, 185, 129, 0.4); }
    .page-header h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 8px; }
    .page-header p { opacity: 0.9; font-size: 0.95rem; margin: 0; }
    .rules-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px; }
    .rule-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: all 0.3s ease; }
    .rule-card:hover { transform: translateY(-2px); box-shadow: 0 12px 30px -8px rgba(0,0,0,0.12); }
    .rule-header { padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; }
    .rule-info h3 { font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0 0 4px 0; }
    .rule-info p { font-size: 0.8rem; color: #64748b; margin: 0; }
    .rule-toggle { position: relative; }
    .toggle-switch { width: 52px; height: 28px; background: #e2e8f0; border-radius: 14px; cursor: pointer; transition: all 0.3s ease; position: relative; }
    .toggle-switch.active { background: linear-gradient(135deg, #10b981, #059669); }
    .toggle-switch::after { content: ''; position: absolute; top: 3px; left: 3px; width: 22px; height: 22px; background: white; border-radius: 50%; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    .toggle-switch.active::after { left: 27px; }
    .rule-body { padding: 24px; }
    .rule-value-input { display: flex; align-items: center; gap: 12px; }
    .value-input { width: 100px; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 1rem; font-weight: 600; text-align: center; color: #1e293b; background: #f8fafc; }
    .value-input:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }
    .value-suffix { font-size: 0.875rem; color: #64748b; font-weight: 500; }
    .rule-description { margin-top: 16px; padding: 12px 16px; background: #f8fafc; border-radius: 10px; font-size: 0.8rem; color: #64748b; line-height: 1.5; }
    .rule-footer { padding: 16px 24px; background: #fafbfc; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; }
    .btn-save { background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 10px 20px; border: none; border-radius: 10px; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
    .btn-save:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
    .info-box { background: linear-gradient(135deg, #ecfdf5, #d1fae5); border: 1px solid #10b981; border-radius: 16px; padding: 20px; margin-bottom: 24px; display: flex; gap: 16px; align-items: flex-start; }
    .info-icon { width: 40px; height: 40px; background: #10b981; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .info-icon svg { width: 20px; height: 20px; color: white; }
    .info-content h4 { font-size: 0.95rem; font-weight: 700; color: #065f46; margin: 0 0 4px 0; }
    .info-content p { font-size: 0.8rem; color: #047857; margin: 0; line-height: 1.5; }
    .category-title { font-size: 1.1rem; font-weight: 700; color: #1e293b; margin: 24px 0 16px 0; padding-bottom: 12px; border-bottom: 2px solid #10b981; display: flex; align-items: center; gap: 12px; }
    .category-icon { width: 32px; height: 32px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 8px; display: flex; align-items: center; justify-content: center; }
    .category-icon svg { width: 16px; height: 16px; color: white; }
    .success-msg { background: linear-gradient(135deg, #d1fae5, #a7f3d0); border: 1px solid #10b981; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #065f46; font-weight: 500; display: flex; align-items: center; gap: 12px; }
</style>

@if(session('success'))
<div class="success-msg">
    <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
    {{ session('success') }}
</div>
@endif

<div class="page-header">
    <h1>Food Loan Eligibility Rules</h1>
    <p>Configure the requirements users must meet to apply for food loans. Only users meeting ALL enabled criteria will be eligible.</p>
</div>

<div class="info-box">
    <div class="info-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    </div>
    <div class="info-content">
        <h4>How Eligibility Works</h4>
        <p>A user is eligible if they pass ALL enabled rules. By default, users must be in a union with at least 10 members and 30% equity contribution. You can customize these rules below or create alternative pathways based on referral count.</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.food-loans.eligibility-rules.batch') }}">
    @csrf

    <h2 class="category-title">
        <span class="category-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </span>
        Union Requirements
    </h2>
    <div class="rules-grid">
        @foreach(['union_membership', 'union_min_members', 'equity_percentage'] as $ruleKey)
        @php $rule = $rules[$ruleKey] ?? null; @endphp
        @if($rule)
        <div class="rule-card">
            <div class="rule-header">
                <div class="rule-info">
                    <h3>{{ $rule['name'] }}</h3>
                    <p>{{ $rule['type'] === 'boolean' ? 'Enable/disable this requirement' : 'Configure the required value' }}</p>
                </div>
                <div class="rule-toggle">
                    <input type="hidden" name="rules[{{ $ruleKey }}][enabled]" value="{{ $rule['enabled'] ? '1' : '0' }}">
                    <div class="toggle-switch {{ $rule['enabled'] ? 'active' : '' }}" 
                         onclick="this.classList.toggle('active'); this.previousElementSibling.value = this.classList.contains('active') ? '1' : '0';"
                         data-rule="{{ $ruleKey }}"></div>
                </div>
            </div>
            <div class="rule-body">
                @if($rule['type'] !== 'boolean')
                <div class="rule-value-input">
                    <input type="number" name="rules[{{ $ruleKey }}][value]" value="{{ $rule['value'] ?? 0 }}" 
                           class="value-input" min="{{ $rule['min'] ?? 0 }}" max="{{ $rule['max'] ?? 9999 }}">
                    <span class="value-suffix">
                        @if($ruleKey === 'equity_percentage')% @endif
                        @if($ruleKey === 'union_min_members') members @endif
                        @if($ruleKey === 'min_referral_count') referrals @endif
                        @if($ruleKey === 'min_membership_duration') days @endif
                    </span>
                </div>
                @endif
                <div class="rule-description">{{ $rule['description'] }}</div>
            </div>
        </div>
        @endif
        @endforeach
    </div>

    <h2 class="category-title">
        <span class="category-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
        </span>
        Alternative Pathways
    </h2>
    <div class="rules-grid">
        @foreach(['min_referral_count'] as $ruleKey)
        @php $rule = $rules[$ruleKey] ?? null; @endphp
        @if($rule)
        <div class="rule-card">
            <div class="rule-header">
                <div class="rule-info">
                    <h3>{{ $rule['name'] }}</h3>
                    <p>Alternative eligibility through referral network</p>
                </div>
                <div class="rule-toggle">
                    <input type="hidden" name="rules[{{ $ruleKey }}][enabled]" value="{{ $rule['enabled'] ? '1' : '0' }}">
                    <div class="toggle-switch {{ $rule['enabled'] ? 'active' : '' }}" 
                         onclick="this.classList.toggle('active'); this.previousElementSibling.value = this.classList.contains('active') ? '1' : '0';"
                         data-rule="{{ $ruleKey }}"></div>
                </div>
            </div>
            <div class="rule-body">
                @if($rule['type'] !== 'boolean')
                <div class="rule-value-input">
                    <input type="number" name="rules[{{ $ruleKey }}][value]" value="{{ $rule['value'] ?? 0 }}" 
                           class="value-input" min="{{ $rule['min'] ?? 0 }}" max="{{ $rule['max'] ?? 9999 }}">
                    <span class="value-suffix">referrals</span>
                </div>
                @endif
                <div class="rule-description">{{ $rule['description'] }}</div>
            </div>
        </div>
        @endif
        @endforeach
    </div>

    <h2 class="category-title">
        <span class="category-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
        </span>
        User Verification
    </h2>
    <div class="rules-grid">
        @foreach(['active_membership', 'kyc_verification', 'min_membership_duration'] as $ruleKey)
        @php $rule = $rules[$ruleKey] ?? null; @endphp
        @if($rule)
        <div class="rule-card">
            <div class="rule-header">
                <div class="rule-info">
                    <h3>{{ $rule['name'] }}</h3>
                    <p>{{ $rule['type'] === 'boolean' ? 'Enable/disable this requirement' : 'Configure the required value' }}</p>
                </div>
                <div class="rule-toggle">
                    <input type="hidden" name="rules[{{ $ruleKey }}][enabled]" value="{{ $rule['enabled'] ? '1' : '0' }}">
                    <div class="toggle-switch {{ $rule['enabled'] ? 'active' : '' }}" 
                         onclick="this.classList.toggle('active'); this.previousElementSibling.value = this.classList.contains('active') ? '1' : '0';"
                         data-rule="{{ $ruleKey }}"></div>
                </div>
            </div>
            <div class="rule-body">
                @if($rule['type'] !== 'boolean')
                <div class="rule-value-input">
                    <input type="number" name="rules[{{ $ruleKey }}][value]" value="{{ $rule['value'] ?? 0 }}" 
                           class="value-input" min="{{ $rule['min'] ?? 0 }}" max="{{ $rule['max'] ?? 9999 }}">
                    <span class="value-suffix">days</span>
                </div>
                @endif
                <div class="rule-description">{{ $rule['description'] }}</div>
            </div>
        </div>
        @endif
        @endforeach
    </div>

    <h2 class="category-title">
        <span class="category-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
        </span>
        Loan Status
    </h2>
    <div class="rules-grid">
        @foreach(['no_active_loan'] as $ruleKey)
        @php $rule = $rules[$ruleKey] ?? null; @endphp
        @if($rule)
        <div class="rule-card">
            <div class="rule-header">
                <div class="rule-info">
                    <h3>{{ $rule['name'] }}</h3>
                    <p>This rule is always enforced</p>
                </div>
                <div class="rule-toggle">
                    <div class="toggle-switch active" style="opacity: 0.7; cursor: not-allowed;"></div>
                </div>
            </div>
            <div class="rule-body">
                <div class="rule-description">{{ $rule['description'] }}</div>
            </div>
        </div>
        @endif
        @endforeach
    </div>

    <div style="margin-top: 32px; display: flex; justify-content: flex-end;">
        <button type="submit" class="btn-save">
            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            Save All Changes
        </button>
    </div>
</form>

<script>
document.querySelectorAll('.toggle-switch[data-rule]').forEach(toggle => {
    toggle.addEventListener('click', async function() {
        const ruleKey = this.dataset.rule;
        const isActive = this.classList.contains('active');
        
        try {
            const response = await fetch(`/admin/food-loans/eligibility-rules/${ruleKey}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            if (!response.ok) {
                this.classList.toggle('active');
            }
        } catch (error) {
            this.classList.toggle('active');
        }
    });
});
</script>
@endsection
