<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuarterlyBenefitEligibility extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'membership_tier_id',
        'qualification_type',
        'food_pack_type',
        'community_count_at_qualification',
        'is_qualified',
        'benefit_claimed',
        'qualification_date',
        'claim_date',
        'quarter',
        'year',
    ];

    protected $casts = [
        'community_count_at_qualification' => 'integer',
        'is_qualified' => 'boolean',
        'benefit_claimed' => 'boolean',
        'qualification_date' => 'date',
        'claim_date' => 'date',
        'quarter' => 'integer',
        'year' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tier()
    {
        return $this->belongsTo(MembershipTier::class, 'membership_tier_id');
    }

    public function scopeQualified($query)
    {
        return $query->where('is_qualified', true);
    }

    public function scopeUnclaimed($query)
    {
        return $query->where('is_qualified', true)->where('benefit_claimed', false);
    }

    public function scopeClaimed($query)
    {
        return $query->where('benefit_claimed', true);
    }

    public function scopeForQuarter($query, int $quarter, int $year)
    {
        return $query->where('quarter', $quarter)->where('year', $year);
    }

    public function scopeCurrentQuarter($query)
    {
        return $query->forQuarter(now()->quarter, now()->year);
    }

    public function isQualified(): bool
    {
        return $this->is_qualified === true;
    }

    public function isClaimed(): bool
    {
        return $this->benefit_claimed === true;
    }

    public function canClaim(): bool
    {
        return $this->is_qualified && !$this->benefit_claimed;
    }

    public function claim(): void
    {
        if (!$this->canClaim()) {
            throw new \Exception('Benefit cannot be claimed');
        }

        $this->update([
            'benefit_claimed' => true,
            'claim_date' => now(),
        ]);
    }

    public static function getFoodPackTypes(): array
    {
        return [
            'mini' => [
                'name' => 'Mini Free Food Pack',
                'description' => 'Quarterly food pack for Bronze members (10-40 community members)',
                'estimated_value' => 5000,
            ],
            'average' => [
                'name' => 'Average Free Food Pack',
                'description' => 'Quarterly food pack for Silver members (50-90 community members)',
                'estimated_value' => 10000,
            ],
            'standard' => [
                'name' => 'Standard Free Food Pack',
                'description' => 'Quarterly food pack for Gold members (100-499 community members)',
                'estimated_value' => 25000,
            ],
            'premium' => [
                'name' => 'Premium Free Food Pack',
                'description' => 'Quarterly food pack for Emerald members (500-1000 community members)',
                'estimated_value' => 50000,
            ],
        ];
    }

    public function getFoodPackDetails(): array
    {
        return self::getFoodPackTypes()[$this->food_pack_type] ?? [];
    }
}
