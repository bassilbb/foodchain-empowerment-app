@extends('layouts.user')
@section('title', 'Create Account')
@section('header', 'Create Account')
@section('subheader', 'Open a new participation account')

@section('content')
<div style="max-width: 720px; margin: 0 auto;">
    <a href="{{ route('user.accounts.index') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: white; border-radius: 999px; font-size: 0.8125rem; font-weight: 600; color: #475569; text-decoration: none; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 1.5rem; transition: all 0.2s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.1)'; this.style.transform='translateX(-3px)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.06)'; this.style.transform='translateX(0)'">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Accounts
    </a>

    <div class="form-3d-card">
        <div class="form-3d-card-inner">
            <div class="form-3d-header">
                <div class="form-3d-icon-wrapper">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 28px; height: 28px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <h3 class="form-3d-title">New Participation Account</h3>
                    <p class="form-3d-subtitle">Select a savings plan and choose your preferred duration</p>
                </div>
            </div>

            <form action="{{ route('user.accounts.store') }}" method="POST">
                @csrf

                <input type="hidden" name="savings_plan_option_id" id="savingsPlanOptionId" value="">

                <div class="form-3d-group">
                    <label class="form-3d-label">Account Name</label>
                    <div class="form-3d-input-wrapper">
                        <div class="form-3d-input-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        </div>
                        <input type="text" name="account_name" required class="form-3d-input" placeholder="e.g., My Food Fund" value="{{ old('account_name') }}">
                    </div>
                    @error('account_name') <p class="form-3d-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-3d-group">
                    <label class="form-3d-label">Choose Your Savings Plan</label>
                    <div class="plan-card-grid">
                        @php
                            $colors = [
                                'Bronze' => ['bg' => 'linear-gradient(135deg, #f59e0b, #d97706)', 'glow' => '#f59e0b', 'light' => '#fef3c7'],
                                'Silver' => ['bg' => 'linear-gradient(135deg, #64748b, #475569)', 'glow' => '#64748b', 'light' => '#f1f5f9'],
                                'Gold' => ['bg' => 'linear-gradient(135deg, #eab308, #ca8a04)', 'glow' => '#eab308', 'light' => '#fefce8'],
                                'Emerald' => ['bg' => 'linear-gradient(135deg, #10b981, #059669)', 'glow' => '#10b981', 'light' => '#ecfdf5'],
                            ];
                        @endphp
                        @foreach($savingsPlans as $plan)
                        @php $c = $colors[$plan->name] ?? $colors['Emerald']; @endphp
                        <div class="plan-card" data-plan-id="{{ $plan->id }}" onclick="selectPlan({{ $plan->id }})" style="border-color: {{ $plan->activeOptions->count() > 0 ? '#e2e8f0' : '#fecaca' }};">
                            <div class="plan-card-glow" style="background: radial-gradient(circle, {{ $c['glow'] }}10 0%, transparent 70%);"></div>
                            <div class="plan-card-icon" style="background: {{ $c['bg'] }};">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="plan-card-name">{{ $plan->name }}</div>
                            <div class="plan-card-target">Target: ₦{{ number_format($plan->target_amount, 2) }}</div>
                            @if($plan->activeOptions->count() === 0)
                            <div style="font-size: 0.7rem; color: #ef4444; margin-top: 4px;">No options available</div>
                            @endif
                            <div class="plan-card-check">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @error('savings_plan_option_id') <p class="form-3d-error">{{ $message }}</p> @enderror
                </div>

                <div id="optionsSection" class="form-3d-group" style="display: none;">
                    <label class="form-3d-label">Choose Your Savings Duration</label>
                    <div id="optionsList" class="options-list"></div>
                </div>

                <div id="planDetails" class="plan-details-3d" style="display: none;">
                    <div class="plan-details-glow"></div>
                    <h4 class="plan-details-title">Plan Summary</h4>
                    <div class="plan-details-grid">
                        <div class="plan-detail-item">
                            <div class="plan-detail-icon" style="background: #d1fae5;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; color: #059669;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="plan-detail-label">Weekly Amount</p>
                                <p class="plan-detail-value" id="planWeekly">₦0</p>
                            </div>
                        </div>
                        <div class="plan-detail-item">
                            <div class="plan-detail-icon" style="background: #dbeafe;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; color: #2563eb;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="plan-detail-label">Duration</p>
                                <p class="plan-detail-value" id="planWeeks">0 weeks</p>
                            </div>
                        </div>
                        <div class="plan-detail-item">
                            <div class="plan-detail-icon" style="background: #fef3c7;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; color: #d97706;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div>
                                <p class="plan-detail-label">Total Contribution</p>
                                <p class="plan-detail-value" id="planTotal">₦0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-3d-group">
                    <label class="form-3d-label">Referral Code <span class="form-3d-optional">(optional)</span></label>
                    <div class="form-3d-input-wrapper">
                        <div class="form-3d-input-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <input type="text" name="referral_code" class="form-3d-input" placeholder="Enter referrer's code" value="{{ old('referral_code') }}" maxlength="8" style="text-transform: uppercase;">
                    </div>
                    <p class="form-3d-hint">If someone referred you, enter their referral code here</p>
                    @error('referral_code') <p class="form-3d-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-3d-actions">
                    <a href="{{ route('user.accounts.index') }}" class="form-3d-btn form-3d-btn-secondary">Cancel</a>
                    <button type="submit" class="form-3d-btn form-3d-btn-primary" id="submitBtn" disabled>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Card Container */
