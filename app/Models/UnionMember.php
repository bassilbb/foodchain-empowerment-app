<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnionMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'union_id',
        'user_id',
        'equity_contributed',
        'equity_percentage',
        'is_leader',
        'is_active',
        'joined_at',
    ];

    protected $casts = [
        'equity_contributed' => 'decimal:2',
        'equity_percentage' => 'decimal:2',
        'is_leader' => 'boolean',
        'is_active' => 'boolean',
        'joined_at' => 'date',
    ];

    public function union()
    {
        return $this->belongsTo(Union::class, 'union_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function loanRepayments()
    {
        return $this->hasMany(LoanRepayment::class, 'union_member_id');
    }

    public function pendingRepayments()
    {
        return $this->hasMany(LoanRepayment::class, 'union_member_id')
            ->whereIn('status', ['pending', 'partial', 'overdue']);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLeaders($query)
    {
        return $query->where('is_leader', true);
    }

    public function isLeader(): bool
    {
        return $this->is_leader === true;
    }

    public function canMakeRepayment(): bool
    {
        return $this->union->isRepaying() || $this->union->isDisbursed();
    }

    public function getTotalRepaid(): float
    {
        return $this->loanRepayments()
            ->where('status', 'paid')
            ->sum('amount_paid');
    }

    public function getPendingRepaymentAmount(): float
    {
        return $this->pendingRepayments()->sum('amount_due');
    }

    public function updateEquityContribution(float $amount): void
    {
        $this->increment('equity_contributed', $amount);
        $this->union->increment('total_equity_contributed', $amount);

        if ($this->union->loan_amount_applied > 0) {
            $newPercentage = ($this->union->total_equity_contributed / $this->union->loan_amount_applied) * 100;
            $this->update(['equity_percentage' => $newPercentage]);
        }
    }
}
