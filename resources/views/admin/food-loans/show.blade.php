@extends('admin.layouts.app')

@section('title', 'Loan Details - ' . $foodLoan->reference)
@section('page-title', 'Loan Details')
@section('header', $foodLoan->reference)
@section('subheader', 'Food Loan Repayment Schedule')

@section('content')
<style>
    .back-btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; color: #475569; text-decoration: none; font-size: 0.875rem; font-weight: 500; margin-bottom: 24px; transition: all 0.2s; }
    .back-btn:hover { background: #f1f5f9; border-color: #10b981; color: #10b981; }
    .detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 24px; }
    .detail-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .detail-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
    .detail-value { font-size: 1.25rem; font-weight: 700; color: #1e293b; }
    .detail-value.emerald { color: #10b981; }
    .progress-section { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .progress-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .progress-title { font-weight: 600; color: #1e293b; }
    .progress-stats { font-size: 0.875rem; color: #64748b; }
    .progress-bar-large { width: 100%; height: 12px; background: #e2e8f0; border-radius: 9999px; overflow: hidden; margin-bottom: 12px; }
    .progress-fill { height: 100%; background: linear-gradient(135deg, #10b981, #059669); border-radius: 9999px; transition: width 0.5s ease; }
    .schedule-section { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); overflow: hidden; }
    .schedule-header { background: linear-gradient(135deg, #10b981, #059669); padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; }
    .schedule-title { color: white; font-weight: 700; font-size: 1.1rem; margin: 0; }
    .schedule-badge { background: rgba(255,255,255,0.2); color: white; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f8fafc; padding: 14px 16px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    td { padding: 16px; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; color: #475569; }
    tr:hover td { background: #fafbfc; }
    .installment-num { width: 36px; height: 36px; background: linear-gradient(135deg, #10b981, #059669); color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; }
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; text-transform: capitalize; }
    .badge-paid { background: #d1fae5; color: #065f46; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-overdue { background: #fee2e2; color: #991b1b; }
    .badge-partial { background: #dbeafe; color: #1e40af; }
    .amount { font-weight: 600; color: #1e293b; font-family: monospace; }
    .date { color: #64748b; font-size: 0.8rem; }
    .action-btn { padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 500; cursor: pointer; border: none; transition: all 0.2s; }
    .btn-approve { background: #10b981; color: white; }
    .btn-approve:hover { background: #059669; }
    .btn-disburse { background: #8b5cf6; color: white; }
    .btn-disburse:hover { background: #7c3aed; }
    .btn-reject { background: #ef4444; color: white; }
    .btn-reject:hover { background: #dc2626; }
    .btn-edit { background: #3b82f6; color: white; }
    .btn-edit:hover { background: #2563eb; }
    .union-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; }
    .modal-overlay.active { display: flex; }
    .modal-content { background: white; border-radius: 20px; padding: 24px; max-width: 400px; width: 90%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
    .modal-title { font-size: 1.1rem; font-weight: 700; color: #1e293b; margin-bottom: 16px; }
    .form-input { width: 100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.875rem; transition: all 0.2s; box-sizing: border-box; }
    .form-input:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }
    .modal-actions { display: flex; gap: 12px; margin-top: 20px; }
    .modal-btn { flex: 1; padding: 10px 16px; border-radius: 10px; font-size: 0.875rem; font-weight: 500; cursor: pointer; transition: all 0.2s; }
    .modal-btn-cancel { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .modal-btn-cancel:hover { background: #e2e8f0; }
    .modal-btn-primary { background: #10b981; color: white; border: none; }
    .modal-btn-primary:hover { background: #059669; }
</style>

<a href="{{ route('admin.food-loans.index') }}" class="back-btn">
    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    Back to Loans
</a>

@if(session('success'))
<div style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); border: 1px solid #10b981; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #065f46; font-weight: 500;">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div style="background: #fee2e2; border: 1px solid #ef4444; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #991b1b; font-weight: 500;">
    {{ session('error') }}
</div>
@endif

<div style="display: flex; gap: 16px; margin-bottom: 24px;">
    @if($foodLoan->status === 'pending')
    <button onclick="openApproveModal()" class="action-btn btn-approve">
        <svg style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        Approve Loan
    </button>
    @endif
    @if($foodLoan->status === 'approved')
    <form action="{{ route('admin.food-loans.disburse', $foodLoan) }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit" class="action-btn btn-disburse"
            data-confirm='{"title":"Disburse Loan?","text":"Are you sure you want to disburse this loan?","btnText":"Disburse","btnClass":"confirm-purple","iconClass":"warning"}'>
            <svg style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Disburse Loan
        </button>
    </form>
    @endif
    @if(in_array($foodLoan->status, ['pending', 'approved']))
    <form action="{{ route('admin.food-loans.reject', $foodLoan) }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit" class="action-btn btn-reject"
            data-confirm='{"title":"Reject Loan?","text":"Are you sure you want to reject this loan?","btnText":"Reject","btnClass":"confirm-danger","iconClass":"danger"}'>
            <svg style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            Reject
        </button>
    </form>
    @endif
</div>

<div class="detail-grid">
    <div class="detail-card">
        <div class="detail-label">Reference</div>
        <div class="detail-value">{{ $foodLoan->reference }}</div>
    </div>
    <div class="detail-card">
        <div class="detail-label">Status</div>
        <div class="detail-value" style="text-transform: capitalize;">{{ $foodLoan->status }}</div>
    </div>
    <div class="detail-card">
        <div class="detail-label">Principal Amount</div>
        <div class="detail-value emerald">₦{{ number_format($foodLoan->principal_amount, 2) }}</div>
    </div>
    <div class="detail-card">
        <div class="detail-label">Approved Amount</div>
        <div class="detail-value emerald">₦{{ number_format($foodLoan->loan_amount_approved, 2) }}</div>
    </div>
    <div class="detail-card">
        <div class="detail-label">Amount Repaid</div>
        <div class="detail-value">₦{{ number_format($foodLoan->amount_repaid, 2) }}</div>
    </div>
    <div class="detail-card">
        <div class="detail-label">Remaining Balance</div>
        <div class="detail-value emerald">₦{{ number_format($foodLoan->remaining_balance, 2) }}</div>
    </div>
    <div class="detail-card">
        <div class="detail-label">Interest Rate</div>
        <div class="detail-value">{{ $foodLoan->interest_rate }}%</div>
    </div>
    <div class="detail-card">
        <div class="detail-label">Total to Repay</div>
        <div class="detail-value">₦{{ number_format($foodLoan->total_repayment_amount, 2) }}</div>
    </div>
</div>

<div class="union-card" style="margin-bottom: 24px;">
    <div class="detail-label">Union Information</div>
    <div style="display: flex; align-items: center; gap: 16px; margin-top: 12px;">
        <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <span style="color: white; font-weight: 700; font-size: 1.25rem;">{{ substr($foodLoan->union->name ?? 'N/A', 0, 1) }}</span>
        </div>
        <div>
            <div style="font-weight: 600; color: #1e293b; font-size: 1rem;">{{ $foodLoan->union->name ?? 'N/A' }}</div>
            <div style="color: #64748b; font-size: 0.875rem;">Code: {{ $foodLoan->union->code ?? 'N/A' }} | Leader: {{ $foodLoan->union->leader->full_name ?? 'N/A' }}</div>
        </div>
    </div>
</div>

@if(in_array($foodLoan->status, ['disbursed', 'repaying']))
<div class="progress-section">
    <div class="progress-header">
        <span class="progress-title">Repayment Progress</span>
        <span class="progress-stats">{{ $foodLoan->installments_paid }} of {{ $foodLoan->total_installments }} installments paid</span>
    </div>
    <div class="progress-bar-large">
        <div style="width: {{ $foodLoan->getRepaymentProgress() }}%;" class="progress-fill"></div>
    </div>
    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
        <span style="color: #64748b;">{{ round($foodLoan->getRepaymentProgress()) }}% Complete</span>
        <span style="color: #10b981; font-weight: 600;">{{ round($foodLoan->getRepaymentProgress()) }}%</span>
    </div>
</div>
@endif

<div class="schedule-section">
    <div class="schedule-header">
        <h3 class="schedule-title">Repayment Schedule</h3>
        <span class="schedule-badge">{{ $repayments->count() }} Installments</span>
    </div>
    
    @if($repayments->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">#</th>
                <th>Due Date</th>
                <th>Amount Due</th>
                <th>Amount Paid</th>
                <th>Status</th>
                <th>Paid Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($repayments as $repayment)
            <tr>
                <td>
                    <div class="installment-num">{{ $repayment->installment_number }}</div>
                </td>
                <td>
                    <div class="date">{{ \Carbon\Carbon::parse($repayment->due_date)->format('M d, Y') }}</div>
                    @if($repayment->isOverdue() && !$repayment->isPaid())
                    <span style="color: #ef4444; font-size: 0.7rem; font-weight: 500;">Overdue</span>
                    @endif
                </td>
                <td class="amount">₦{{ number_format($repayment->amount_due, 2) }}</td>
                <td class="amount">₦{{ number_format($repayment->amount_paid, 2) }}</td>
                <td>
                    <span class="badge badge-{{ $repayment->status }}">{{ $repayment->status }}</span>
                </td>
                <td>
                    @if($repayment->paid_date)
                    <span class="date">{{ \Carbon\Carbon::parse($repayment->paid_date)->format('M d, Y') }}</span>
                    @else
                    <span class="date">-</span>
                    @endif
                </td>
                <td>
                    @if(!$repayment->isPaid())
                    <button type="button" onclick="openEditModal({{ $repayment->id }})" class="action-btn btn-edit" data-amount="{{ $repayment->amount_paid }}" data-status="{{ $repayment->status }}">
                        Edit
                    </button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 60px 20px; color: #94a3b8;">
        <svg style="width: 64px; height: 64px; margin-bottom: 16px; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        <p style="font-size: 1rem; font-weight: 500;">No repayment schedule yet</p>
        <p style="font-size: 0.875rem;">The repayment schedule will be generated when the loan is disbursed.</p>
    </div>
    @endif
</div>

<div id="approveModal" class="modal-overlay">
    <div class="modal-content">
        <h3 class="modal-title">Approve Loan Application</h3>
        <form action="{{ route('admin.food-loans.approve', $foodLoan) }}" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 8px;">Approved Amount (₦)</label>
                <input type="number" name="approved_amount" value="{{ $foodLoan->principal_amount }}" class="form-input" required step="0.01">
            </div>
            <div class="modal-actions">
                <button type="button" onclick="closeApproveModal()" class="modal-btn modal-btn-cancel">Cancel</button>
                <button type="submit" class="modal-btn modal-btn-primary">Approve</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="modal-overlay">
    <div class="modal-content">
        <h3 class="modal-title">Update Repayment</h3>
        <form action="{{ route('admin.food-loans.repayments.update', $repayment ?? 0) }}" method="POST">
            @csrf
            @method('PATCH')
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 8px;">Amount Paid (₦)</label>
                <input type="number" name="amount_paid" id="editAmount" class="form-input" required step="0.01">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 8px;">Status</label>
                <select name="status" id="editStatus" class="form-input" required>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="partial">Partial</option>
                    <option value="overdue">Overdue</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" onclick="closeEditModal()" class="modal-btn modal-btn-cancel">Cancel</button>
                <button type="submit" class="modal-btn modal-btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openApproveModal() {
    document.getElementById('approveModal').classList.add('active');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.remove('active');
}

function openEditModal(id) {
    const btn = event.target;
    document.getElementById('editAmount').value = btn.dataset.amount;
    document.getElementById('editStatus').value = btn.dataset.status;
    document.querySelector('#editModal form').action = '/admin/food-loans/repayments/' + id;
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});
</script>
@endsection
