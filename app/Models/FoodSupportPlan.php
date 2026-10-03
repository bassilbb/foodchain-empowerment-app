<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FoodSupportPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'reference',
        'plan_type',
        'plan_value',
        'top_up_percentage',
        'top_up_amount',
        'total_payout_value',
        'total_contributed',
        'status',
        'start_date',
        'maturity_date',
        'withdrawn_at',
    ];

    protected $casts = [
        'plan_value' => 'decimal:2',
        'top_up_percentage' => 'decimal:2',
        'top_up_amount' => 'decimal:2',
        'total_payout_value' => 'decimal:2',
        'total_contributed' => 'decimal:2',
        'start_date' => 'date',
        'maturity_date' => 'date',
        'withdrawn_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($plan) {
            if (empty($plan->reference)) {
                $plan->reference = self::generateUniqueReference();
            }
            if (empty($plan->start_date)) {
                $plan->start_date = now()->format('Y-m-d');
            }
            if (empty($plan->maturity_date)) {
                $plan->maturity_date = $plan->calculateMaturityDate();
            }
        });
    }

    public static function generateUniqueReference(): string
    {
        do {
            $reference = 'FSP-'.strtoupper(Str::random(8));
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }

    public function calculateMaturityDate(): string
    {
        $months = $this->plan_type === 'one_year' ? 12 : 6;

        return now()->addMonths($months)->format('Y-m-d');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contributions()
    {
        return $this->hasMany(FoodSupportPlanContribution::class, 'food_support_plan_id');
    }

    public function addContribution(float $amount, ?string $reference = null, ?string $method = null): FoodSupportPlanContribution
    {
        $contribution = $this->contributions()->create([
            'amount' => $amount,
            'contribution_date' => now()->format('Y-m-d'),
            'payment_reference' => $reference,
            'payment_method' => $method,
        ]);

        $this->increment('total_contributed', $amount);

        if ($this->total_contributed >= $this->plan_value && $this->status === 'active') {
            $this->update(['status' => 'fully_funded']);
        }

        if ($this->status === 'pending') {
            $this->update(['status' => 'active']);
        }

        return $contribution;
    }

    public function isFullyFunded(): bool
    {
        return $this->total_contributed >= $this->plan_value;
    }

    public function isMatured(): bool
    {
        return $this->maturity_date && now()->startOfDay()->gte($this->maturity_date);
    }

    public function processMaturity(): void
    {
        $topUp = $this->total_contributed * ($this->top_up_percentage / 100);
        $totalPayout = $this->total_contributed + $topUp;

        $this->update([
            'top_up_amount' => $topUp,
            'total_payout_value' => $totalPayout,
            'status' => 'matured',
        ]);
    }

    public function processWithdrawal(): void
    {
        if ($this->status !== 'matured') {
            throw new \Exception('Only matured plans can be withdrawn.');
        }

        // Method 1: Create FoodDistribution
        $distribution = FoodDistribution::create([
            'distribution_number' => 'FSP-'.strtoupper(Str::random(8)),
            'user_id' => $this->user_id,
            'distribution_type' => 'support_plan_maturity',
            'status' => 'pending',
            'package_value' => $this->total_payout_value,
            'scheduled_date' => now(),
        ]);

        // Method 2: Create Voucher
        $voucher = Voucher::create([
            'user_id' => $this->user_id,
            'voucher_number' => 'FSPV-'.strtoupper(Str::random(8)),
            'status' => 'pending',
            'issued_at' => now(),
            'expires_at' => now()->addDays(30),
        ]);

        $this->update([
            'status' => 'withdrawn',
            'withdrawn_at' => now(),
        ]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'active', 'fully_funded']);
    }

    public function scopeMatured($query)
    {
        return $query->where('status', 'matured');
    }
}
