@extends('layouts.user')
@section('title', $union->name)
@section('header', 'Union Details')
@section('subheader', $union->name)

@section('content')
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

<div class="page-card">
    <div class="page-card-header">
        <div>
            <a href="{{ route('unions.index') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem; margin-bottom: 0.5rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Unions
            </a>
            <h3 class="page-card-title" style="margin: 0;">{{ $union->name }}</h3>
            <p class="page-card-subtitle">Code: <span style="font-family: monospace; background: #f1f5f9; padding: 0.25rem 0.5rem; border-radius: 4px;">{{ $union->code }}</span></p>
        </div>
        <span class="badge {{ $union->status === 'active' ? 'badge-success' : ($union->status === 'forming' ? 'badge-warning' : ($union->status === 'disbursed' ? 'badge-info' : 'badge-purple')) }}">
            {{ ucfirst($union->status) }}
        </span>
    </div>
    <div class="page-card-body">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
            <div style="text-align: center; padding: 1rem; background: #f8fafc; border-radius: 12px;">
                <div style="font-size: 1.5rem; font-weight: 700; color: #10b981;">{{ $union->member_count }}</div>
                <div style="font-size: 0.75rem; color: #64748b;">Members</div>
            </div>
            <div style="text-align: center; padding: 1rem; background: #f8fafc; border-radius: 12px;">
                <div style="font-size: 1.5rem; font-weight: 700; color: #10b981;">{{ $union->tier->union_min_members ?? 10 }}</div>
                <div style="font-size: 0.75rem; color: #64748b;">Min Required</div>
            </div>
            <div style="text-align: center; padding: 1rem; background: #f8fafc; border-radius: 12px;">
                <div style="font-size: 1.5rem; font-weight: 700; color: #10b981;">₦{{ number_format($union->total_equity_contributed, 0) }}</div>
                <div style="font-size: 0.75rem; color: #64748b;">Total Equity</div>
            </div>
            <div style="text-align: center; padding: 1rem; background: #f8fafc; border-radius: 12px;">
                <div style="font-size: 1.5rem; font-weight: 700; color: #10b981;">{{ round($union->getEquityContributedPercentage()) }}%</div>
                <div style="font-size: 0.75rem; color: #64748b;">Equity Progress</div>
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <div>
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Members</h3>
            </div>
            <div class="page-card-body" style="padding: 0;">
                @forelse($union->members as $member)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: #d1fae5; display: flex; align-items: center; justify-content: center;">
                                <span style="color: #059669; font-weight: 600;">{{ substr($member->user->first_name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $member->user->full_name }}</p>
                                <p style="color: #64748b; font-size: 0.875rem; margin: 0;">{{ $member->user->email }}</p>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            @if($member->is_leader)
                                <span class="badge badge-warning" style="font-size: 0.625rem;">Leader</span>
                            @endif
                            <p style="color: #64748b; font-size: 0.75rem; margin: 0.25rem 0 0 0;">Equity: ₦{{ number_format($member->equity_contributed, 2) }}</p>
                        </div>
                    </div>
                @empty
                    <div style="padding: 2rem; text-align: center; color: #94a3b8;">No members yet</div>
                @endforelse
            </div>
        </div>

        @if($union->foodLoan)
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Food Loan</h3>
            </div>
            <div class="page-card-body">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 0.25rem 0;">Reference</p>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $union->foodLoan->reference }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 0.25rem 0;">Status</p>
                        <span class="badge {{ $union->foodLoan->status === 'approved' ? 'badge-info' : ($union->foodLoan->status === 'disbursed' ? 'badge-purple' : ($union->foodLoan->status === 'repaying' ? 'badge-warning' : 'badge-success')) }}">
                            {{ ucfirst($union->foodLoan->status) }}
                        </span>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 0.25rem 0;">Amount Approved</p>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">₦{{ number_format($union->foodLoan->loan_amount_approved, 2) }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 0.25rem 0;">Remaining Balance</p>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">₦{{ number_format($union->foodLoan->remaining_balance, 2) }}</p>
                    </div>
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.5rem;">
                        <span style="color: #475569;">Repayment Progress</span>
                        <span style="color: #64748b;">{{ $union->foodLoan->installments_paid }} / {{ $union->foodLoan->total_installments }} installments</span>
                    </div>
                    <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 9999px; overflow: hidden;">
                        @php $progress = $union->foodLoan->total_installments > 0 ? ($union->foodLoan->installments_paid / $union->foodLoan->total_installments) * 100 : 0; @endphp
                        <div style="width: {{ $progress }}%; height: 100%; background: linear-gradient(135deg, #10b981, #059669); border-radius: 9999px;"></div>
                    </div>
                </div>

                <div style="display: flex; gap: 8px;">
                    @if(in_array($union->foodLoan->status, ['disbursed', 'repaying']))
                    <a href="{{ route('unions.repayment-schedule', $union->id) }}" class="btn btn-secondary" style="flex: 1; justify-content: center;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Schedule
                    </a>
                    <button onclick="document.getElementById('repaymentModal').showModal()" class="btn btn-primary" style="flex: 1; justify-content: center;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Pay
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <div>
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Union Leader</h3>
            </div>
            <div class="page-card-body">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef3c7; display: flex; align-items: center; justify-content: center;">
                        <span style="color: #d97706; font-weight: 600;">{{ substr($union->leader->first_name, 0, 1) }}</span>
                    </div>
                    <div>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $union->leader->full_name }}</p>
                        <p style="color: #64748b; font-size: 0.875rem; margin: 0;">{{ $union->leader->email }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Invite Members</h3>
            </div>
            <div class="page-card-body">
                <p style="color: #64748b; font-size: 0.875rem; margin: 0 0 0.75rem 0;">Share this code with people you want to join your union:</p>
                <div style="background: #f1f5f9; padding: 0.75rem; border-radius: 8px; text-align: center;">
                    <span style="font-size: 1.25rem; font-weight: 700; font-family: monospace; color: #475569;">{{ $union->code }}</span>
                </div>
                <button onclick="copyToClipboard('{{ $union->code }}')" class="btn btn-secondary" style="width: 100%; justify-content: center; margin-top: 0.75rem;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    Copy Code
                </button>
            </div>
        </div>

        @if($isLeader && $union->status === 'active')
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Leader Actions</h3>
            </div>
            <div class="page-card-body">
                @if(!$union->foodLoan || !in_array($union->foodLoan->status, ['pending', 'approved', 'disbursed', 'repaying']))
                <button onclick="document.getElementById('loanModal').showModal()" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Apply for Loan
                </button>
                @endif
            </div>
        </div>
        @endif

        @if(!$isLeader)
        <div class="page-card">
            <div class="page-card-body">
                <button onclick="confirmLeave()" class="btn" style="width: 100%; justify-content: center; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Leave Union
                </button>
            </div>
        </div>
        @endif
    </div>
</div>

@if($isLeader && (!$union->foodLoan || !in_array($union->foodLoan->status, ['pending', 'approved', 'disbursed', 'repaying'])))
<dialog id="loanModal" style="border: none; border-radius: 16px; padding: 0; max-width: 28rem; width: 100%; background: white; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
    <div style="padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #f1f5f9;">
            <h3 style="font-weight: 700; color: #1e293b; margin: 0; font-size: 1.1rem;">Apply for Food Loan</h3>
            <button onclick="document.getElementById('loanModal').close()" style="background: none; border: none; cursor: pointer; color: #64748b; padding: 0.25rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('unions.apply-loan', $union->id) }}" method="POST" style="display: grid; gap: 1rem;">
            @csrf
            <div class="form-group" style="margin: 0;">
                <label class="form-label">Loan Amount (₦)</label>
                <input type="number" name="amount" required min="10000" step="1000" class="form-input" placeholder="Minimum ₦10,000">
            </div>
            <div style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; color: #92400e;">
                <p style="font-weight: 600; margin: 0 0 0.5rem 0;">Requirements:</p>
                <ul style="margin: 0; padding-left: 1rem;">
                    <li>Minimum {{ $union->member_count }} / {{ $union->tier->union_min_members }} members</li>
                    <li>30% equity contribution required</li>
                </ul>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" onclick="document.getElementById('loanModal').close()" class="btn btn-secondary" style="flex: 1;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;">Submit</button>
            </div>
        </form>
    </div>
</dialog>
@endif

@if($union->foodLoan && in_array($union->foodLoan->status, ['disbursed', 'repaying']))
<dialog id="repaymentModal" style="border: none; border-radius: 16px; padding: 0; max-width: 28rem; width: 100%; background: white; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
    <div style="padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #f1f5f9;">
            <h3 style="font-weight: 700; color: #1e293b; margin: 0; font-size: 1.1rem;">Make Repayment</h3>
            <button onclick="document.getElementById('repaymentModal').close()" style="background: none; border: none; cursor: pointer; color: #64748b; padding: 0.25rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('unions.repayment', $union->id) }}" method="POST" style="display: grid; gap: 1rem;">
            @csrf
            <div class="form-group" style="margin: 0;">
                <label class="form-label">Repayment Amount (₦)</label>
                <input type="number" name="amount" required min="100" step="100" class="form-input" placeholder="Enter amount">
                <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.5rem;">Remaining balance: ₦{{ number_format($union->foodLoan->remaining_balance, 2) }}</p>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" onclick="document.getElementById('repaymentModal').close()" class="btn btn-secondary" style="flex: 1;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;">Make Payment</button>
            </div>
        </form>
    </div>
</dialog>
@endif

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Code copied to clipboard!');
    });
}

function confirmLeave() {
    if (confirm('Are you sure you want to leave this union?')) {
        let url = "{{ route('unions.leave', $union->id) }}";
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(response => {
            if (response.ok) {
                window.location.href = "{{ route('unions.index') }}";
            } else {
                alert('Failed to leave union');
            }
        });
    }
}
</script>
@endsection