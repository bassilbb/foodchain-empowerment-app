@extends('layouts.user')
@section('title', 'Apply - Food Assistance')
@section('header', 'Apply for Food Assistance')
@section('subheader', 'Select a plan tier to get started')

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Choose Your Plan</h3>
            <p class="page-card-subtitle">Select a food assistance tier that suits your needs</p>
        </div>
    </div>
    <div class="page-card-body">
        <style>
            .tier-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
            .tier-card { background: white; border: 2px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; text-align: center; cursor: pointer; transition: all 0.3s; }
            .tier-card:hover { border-color: #10b981; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(16,185,129,0.1); }
            .tier-card.selected { border-color: #10b981; background: #f0fdf4; }
            .tier-name { font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; }
            .tier-amount { font-size: 2.5rem; font-weight: 800; color: #10b981; margin-bottom: 0.5rem; }
            .tier-desc { font-size: 0.85rem; color: #64748b; margin-bottom: 1rem; }
            .tier-features { text-align: left; font-size: 0.85rem; color: #475569; }
            .tier-features li { padding: 4px 0; list-style: none; }
            .tier-features li:before { content: "✓ "; color: #10b981; font-weight: 700; }
            .calculator { background: #f8fafc; border-radius: 12px; padding: 1.5rem; margin-top: 1rem; }
            .calc-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0; font-size: 0.9rem; }
            .calc-row.total { border-bottom: none; font-weight: 700; font-size: 1.1rem; color: #1e293b; }
            .calc-value { font-weight: 600; }
            .calc-value.fee { color: #ef4444; }
            .calc-value.inst { color: #3b82f6; }
            .calc-value.weekly { color: #10b981; }
        </style>

        <form method="POST" action="{{ route('user.food-assistance.store') }}" id="applyForm">
            @csrf
            <input type="hidden" name="tier_id" id="selectedTierId" value="">

            <div class="tier-grid">
                @foreach($tiers as $tier)
                <div class="tier-card" data-tier-id="{{ $tier->id }}" data-amount="{{ $tier->amount }}" onclick="selectTier(this)">
                    <div class="tier-name">{{ $tier->name }}</div>
                    <div class="tier-amount">₦{{ number_format($tier->amount) }}</div>
                    <div class="tier-desc">{{ $tier->description ?? 'Food assistance plan' }}</div>
                    <ul class="tier-features">
                        <li>₦3,500 processing fee (non-refundable)</li>
                        <li>30% down payment before collection</li>
                        <li>Balance in 12 weekly installments</li>
                        <li>Food items worth ₦{{ number_format($tier->amount) }}</li>
                    </ul>
                </div>
                @endforeach
            </div>

            <div id="calculator" style="display: none;" class="calculator">
                <h4 style="margin: 0 0 1rem; color: #1e293b;">Payment Breakdown</h4>
                <div class="calc-row"><span>Plan Value (Food Items)</span><span class="calc-value" id="calcTotal">₦0</span></div>
                <div class="calc-row"><span>Processing Fee (non-refundable)</span><span class="calc-value fee">₦3,500</span></div>
                <div class="calc-row"><span>First Installment (30%)</span><span class="calc-value inst" id="calcFirstInst">₦0</span></div>
                <div class="calc-row"><span>Balance to spread</span><span class="calc-value" id="calcBalance">₦0</span></div>
                <div class="calc-row total"><span>Weekly Installment (12 weeks)</span><span class="calc-value weekly" id="calcWeekly">₦0</span></div>

                <div style="margin-top: 1rem; display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="terms" id="terms" required style="width: 18px; height: 18px;">
                    <label for="terms" style="font-size: 0.85rem; color: #475569;">I understand the payment terms and agree to the ₦3,500 non-refundable processing fee and weekly repayment schedule.</label>
                </div>
                @error('terms') <span style="color: #ef4444; font-size: 0.8rem;">{{ $message }}</span> @enderror

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 1rem;" id="submitBtn" disabled>
                    Proceed to Payment (₦3,500 Processing Fee)
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function selectTier(el) {
    document.querySelectorAll('.tier-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    const amount = parseFloat(el.dataset.amount);
    const tierId = el.dataset.tierId;
    document.getElementById('selectedTierId').value = tierId;

    const processingFee = 3500;
    const firstInst = amount * 0.30;
    const balance = amount - firstInst;
    const weekly = balance / 12;

    document.getElementById('calcTotal').textContent = '₦' + amount.toLocaleString();
    document.getElementById('calcFirstInst').textContent = '₦' + firstInst.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('calcBalance').textContent = '₦' + balance.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('calcWeekly').textContent = '₦' + weekly.toLocaleString(undefined, {minimumFractionDigits: 2});

    document.getElementById('calculator').style.display = 'block';
    document.getElementById('submitBtn').disabled = false;
}

document.getElementById('terms').addEventListener('change', function() {
    document.getElementById('submitBtn').disabled = !this.checked || !document.getElementById('selectedTierId').value;
});
</script>
@endsection
