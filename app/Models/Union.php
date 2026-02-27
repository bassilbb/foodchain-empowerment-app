<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Union extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'leader_id',
        'membership_tier_id',
        'member_count',
        'total_equity_contributed',
        'loan_amount_applied',
        'loan_amount_approved',
        'loan_amount_disbursed',
        'loan_amount_repaid',
        'equity_percentage_required',
        'status',
        'loan_start_date',
        'loan_end_date',
    ];

    protected $casts = [
        'member_count' => 'integer',
        'total_equity_contributed' => 'decimal:2',
        'loan_amount_applied' => 'decimal:2',
        'loan_amount_approved' => 'decimal:2',
        'loan_amount_disbursed' => 'decimal:2',
        'loan_amount_repaid' => 'decimal:2',
        'equity_percentage_required' => 'decimal:2',
        'loan_start_date' => 'date',
        'loan_end_date' => 'date',
    ];

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function tier()
    {
        return $this->belongsTo(MembershipTier::class, 'membership_tier_id');
    }

    public function members()
    {
        return $this->hasMany(UnionMember::class, 'union_id');
    }

    public function activeMembers()
    {
        return $this->hasMany(UnionMember::class, 'union_id')->where('is_active', true);
    }

    public function foodLoan()
    {
        return $this->hasOne(FoodLoan::class, 'union_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'completed');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByLeader($query, int $leaderId)
    {
        return $query->where('leader_id', $leaderId);
    }

    public function isForming(): bool
    {
        return $this->status === 'forming';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
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

    public function canAddMember(): bool
    {
        return in_array($this->status, ['forming', 'active']);
    }

    public function canApplyForLoan(): bool
    {
        return $this->status === 'active' && $this->member_count >= $this->tier->union_min_members;
    }

    public function hasMetEquityRequirement(): bool
    {
        return $this->total_equity_contributed >= ($this->loan_amount_applied * $this->equity_percentage_required / 100);
    }

    public function getEquityRequired(): float
    {
        return $this->loan_amount_applied * $this->equity_percentage_required / 100;
    }

    public function getEquityContributedPercentage(): float
    {
        if ($this->loan_amount_applied <= 0) {
            return 0;
        }
        return ($this->total_equity_contributed / $this->loan_amount_applied) * 100;
    }

    public function getLoanBalance(): float
    {
        return $this->loan_amount_disbursed - $this->loan_amount_repaid;
    }

    public function getRepaymentProgress(): float
    {
        if ($this->loan_amount_disbursed <= 0) {
            return 0;
        }
        return ($this->loan_amount_repaid / $this->loan_amount_disbursed) * 100;
    }

    public function addMember(User $user, float $equityContributed = 0): UnionMember
    {
        if (!$this->canAddMember()) {
            throw new \Exception('Cannot add member to union in current status');
        }

        $existingMember = $this->members()->where('user_id', $user->id)->first();
        if ($existingMember) {
            throw new \Exception('User is already a member of this union');
        }

        $unionMember = UnionMember::create([
            'union_id' => $this->id,
            'user_id' => $user->id,
            'equity_contributed' => $equityContributed,
            'is_leader' => false,
        ]);

        $this->increment('member_count', 1);
        $this->increment('total_equity_contributed', $equityContributed);

        if ($this->member_count >= $this->tier->union_min_members && $this->status === 'forming') {
            $this->update(['status' => 'active']);
        }

        return $unionMember;
    }

    public function removeMember(UnionMember $unionMember): void
    {
        if ($unionMember->is_leader) {
            throw new \Exception('Cannot remove the union leader');
        }

        $this->decrement('member_count', 1);
        $this->decrement('total_equity_contributed', $unionMember->equity_contributed);

        $unionMember->delete();

        if ($this->member_count < $this->tier->union_min_members) {
            $this->update(['status' => 'forming']);
        }
    }

    public function applyForLoan(float $amount): void
    {
        if (!$this->canApplyForLoan()) {
            throw new \Exception('Union does not meet requirements to apply for loan');
        }

        $equityRequired = $this->getEquityRequired();

        if ($this->total_equity_contributed < $equityRequired) {
            throw new \Exception("Union must contribute {$equityRequired} in equity (30% of loan amount)");
        }

        $this->update([
            'loan_amount_applied' => $amount,
            'status' => 'active',
        ]);
    }

    public function approveLoan(float $approvedAmount): void
    {
        $this->update([
            'loan_amount_approved' => $approvedAmount,
        ]);
    }

    public function disburseLoan(float $disbursedAmount): void
    {
        $this->update([
            'loan_amount_disbursed' => $disbursedAmount,
            'loan_start_date' => now(),
            'loan_end_date' => now()->addMonths($this->tier->union_loan_tenor_months),
            'status' => 'disbursed',
        ]);
    }

    public function recordRepayment(float $amount): void
    {
        $this->increment('loan_amount_repaid', $amount);

        if ($this->loan_amount_repaid >= $this->loan_amount_disbursed) {
            $this->update(['status' => 'completed']);
        } elseif ($this->status === 'disbursed') {
            $this->update(['status' => 'repaying']);
        }
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = 'UN-' . strtoupper(uniqid());
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
