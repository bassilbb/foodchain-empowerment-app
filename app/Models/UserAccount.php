<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'primary_user_id',
        'account_name',
        'selected_plan_id',
        'savings_plan_option_id',
        'referred_by',
        'available_balance',
        'total_contributions',
        'defaults',
        'last_deduction_at',
        'current_tariff_weeks',
        'weekly_contribution_amount',
        'status',
    ];

    protected $casts = [
        'available_balance' => 'decimal:2',
        'total_contributions' => 'decimal:2',
        'weekly_contribution_amount' => 'decimal:2',
        'last_deduction_at' => 'datetime',
    ];

    public function primaryUser()
    {
        return $this->belongsTo(User::class, 'primary_user_id');
    }

    public function plan()
    {
        return $this->belongsTo(ContributionPlan::class, 'selected_plan_id');
    }

    public function savingsPlanOption()
    {
        return $this->belongsTo(SavingsPlanOption::class);
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class, 'user_account_id');
    }

    public function clearanceRequest()
    {
        return $this->hasOne(ClearanceRequest::class, 'user_account_id');
    }

    public function isMatured(): bool
    {
        return $this->current_tariff_weeks >= $this->plan_duration_weeks && $this->plan_duration_weeks > 0;
    }

    public function getPlanNameAttribute(): string
    {
        if ($this->savings_plan_option_id && $this->relationLoaded('savingsPlanOption') && $this->savingsPlanOption) {
            return $this->savingsPlanOption->plan->name ?? 'N/A';
        }

        return $this->plan?->name ?? 'N/A';
    }

    public function getPlanDurationWeeksAttribute(): int
    {
        if ($this->savings_plan_option_id) {
            if ($this->relationLoaded('savingsPlanOption') && $this->savingsPlanOption) {
                return $this->savingsPlanOption->duration_weeks;
            }

            return $this->savingsPlanOption()->value('duration_weeks') ?? 0;
        }

        return $this->plan?->duration_weeks ?? 0;
    }
}
