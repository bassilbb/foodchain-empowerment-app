<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'union_id',
        'reference',
        'principal_amount',
        'interest_rate',
        'total_repayment_amount',
        'amount_disbursed',
        'amount_repaid',
        'remaining_balance',
        'loan_amount_approved',
        'tenor_months',
        'repayment_frequency_weeks',
        'total_installments',
        'installments_paid',
        'status',
        'disbursement_date',
        'first_repayment_date',
        'final_repayment_date',
        'repayment_schedule',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'total_repayment_amount' => 'decimal:2',
        'amount_disbursed' => 'decimal:2',
        'amount_repaid' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'tenor_months' => 'integer',
        'repayment_frequency_weeks' => 'integer',
        'total_installments' => 'integer',
        'installments_paid' => 'integer',
        'disbursement_date' => 'date',
        'first_repayment_date' => 'date',
        'final_repayment_date' => 'date',
        'repayment_schedule' => 'array',
    ];

    public function union()
    {
        return $this->belongsTo(Union::class, 'union_id');
    }

    public function repayments()
    {
        return $this->hasMany(LoanRepayment::class, 'food_loan_id');
    }

    public function pendingRepayments()
    {
        return $this->hasMany(LoanRepayment::class, 'food_loan_id')
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->orderBy('due_date', 'asc');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeDisbursed($query)
    {
        return $query->where('status', 'disbursed');
    }

    public function scopeRepaying($query)
    {
        return $query->where('status', 'repaying');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isDisbursed(): bool
    {
        return $this->status === 'disbursed';
    }

    public function isRepaying(): bool
    {
        return $this->status === 'repaying';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isDefaulted(): bool
    {
        return $this->status === 'defaulted';
    }

    public function getInstallmentAmount(): float
    {
        if ($this->total_installments <= 0) {
            return 0;
        }

        return $this->total_repayment_amount / $this->total_installments;
    }

    public function getRepaymentProgress(): float
    {
        if ($this->total_repayment_amount <= 0) {
            return 0;
        }

        return ($this->amount_repaid / $this->total_repayment_amount) * 100;
    }

    public function generateRepaymentSchedule(): array
    {
        $schedule = [];
        $installmentAmount = $this->getInstallmentAmount();
        $startDate = $this->first_repayment_date ?? now()->addWeeks($this->repayment_frequency_weeks);

        for ($i = 1; $i <= $this->total_installments; $i++) {
            $schedule[] = [
                'installment_number' => $i,
                'due_date' => $startDate->copy()->addWeeks(($i - 1) * $this->repayment_frequency_weeks)->format('Y-m-d'),
                'amount_due' => $installmentAmount,
                'status' => 'pending',
            ];
        }

        return $schedule;
    }

    public function createRepaymentRecords(): void
    {
        $schedule = $this->repayment_schedule ?? $this->generateRepaymentSchedule();

        foreach ($schedule as $installment) {
            LoanRepayment::create([
                'food_loan_id' => $this->id,
                'union_member_id' => $this->union->leader_id,
                'reference' => $this->generateRepaymentReference($installment['installment_number']),
                'installment_number' => $installment['installment_number'],
                'amount_due' => $installment['amount_due'],
                'due_date' => $installment['due_date'],
                'status' => 'pending',
            ]);
        }
    }

    public function approve(): void
    {
        $this->update(['status' => 'approved']);
    }

    public function disburse(?float $amount = null): void
    {
        $disbursementAmount = $amount ?? $this->principal_amount;

        $this->update([
            'amount_disbursed' => $disbursementAmount,
            'remaining_balance' => $this->total_repayment_amount,
            'status' => 'disbursed',
            'disbursement_date' => now(),
            'first_repayment_date' => now()->addWeeks($this->repayment_frequency_weeks),
            'final_repayment_date' => now()->addMonths($this->tenor_months),
        ]);

        $this->union->disburseLoan($disbursementAmount);

        $this->update(['repayment_schedule' => $this->generateRepaymentSchedule()]);
        $this->createRepaymentRecords();
    }

    public function recordPayment(float $amount, ?int $installmentNumber = null): LoanRepayment
    {
        $repayment = $this->repayments()
            ->where('installment_number', $installmentNumber ?? $this->installments_paid + 1)
            ->first();

        if (! $repayment) {
            throw new \Exception('Repayment record not found');
        }

        $repayment->update([
            'amount_paid' => $amount,
            'status' => 'paid',
            'paid_date' => now(),
        ]);

        $this->increment('amount_repaid', $amount);
        $this->increment('installments_paid', 1);
        $this->update(['remaining_balance' => $this->remaining_balance - $amount]);

        $this->union->recordRepayment($amount);

        if ($this->amount_repaid >= $this->total_repayment_amount) {
            $this->update(['status' => 'completed']);
            $this->union->update(['status' => 'completed']);
        }

        return $repayment;
    }

    public function checkOverdue(): void
    {
        $overdueRepayments = $this->repayments()
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->get();

        foreach ($overdueRepayments as $repayment) {
            $repayment->update(['status' => 'overdue']);
        }

        $overdueCount = $this->repayments()->where('status', 'overdue')->count();

        if ($overdueCount >= 2 && $this->status !== 'defaulted') {
            $this->update(['status' => 'defaulted']);
            $this->union->update(['status' => 'defaulted']);
        }
    }

    private function generateRepaymentReference(int $installmentNumber): string
    {
        return "LOAN-{$this->reference}-{$installmentNumber}";
    }

    public static function generateUniqueReference(): string
    {
        do {
            $reference = 'FL-'.strtoupper(uniqid());
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }
}
