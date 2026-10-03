<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FoodAssistancePlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'tier_id',
        'reference',
        'total_amount',
        'processing_fee',
        'first_installment_percentage',
        'first_installment_amount',
        'balance_amount',
        'weekly_installment_amount',
        'number_of_installments',
        'installments_paid',
        'total_paid',
        'status',
        'applied_at',
        'approved_at',
        'collected_at',
        'completed_at',
        'admin_notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'first_installment_percentage' => 'decimal:2',
        'first_installment_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'weekly_installment_amount' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'installments_paid' => 'integer',
        'number_of_installments' => 'integer',
        'applied_at' => 'datetime',
        'approved_at' => 'datetime',
        'collected_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($plan) {
            if (empty($plan->reference)) {
                $plan->reference = self::generateUniqueReference();
            }
        });
    }

    public static function generateUniqueReference(): string
    {
        do {
            $reference = 'FAP-'.strtoupper(Str::random(8));
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tier()
    {
        return $this->belongsTo(FoodAssistancePlanTier::class, 'tier_id');
    }

    public function installments()
    {
        return $this->hasMany(FoodAssistancePlanInstallment::class, 'food_assistance_plan_id');
    }

    public function pendingInstallments()
    {
        return $this->hasMany(FoodAssistancePlanInstallment::class, 'food_assistance_plan_id')
            ->where('status', 'pending')
            ->orderBy('installment_number');
    }

    public function paidInstallments()
    {
        return $this->hasMany(FoodAssistancePlanInstallment::class, 'food_assistance_plan_id')
            ->where('status', 'paid');
    }

    public function overdueInstallments()
    {
        return $this->hasMany(FoodAssistancePlanInstallment::class, 'food_assistance_plan_id')
            ->where('status', 'overdue');
    }

    public function generateInstallmentSchedule(): void
    {
        $installments = [];
        $now = now();

        // Installment 0: Processing fee (due immediately)
        $installments[] = [
            'installment_number' => 0,
            'type' => 'processing_fee',
            'amount_due' => $this->processing_fee,
            'due_date' => $now->format('Y-m-d'),
        ];

        // Installment 1: First installment 30% (due after processing fee)
        $installments[] = [
            'installment_number' => 1,
            'type' => 'first_installment',
            'amount_due' => $this->first_installment_amount,
            'due_date' => $now->copy()->addWeek()->format('Y-m-d'),
        ];

        // Installments 2-13: Weekly repayments (12 weeks)
        $weeklyAmount = $this->weekly_installment_amount;
        for ($i = 2; $i <= 13; $i++) {
            $installments[] = [
                'installment_number' => $i,
                'type' => 'weekly_repayment',
                'amount_due' => $weeklyAmount,
                'due_date' => $now->copy()->addWeeks($i - 1)->format('Y-m-d'),
            ];
        }

        foreach ($installments as $data) {
            $this->installments()->create($data);
        }
    }

    public function getNextPendingInstallment(): ?FoodAssistancePlanInstallment
    {
        return $this->pendingInstallments()->first();
    }

    public function getProgress(): float
    {
        $totalInstallments = $this->installments()->count();
        if ($totalInstallments <= 0) {
            return 0;
        }
        $paidCount = $this->installments()->where('status', 'paid')->count();

        return ($paidCount / $totalInstallments) * 100;
    }

    public function isFullyPaid(): bool
    {
        return $this->installments()->where('status', '!=', 'paid')->count() === 0;
    }

    public function payProcessingFee(string $reference, string $method): void
    {
        $installment = $this->installments()->where('installment_number', 0)->first();
        if ($installment) {
            $installment->markAsPaid($reference, $method);
        }
        $this->update([
            'total_paid' => $this->total_paid + $this->processing_fee,
            'status' => 'processing_fee_paid',
        ]);
    }

    public function payFirstInstallment(string $reference, string $method): void
    {
        $installment = $this->installments()->where('installment_number', 1)->first();
        if ($installment) {
            $installment->markAsPaid($reference, $method);
        }
        $this->update([
            'total_paid' => $this->total_paid + $this->first_installment_amount,
            'installments_paid' => $this->installments_paid + 1,
            'status' => 'first_installment_paid',
        ]);
    }

    public function markCollected(): void
    {
        $this->update([
            'status' => 'active',
            'collected_at' => now(),
        ]);
    }

    public function payWeeklyInstallment(string $reference, string $method): void
    {
        $next = $this->getNextPendingInstallment();
        if (! $next || $next->type !== 'weekly_repayment') {
            return;
        }

        $next->markAsPaid($reference, $method);
        $this->increment('installments_paid');
        $this->increment('total_paid', $next->amount_due);

        if ($this->isFullyPaid()) {
            $this->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['processing_fee_paid', 'first_installment_paid', 'active']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