.form-3d-card {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 24px;
    box-shadow:
        0 15px 50px rgba(0,0,0,0.08),
        0 0 0 1px rgba(255,255,255,0.5) inset,
        0 -2px 15px rgba(16, 185, 129, 0.03) inset;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.form-3d-card:hover {
    transform: translateY(-4px);
    box-shadow:
        0 25px 60px rgba(0,0,0,0.12),
        0 0 0 1px rgba(16, 185, 129, 0.08) inset;
}
.form-3d-card-inner { padding: 2rem; }

/* Header */
.form-3d-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}
.form-3d-icon-wrapper {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.form-3d-icon-wrapper svg { width: 28px; height: 28px; color: #059669; }
.form-3d-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}
.form-3d-subtitle {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0.25rem 0 0;
}

/* Form Groups */
.form-3d-group { margin-bottom: 1.5rem; }
.form-3d-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.75rem;
}
.form-3d-optional {
    font-weight: 400;
    color: #9ca3af;
    font-size: 0.8rem;
}

/* Input Wrapper */
.form-3d-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}
.form-3d-input-icon {
    position: absolute;
    left: 14px;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #f0fdf4;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
    z-index: 1;
}
.form-3d-input-icon svg { width: 18px; height: 18px; color: #10b981; }
.form-3d-input {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 3.5rem;
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    font-size: 0.9375rem;
    color: #1e293b;
    background: white;
    transition: all 0.2s;
    outline: none;
    font-family: 'Inter', sans-serif;
}
.form-3d-input:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
    background: #fafdfa;
}
.form-3d-input::placeholder { color: #9ca3af; }
.form-3d-hint {
    font-size: 0.8rem;
    color: #94a3b8;
    margin-top: 0.5rem;
}
.form-3d-error {
    font-size: 0.8rem;
    color: #dc2626;
    margin-top: 0.5rem;
    font-weight: 500;
}

/* Plan Cards Grid */
.plan-card-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}
.plan-card {
    position: relative;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    padding: 1.25rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}
