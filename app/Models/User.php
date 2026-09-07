<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'referral_code',
        'referred_by',
        'current_tier_id',
        'direct_referrals_count',
        'wallet_balance',
        'loyalty_points_balance',
        'membership_expires_at',
        'is_active',
        'address',
        'city',
        'state',
        'country',
        'last_login_at',
        'total_community_count',
        'membership_started_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'membership_expires_at' => 'date',
        'membership_started_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'wallet_balance' => 'decimal:2',
        'loyalty_points_balance' => 'integer',
        'password' => 'hashed',
    ];

    // Boot method for automatic referral code generation
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                $user->referral_code = self::generateUniqueReferralCode();
            }
        });
    }

    // Generate unique 8-character referral code
    public static function generateUniqueReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    // Relationships
    public function currentTier()
    {
        return $this->belongsTo(MembershipTier::class, 'current_tier_id');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function activeReferrals()
    {
        return $this->hasMany(User::class, 'referred_by')->where('is_active', true);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function manualPayments()
    {
        return $this->hasMany(ManualPayment::class);
    }

    public function starterPack()
    {
        return $this->hasOne(StarterPack::class);
    }

    public function distributions()
    {
        return $this->hasMany(FoodDistribution::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function referralStats()
    {
        return $this->hasOne(ReferralStat::class);
    }

    public function referralActivities()
    {
        return $this->hasMany(ReferralActivity::class, 'referrer_id');
    }

    public function renewals()
    {
        return $this->hasMany(MembershipRenewal::class);
    }

    public function tierProgressions()
    {
        return $this->hasMany(TierProgressionLog::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function linkedAccounts()
    {
        return $this->hasMany(UserAccount::class, 'primary_user_id');
    }

    public function accountTransactions()
    {
        return $this->hasMany(AccountTransaction::class, 'primary_user_id');
    }

    public function referredAccounts()
    {
        return $this->hasMany(UserAccount::class, 'referred_by');
    }

    public function unionMembership()
    {
        return $this->hasOne(UnionMember::class)->where('is_active', true);
    }

    // Marketplace relationships
    public function marketplaceVendor()
    {
        return $this->hasOne(\App\Models\Marketplace\Vendor::class, 'user_id');
    }

    public function cartItems()
    {
        return $this->hasMany(\App\Models\Marketplace\CartItem::class, 'user_id');
    }

    public function marketplaceOrders()
    {
        return $this->hasMany(\App\Models\Marketplace\Order::class, 'user_id');
    }

    public function savedProducts()
    {
        return $this->hasMany(\App\Models\Marketplace\SavedProduct::class, 'user_id');
    }

    public function marketplaceWalletTransactions()
    {
        return $this->hasMany(\App\Models\Marketplace\WalletTransaction::class, 'user_id');
    }

    public function loyaltyPoints()
    {
        return $this->hasMany(\App\Models\Marketplace\LoyaltyPoint::class, 'user_id');
    }

    public function recentProductViews()
    {
        return $this->hasMany(\App\Models\Marketplace\RecentView::class, 'user_id');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getReferralLinkAttribute(): string
    {
        return url("/register?ref={$this->referral_code}");
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByTier($query, $tierId)
    {
        return $query->where('current_tier_id', $tierId);
    }

    public function scopeMembershipExpired($query)
    {
        return $query->whereNotNull('membership_expires_at')
            ->where('membership_expires_at', '<', now());
    }

    public function scopeMembershipExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('membership_expires_at')
            ->whereBetween('membership_expires_at', [now(), now()->addDays($days)]);
    }

    // Helper Methods
    public function isMembershipActive(): bool
    {
        // Emerald members have lifetime membership (null expiry)
        if (is_null($this->membership_expires_at)) {
            return true;
        }

        return $this->membership_expires_at->isFuture();
    }

    public function isEmerald(): bool
    {
        return $this->currentTier && $this->currentTier->slug === 'emerald';
    }

    public function canUpgradeTier(): bool
    {
        $nextTier = MembershipTier::where('min_referrals', '>', $this->currentTier->min_referrals)
            ->orderBy('min_referrals', 'asc')
            ->first();

        if (! $nextTier) {
            return false;
        }

        return $this->direct_referrals_count >= $nextTier->min_referrals;
    }

    public function getNextTier()
    {
        return MembershipTier::where('min_referrals', '>', $this->direct_referrals_count)
            ->orderBy('min_referrals', 'asc')
            ->first();
    }

    public function getReferralsNeededForNextTier(): int
    {
        $nextTier = $this->getNextTier();

        if (! $nextTier) {
            return 0;
        }

        return max(0, $nextTier->min_referrals - $this->direct_referrals_count);
    }

    public function incrementReferralCount(): void
    {
        $this->increment('direct_referrals_count');
    }

    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    // Eligibility Helper Methods
    public function hasActiveMembership(): bool
    {
        return $this->isMembershipActive();
    }

    public function isKycVerified(): bool
    {
        return $this->kyc_verified ?? false;
    }

    public function referralCount(): int
    {
        return $this->direct_referrals_count ?? $this->referrals()->count();
    }

    public function activeReferralCount(): int
    {
        return $this->activeReferrals()->count();
    }

    public function getMembershipDurationDays(): int
    {
        if (! $this->created_at) {
            return 0;
        }

        return (int) $this->created_at->diffInDays(now());
    }

    public function getUnion(): ?\App\Models\Union
    {
        return $this->unionMembership?->union;
    }

    public function hasActiveUnion(): bool
    {
        $union = $this->getUnion();

        return $union !== null && $union->status === 'active';
    }

    // Wallet Helpers
    public function hasSufficientWalletBalance(float $amount): bool
    {
        return $this->wallet_balance >= $amount;
    }

    public function deductFromWallet(float $amount, ?string $description = null): bool
    {
        if (! $this->hasSufficientWalletBalance($amount)) {
            return false;
        }

        $this->decrement('wallet_balance', $amount);

        return true;
    }

    public function addToWallet(float $amount): void
    {
        $this->increment('wallet_balance', $amount);
    }
}
