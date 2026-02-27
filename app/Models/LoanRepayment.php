<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRepayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'food_loan_id',
        'union_member_id',
        'reference',
        'installment_number',
        'amount_due',
        'amount_paid',
        'principal_portion',
        'interest_portion',
        'due_date',
        'paid_date',
        'status',
        'payment_details',
    ];

    protected $casts = [
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'principal_portion' => 'decimal:2',
        'interest_portion' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
        'payment_details' => 'array',
    ];

    public function foodLoan()
    {
        return $this->belongsTo(FoodLoan::class, 'food_loan_id');
    }

    public function unionMember()
    {
        return $this->belongsTo(UnionMember::class, 'union_member_id');
    }

    public function user()
    {
        return $this->unionMember->user();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPartial(): bool
    {
        return $this->status === 'partial';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue' || ($this->isPending() && $this->due_date->isPast());
    }

    public function getAmountRemaining(): float
    {
        return $this->amount_due - $this->amount_paid;
    }

    public function getPaymentProgress(): float
    {
        if ($this->amount_due <= 0) {
            return 0;
        }
        return ($this->amount_paid / $this->amount_due) * 100;
    }

    public function markAsPaid(array $paymentDetails = []): void
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => now(),
            'payment_details' => $paymentDetails,
        ]);
    }

    public function markAsPartial(float $amount): void
    {
        $this->update([
            'amount_paid' => $this->amount_paid + $amount,
            'status' => 'partial',
        ]);

        if ($this->amount_paid >= $this->amount_due) {
            $this->update(['status' => 'paid', 'paid_date' => now()]);
        }
    }

    public function markAsOverdue(): void
    {
        $this->update(['status' => 'overdue']);
    }
}