.plan-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
}
.plan-card.selected {
    border-color: #10b981;
    background: #f0fdf4;
}
.plan-card.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.plan-card-glow {
    position: absolute;
    top: -30px;
    right: -30px;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    pointer-events: none;
}
.plan-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin: 0 auto 0.5rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.plan-card-icon svg { width: 20px; height: 20px; }
.plan-card-name { font-size: 1rem; font-weight: 700; color: #1e293b; }
.plan-card-target { font-size: 0.8rem; color: #64748b; margin-top: 2px; }
.plan-card-check {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #10b981;
    color: white;
    display: none;
    align-items: center;
    justify-content: center;
}
.plan-card-check svg { width: 14px; height: 14px; }
.plan-card.selected .plan-card-check { display: flex; }

/* Options List */
.options-list { display: flex; flex-direction: column; gap: 0.5rem; }
.option-radio {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1rem;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.2s;
}
.option-radio:hover { border-color: #94a3b8; }
.option-radio.selected { border-color: #10b981; background: #f0fdf4; }
.option-radio input[type="radio"] { width: 18px; height: 18px; accent-color: #10b981; margin: 0; }

/* Plan Details */
.plan-details-3d {
    position: relative;
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    border: 2px solid #bbf7d0;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    overflow: hidden;
}
.plan-details-glow {
    position: absolute;
    top: -40px;
    right: -40px;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(16,185,129,0.12) 0%, transparent 70%);
    pointer-events: none;
}
.plan-details-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: #065f46;
    margin: 0 0 1rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.plan-details-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
}
.plan-detail-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: rgba(255,255,255,0.7);
    border-radius: 12px;
}
.plan-detail-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.plan-detail-icon svg { width: 16px; height: 16px; }
.plan-detail-label {
    font-size: 0.7rem;
    font-weight: 600;
    color: #6b7280;
    margin: 0 0 2px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.plan-detail-value {
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

/* Action Buttons */
.form-3d-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #f1f5f9;
}
.form-3d-btn {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.875rem 1.5rem;
    border-radius: 14px;
    font-size: 0.9375rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    border: none;
}
.form-3d-btn-primary {
    background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
    background-size: 200% 200%;
    color: white;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
}
.form-3d-btn-primary:hover:not(:disabled) {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(16, 185, 129, 0.5);
    background-position: 100% 100%;
}
.form-3d-btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.form-3d-btn-secondary {
    background: white;
    color: #475569;
    border: 2px solid #e2e8f0;
}
.form-3d-btn-secondary:hover {
    border-color: #94a3b8;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}
.form-3d-btn svg { width: 18px; height: 18px; }

@media (max-width: 640px) {
    .plan-card-grid { grid-template-columns: 1fr 1fr; gap: 0.5rem; }
    .plan-card { padding: 1rem; }
    .plan-details-grid { grid-template-columns: 1fr; }
    .form-3d-actions { flex-direction: column; }
    .form-3d-card-inner { padding: 1.25rem; }
}
</style>

@push('scripts')
<script>
const plansData = @json($plansData);

let selectedPlanId = null;
let selectedOptionId = null;

function selectPlan(planId) {
    document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
    document.querySelector(`.plan-card[data-plan-id="${planId}"]`).classList.add('selected');
    selectedPlanId = planId;
    selectedOptionId = null;
    document.getElementById('savingsPlanOptionId').value = '';
    document.getElementById('planDetails').style.display = 'none';

    const plan = plansData[planId];
    const list = document.getElementById('optionsList');
    list.innerHTML = '';

    if (plan && plan.options.length > 0) {
        document.getElementById('optionsSection').style.display = 'block';
        plan.options.forEach(opt => {
            const div = document.createElement('div');
            div.className = 'option-radio';
            div.onclick = () => selectOption(opt.id);
            div.innerHTML = `
                <input type="radio" name="option_radio" value="${opt.id}">
                <div style="flex: 1;">
                    <div style="font-weight: 600; color: #1e293b;">${opt.weeks} Weeks</div>
                    <div style="font-size: 0.85rem; color: #10b981; font-weight: 600;">₦${opt.amount.toLocaleString(undefined, {minimumFractionDigits: 2})} weekly contribution</div>
                </div>
            `;
            list.appendChild(div);
        });
    } else {
        document.getElementById('optionsSection').style.display = 'none';
    }
    updateSubmitBtn();
}

function selectOption(optionId) {
    document.querySelectorAll('.option-radio').forEach(r => r.classList.remove('selected'));
    document.querySelectorAll('.option-radio input').forEach(r => { if (r.value == optionId) { r.checked = true; r.closest('.option-radio').classList.add('selected'); }});
    selectedOptionId = optionId;
    document.getElementById('savingsPlanOptionId').value = optionId;

    const plan = plansData[selectedPlanId];
    const opt = plan.options.find(o => o.id == optionId);
    if (opt) {
        const total = Math.round(opt.amount * opt.weeks);
        document.getElementById('planWeekly').textContent = '₦' + opt.amount.toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('planWeeks').textContent = opt.weeks + ' weeks';
        document.getElementById('planTotal').textContent = '₦' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('planDetails').style.display = 'block';
        document.getElementById('planDetails').style.animation = 'fadeSlideIn 0.4s ease-out';
    }
    updateSubmitBtn();
}

function updateSubmitBtn() {
    document.getElementById('submitBtn').disabled = !(selectedOptionId && document.querySelector('input[name="account_name"]').value.trim());
}

document.querySelector('input[name="account_name"]').addEventListener('input', updateSubmitBtn);
</script>
<style>
@keyframes fadeSlideIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush
@endsection
