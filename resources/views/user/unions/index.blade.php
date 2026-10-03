@extends('layouts.user')
@section('title', 'My Unions')
@section('header', 'My Unions')
@section('subheader', 'Join or create unions for food loans and community benefits')

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Your Unions</h3>
            <p class="page-card-subtitle">Manage your unions and track loans</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button onclick="document.getElementById('joinUnionModal').showModal()" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                Join Union
            </button>
            <a href="{{ route('unions.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create Union
            </a>
        </div>
    </div>
    <div class="page-card-body">
        @if(session('success'))
            <div class="alert alert-success">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; flex-shrink: 0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; flex-shrink: 0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        @if($unions->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="empty-title">No Unions Yet</h3>
                <p class="empty-text">Create a new union or join an existing one to access food loans and community benefits.</p>
                <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 1.5rem;">
                    <a href="{{ route('unions.create') }}" class="btn btn-primary">Create Union</a>
                    <button onclick="document.getElementById('joinUnionModal').showModal()" class="btn btn-secondary">Join Union</button>
                </div>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                @foreach($unions as $union)
                <div style="background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%); border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; transition: all 0.3s;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                        <div>
                            <h4 style="font-weight: 700; color: #1e293b; margin: 0; font-size: 1.1rem;">{{ $union->name }}</h4>
                            <span style="font-size: 0.75rem; color: #64748b; font-family: monospace;">{{ $union->code }}</span>
                        </div>
                        <span class="badge badge-{{ $union->status === 'active' ? 'success' : ($union->status === 'forming' ? 'warning' : 'info') }}">
                            {{ ucfirst($union->status) }}
                        </span>
                    </div>
                    
                    <div style="display: grid; gap: 0.75rem;">
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9;">
                            <span style="color: #64748b; font-size: 0.875rem;">Members</span>
                            <span style="font-weight: 600; color: #1e293b;">{{ $union->member_count }} / {{ $union->tier->union_min_members ?? 'N/A' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9;">
                            <span style="color: #64748b; font-size: 0.875rem;">Equity</span>
                            <span style="font-weight: 600; color: #10b981;">{{ format_currency($union->total_equity_contributed) }}</span>
                        </div>
                        @if($union->foodLoan)
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                            <span style="color: #64748b; font-size: 0.875rem;">Loan Balance</span>
                            <span style="font-weight: 600; color: #ea580c;">{{ format_currency($union->getLoanBalance()) }}</span>
                        </div>
                        @endif
                    </div>
                    
                    <a href="{{ route('unions.show', $union->id) }}" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 1rem;">
                        View Details
                    </a>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@if($loan)
<div class="page-card" style="background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); border: none;">
    <div class="page-card-body" style="color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-weight: 700; font-size: 1.25rem; margin: 0;">Active Food Loan</h3>
            <span style="padding: 0.25rem 0.75rem; background: rgba(255,255,255,0.2); border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Repaying</span>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1.5rem;">
            <div>
                <p style="opacity: 0.8; font-size: 0.75rem; margin-bottom: 0.25rem;">Reference</p>
                <p style="font-weight: 600; font-family: monospace;">{{ $loan->reference }}</p>
            </div>
            <div>
                <p style="opacity: 0.8; font-size: 0.75rem; margin-bottom: 0.25rem;">Amount</p>
                <p style="font-weight: 700; font-size: 1.25rem;">{{ format_currency($loan->loan_amount_approved) }}</p>
            </div>
            <div>
                <p style="opacity: 0.8; font-size: 0.75rem; margin-bottom: 0.25rem;">Balance</p>
                <p style="font-weight: 700; font-size: 1.25rem;">{{ format_currency($loan->remaining_balance) }}</p>
            </div>
            <div>
                <p style="opacity: 0.8; font-size: 0.75rem; margin-bottom: 0.25rem;">Progress</p>
                <p style="font-weight: 600;">{{ $loan->installments_paid }} / {{ $loan->total_installments }}</p>
                @php $progress = $loan && $loan->total_installments > 0 ? ($loan->installments_paid / $loan->total_installments) * 100 : 0; @endphp
                <div style="margin-top: 0.5rem; width: 100%; height: 8px; background: rgba(255,255,255,0.2); border-radius: 9999px; overflow: hidden;">
                    <div style="width: {{ $progress }}%; height: 100%; background: white; border-radius: 9999px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<dialog id="joinUnionModal" style="border: none; border-radius: 16px; padding: 0; max-width: 28rem; width: 100%; background: white; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
    <div style="padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #f1f5f9;">
            <h3 style="font-weight: 700; color: #1e293b; margin: 0; font-size: 1.1rem;">Join a Union</h3>
            <button onclick="document.getElementById('joinUnionModal').close()" style="background: none; border: none; cursor: pointer; color: #64748b; padding: 0.25rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('unions.join') }}" method="POST" style="display: grid; gap: 1rem;">
            @csrf
            <div class="form-group" style="margin: 0;">
                <label class="form-label">Union Code</label>
                <input type="text" name="union_code" required class="form-input" placeholder="Enter union code (e.g., UN-XXXXXXXX)">
            </div>
            <div class="form-group" style="margin: 0;">
                <label class="form-label">Equity Contribution (Optional)</label>
                <input type="number" name="equity_contribution" min="0" step="100" class="form-input" placeholder="Amount in Naira">
                <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.5rem;">Minimum 30% of loan amount required</p>
            </div>
            <div style="display: flex; gap: 0.75rem; padding-top: 0.5rem;">
                <button type="button" onclick="document.getElementById('joinUnionModal').close()" class="btn btn-secondary" style="flex: 1;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;">Join Union</button>
            </div>
        </form>
    </div>
</dialog>
@endsection