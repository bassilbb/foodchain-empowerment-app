<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'min_referrals',
        'annual_fee',
        'annual_fee_kobo',
        'food_package_value',
        'distribution_frequency',
        'benefits',
        'extended_benefits',
        'sort_order',
        'is_active',
        'instant_food_pack_price',
        'instant_food_pack_price_kobo',
        'community_min_members',
        'community_max_members',
        'free_food_pack_type',
        'has_delivery',
        'delivery_fee',
        'delivery_subsidy_percentage',
        'free_delivery',
        'target_audience',
        'community_category',
        'union_min_members',
        'union_equity_percentage',
        'union_loan_tenor_months',
        'union_repayment_frequency_weeks',
    ];

    protected $casts = [
        'min_referrals' => 'integer',
        'annual_fee' => 'decimal:2',
        'annual_fee_kobo' => 'decimal:2',
        'food_package_value' => 'decimal:2',
        'instant_food_pack_price' => 'decimal:2',
        'instant_food_pack_price_kobo' => 'decimal:2',
        'community_min_members' => 'integer',
        'community_max_members' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'benefits' => 'array',
        'extended_benefits' => 'array',
        'has_delivery' => 'boolean',
        'delivery_fee' => 'decimal:2',
        'delivery_subsidy_percentage' => 'decimal:2',
        'free_delivery' => 'boolean',
        'union_min_members' => 'integer',
        'union_equity_percentage' => 'decimal:2',
        'union_loan_tenor_months' => 'integer',
        'union_repayment_frequency_weeks' => 'integer',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'current_tier_id');
    }

    public function foodPackages()
    {
        return $this->hasMany(FoodPackage::class, 'tier_id');
    }

    public function distributions()
    {
        return $this->hasMany(FoodDistribution::class, 'tier_id');
    }

    public function unions()
    {
        return $this->hasMany(Union::class, 'membership_tier_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    public function isBronze(): bool
    {
        return $this->slug === 'bronze';
    }

    public function isSilver(): bool
    {
        return $this->slug === 'silver';
    }

    public function isGold(): bool
    {
        return $this->slug === 'gold';
    }

    public function isEmerald(): bool
    {
        return $this->slug === 'emerald';
    }

    public function hasFreeMembership(): bool
    {
        return $this->annual_fee == 0;
    }

    public function hasFreeDelivery(): bool
    {
        return $this->free_delivery === true;
    }

    public function hasSubsidizedDelivery(): bool
    {
        return $this->delivery_subsidy_percentage > 0;
    }

    public function getDeliverySubsidyAmount(float $baseFee): float
    {
        return $baseFee * ($this->delivery_subsidy_percentage / 100);
    }

    public function getDeliveryFee(): float
    {
        if ($this->hasFreeDelivery()) {
            return 0;
        }
        return (float) $this->delivery_fee;
    }

    public function calculateDeliveryFee(float $baseFee): float
    {
        if ($this->hasFreeDelivery()) {
            return 0;
        }

        if ($this->hasSubsidizedDelivery()) {
            return max(0, $baseFee - $this->getDeliverySubsidyAmount($baseFee));
        }

        return $baseFee;
    }

    public function getDefaultPackage()
    {
        return $this->foodPackages()->where('is_default', true)->first();
    }

    public function qualifiesForFreeFoodPack(int $communityCount): bool
    {
        return $communityCount >= $this->community_min_members 
            && $communityCount <= $this->community_max_members;
    }

    public function getEligibleFoodPackType(int $communityCount): ?string
    {
        if (!$this->qualifiesForFreeFoodPack($communityCount)) {
            return null;
        }

        return $this->free_food_pack_type;
    }

    public function canCreateUnion(): bool
    {
        return true;
    }

    public function getUnionRequirements(): array
    {
        return [
            'min_members' => $this->union_min_members,
            'equity_percentage' => $this->union_equity_percentage,
            'loan_tenor_months' => $this->union_loan_tenor_months,
            'repayment_frequency_weeks' => $this->union_repayment_frequency_weeks,
        ];
    }

    public function getTargetAudiences(): array
    {
        return explode(',', $this->target_audience);
    }

    public static function getGradeByCommunityCount(int $communityCount): ?self
    {
        return static::where('community_min_members', '<=', $communityCount)
            ->where('community_max_members', '>=', $communityCount)
            ->active()
            ->first();
    }

    public static function getDefaultGrades(): array
    {
        return [
            'bronze' => [
                'name' => 'Bronze',
                'slug' => 'bronze',
                'min_referrals' => 0,
                'annual_fee' => 5000.00,
                'annual_fee_kobo' => 500000,
                'instant_food_pack_price' => 20000.00,
                'instant_food_pack_price_kobo' => 2000000,
                'community_min_members' => 10,
                'community_max_members' => 40,
                'free_food_pack_type' => 'mini',
                'delivery_fee' => 1500.00,
                'delivery_subsidy_percentage' => 0,
                'free_delivery' => false,
                'target_audience' => 'low_income_households,micro_business_owners,working_class_individuals,students',
                'community_category' => 'all_categories',
                'union_min_members' => 10,
                'union_equity_percentage' => 30,
                'union_loan_tenor_months' => 3,
                'union_repayment_frequency_weeks' => 2,
            ],
            'silver' => [
                'name' => 'Silver',
                'slug' => 'silver',
                'min_referrals' => 10,
                'annual_fee' => 5000.00,
                'annual_fee_kobo' => 500000,
                'instant_food_pack_price' => 50000.00,
                'instant_food_pack_price_kobo' => 5000000,
                'community_min_members' => 50,
                'community_max_members' => 90,
                'free_food_pack_type' => 'average',
                'delivery_fee' => 1500.00,
                'delivery_subsidy_percentage' => 0,
                'free_delivery' => false,
                'target_audience' => 'low_income_households,micro_business_owners,working_class_individuals,students',
                'community_category' => 'all_categories',
                'union_min_members' => 10,
                'union_equity_percentage' => 30,
                'union_loan_tenor_months' => 3,
                'union_repayment_frequency_weeks' => 2,
            ],
            'gold' => [
                'name' => 'Gold',
                'slug' => 'gold',
                'min_referrals' => 100,
                'annual_fee' => 5000.00,
                'annual_fee_kobo' => 500000,
                'instant_food_pack_price' => 100000.00,
                'instant_food_pack_price_kobo' => 10000000,
                'community_min_members' => 100,
                'community_max_members' => 499,
                'free_food_pack_type' => 'standard',
                'delivery_fee' => 1500.00,
                'delivery_subsidy_percentage' => 50,
                'free_delivery' => false,
                'target_audience' => 'low_income_households,micro_business_owners,working_class_individuals,students',
                'community_category' => 'all_categories',
                'union_min_members' => 10,
                'union_equity_percentage' => 30,
                'union_loan_tenor_months' => 3,
                'union_repayment_frequency_weeks' => 2,
            ],
            'emerald' => [
                'name' => 'Emerald',
                'slug' => 'emerald',
                'min_referrals' => 500,
                'annual_fee' => 0.00,
                'annual_fee_kobo' => 0,
                'instant_food_pack_price' => 200000.00,
                'instant_food_pack_price_kobo' => 20000000,
                'community_min_members' => 500,
                'community_max_members' => 1000,
                'free_food_pack_type' => 'premium',
                'delivery_fee' => 0,
                'delivery_subsidy_percentage' => 100,
                'free_delivery' => true,
                'target_audience' => 'low_income_households,micro_business_owners,working_class_individuals,students',
                'community_category' => 'all_categories',
                'union_min_members' => 10,
                'union_equity_percentage' => 30,
                'union_loan_tenor_months' => 3,
                'union_repayment_frequency_weeks' => 2,
            ],
        ];
    }
}
