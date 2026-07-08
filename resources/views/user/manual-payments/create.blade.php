@extends('layouts.user')
@section('title', 'Make Payment')
@section('header', 'Make Manual Payment')
@section('subheader', 'Transfer to our account and upload proof')

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <a href="{{ route('manual-payments.index') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Payments
            </a>
            <h3 class="page-card-title" style="margin-top: 0.5rem;">Submit Manual Payment</h3>
            <p class="page-card-subtitle">Transfer to our account and upload proof of payment</p>
        </div>
    </div>
    <div class="page-card-body">
        <form action="{{ route('manual-payments.store') }}" method="POST" enctype="multipart/form-data" style="max-width: 600px;">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Payment Type</label>
                    <select name="type" required class="form-select">
                        <option value="membership">Membership</option>
                        <option value="starter_pack">Starter Pack</option>
                        <option value="renewal">Membership Renewal</option>
                        <option value="assistance_processing_fee">Food Assistance - Processing Fee</option>
                        <option value="assistance_first_installment">Food Assistance - First Installment</option>
                        <option value="assistance_weekly_repayment">Food Assistance - Weekly Repayment</option>
                        <option value="support_plan_contribution">Food Support - Plan Contribution</option>
                        <option value="wallet_topup">Wallet Top-Up</option>
                    </select>
            </div>

            <div class="form-group">
                <label class="form-label">Amount (₦)</label>
                <input type="number" name="amount" required class="form-input" placeholder="5000">
            </div>

            <div class="form-group">
                <label class="form-label">Select Bank Account</label>
                <select name="bank_account_id" required class="form-select">
                    <option value="">-- Select Bank --</option>
                    @foreach($bankAccounts as $account)
                    <option value="{{ $account->id }}">{{ $account->bank_name }} - {{ $account->account_number }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Your Name (as on bank account)</label>
                <input type="text" name="sender_name" required class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Transfer Date</label>
                <input type="date" name="transfer_date" required class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Payment Proof (Screenshot)</label>
                <input type="file" name="proof_image" required accept="image/*" class="form-input">
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <a href="{{ route('manual-payments.index') }}" class="btn btn-secondary" style="flex: 1; justify-content: center;">Cancel</a>
                <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Submit Payment</button>
            </div>
        </form>
    </div>
</div>
@endsection