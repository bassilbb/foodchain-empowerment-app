@extends('layouts.user')
@section('title', 'Subscribe - Food Support')
@section('header', 'Start a Food Support Plan')
@section('subheader', 'Save towards food and earn 20% bonus at maturity')

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Choose Your Plan</h3>
            <p class="page-card-subtitle">Select a savings duration and set your target</p>
        </div>
    </div>
    <div class="page-card-body">
        <style>
            .plan-type-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem; }
            .plan-type-card { background: white; border: 2px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; text-align: center; cursor: pointer; transition: all 0.3s; }
            .plan-type-card:hover { border-color: #10b981; transform: translateY(-2px); }
            .plan-type-card.selected { border-color: #10b981; background: #f0fdf4; }
            .plan-duration { font-size: 1.5rem; font-weight: 700; color: #1e293b; }
            .plan-label { font-size: 0.85rem; color: #64748b; margin-top: 4px; }
            .plan-icon { font-size: 2rem; margin-bottom: 0.5rem; }
            .calc-result { background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px; padding: 1.5rem; color: white; text-align: center; margin-top: 1rem; }
            .calc-amount { font-size: 2.5rem; font-weight: 800; }
            .calc-label { font-size: 0.85rem; opacity: 0.9; }
        </style>

        <form method="POST" action="{{ route('user.food-support.store') }}" id="subscribeForm">
            @csrf
            <input type="hidden" name="plan_type" id="planType" value="">
            <input type="hidden" name="terms" value="1">

            <div class="plan-type-grid">
                <div class="plan-type-card" data-type="six_months" onclick="selectPlanType(this)">
                    <div class="plan-icon">📦</div>
                    <div class="plan-duration">6 Months</div>
                    <div class="plan-label">Short-term savings plan</div>
                    <div style="margin-top: 0.5rem; font-size: 0.8rem; color: #10b981; font-weight: 600;">+20% bonus at maturity</div>
                </div>
                <div class="plan-type-card" data-type="one_year" onclick="selectPlanType(this)">
                    <div class="plan-icon">🎯</div>
                    <div class="plan-duration">1 Year</div>
                    <div class="plan-label">Long-term savings plan</div>
                    <div style="margin-top: 0.5rem; font-size: 0.8rem; color: #10b981; font-weight: 600;">+20% bonus at maturity</div>
                </div>
            </div>

            <div id="valueInput" style="display: none;">
                <div class="form-group">
                    <label class="form-label">How much do you want to save?</label>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 1.5rem; font-weight: 700; color: #10b981;">₦</span>
                        <input type="number" name="plan_value" id="planValue" class="form-control" min="1000" step="100" placeholder="e.g. 50000" style="flex: 1; font-size: 1.25rem; padding: 12px; border: 2px solid #e2e8f0; border-radius: 12px;" oninput="updateCalc()">
                    </div>
                    @error('plan_value') <span style="color: #ef4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div id="calcResult" class="calc-result" style="display: none;">
                    <div class="calc-label">At maturity, you get</div>
                    <div class="calc-amount" id="payoutAmount">₦0</div>
                    <div style="font-size: 0.85rem; opacity: 0.9; margin-top: 8px;">
                        Your contribution: ₦<span id="contributionDisplay">0</span> + 
                        20% bonus: ₦<span id="bonusDisplay">0</span>
                    </div>
                </div>

                <div style="margin-top: 1.5rem; display: flex; align-items: flex-start; gap: 8px;">
                    <input type="checkbox" id="termsCheck" required style="width: 18px; height: 18px; margin-top: 3px;">
                    <label for="termsCheck" style="font-size: 0.85rem; color: #475569;">I understand that my savings will be locked until the maturity date and I will receive my contributions plus 20% bonus in food items at maturity.</label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 1rem;" id="submitBtn" disabled>
                    Proceed to Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function selectPlanType(el) {
    document.querySelectorAll('.plan-type-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('planType').value = el.dataset.type;
    document.getElementById('valueInput').style.display = 'block';
    updateCalc();
}

function updateCalc() {
    const value = parseFloat(document.getElementById('planValue').value) || 0;
    if (value >= 1000) {
        const bonus = value * 0.20;
        const total = value + bonus;
        document.getElementById('calcResult').style.display = 'block';
        document.getElementById('payoutAmount').textContent = '₦' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('contributionDisplay').textContent = value.toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('bonusDisplay').textContent = bonus.toLocaleString(undefined, {minimumFractionDigits: 2});
    } else {
        document.getElementById('calcResult').style.display = 'none';
    }
    checkForm();
}

document.getElementById('termsCheck').addEventListener('change', checkForm);
document.getElementById('planValue').addEventListener('input', checkForm);

function checkForm() {
    const type = document.getElementById('planType').value;
    const value = parseFloat(document.getElementById('planValue').value) || 0;
    const terms = document.getElementById('termsCheck').checked;
    document.getElementById('submitBtn').disabled = !(type && value >= 1000 && terms);
}
</script>
@endsection
