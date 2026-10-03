<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FoodDistribution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'distribution_number',
        'user_id',
        'tier_id',
        'package_id',
        'distribution_type',
        'package_value',
        'status',
        'approved_at',
        'scheduled_date',
        'packaged_at',
        'dispatched_at',
        'delivered_at',
        'pickup_location_id',
        'delivery_address',
        'delivery_fee',
        'delivery_method',
        'approved_by',
        'packaged_by',
        'admin_notes',
        'user_notes',
        'tracking_number',
        'courier_name',
    ];

    protected $casts = [
        'package_value' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'approved_at' => 'datetime',
        'scheduled_date' => 'date',
        'packaged_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Boot method for auto-generating distribution number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($distribution) {
            if (empty($distribution->distribution_number)) {
                $distribution->distribution_number = self::generateDistributionNumber();
            }
        });
    }

    public static function generateDistributionNumber(): string
    {
        $year = date('Y');
        $lastDistribution = self::whereYear('created_at', $year)
                                ->orderBy('id', 'desc')
                                ->first();
        
        $number = $lastDistribution ? intval(substr($lastDistribution->distribution_number, -5)) + 1 : 1;
        
        return 'DIST-' . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tier()
    {
        return $this->belongsTo(MembershipTier::class, 'tier_id');
    }

    public function package()
    {
        return $this->belongsTo(FoodPackage::class, 'package_id');
    }

    public function items()
    {
        return $this->hasMany(FoodDistributionItem::class, 'distribution_id');
    }

    public function pickupLocation()
    {
        return $this->belongsTo(PickupLocation::class, 'pickup_location_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(AdminUser::class, 'approved_by');
    }

    public function packagedBy()
    {
        return $this->belongsTo(AdminUser::class, 'packaged_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('distribution_type', $type);
    }

    public function scopeScheduledFor($query, $date)
    {
        return $query->whereDate('scheduled_date', $date);
    }

    // Helper Methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function approve(AdminUser $admin, string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $admin->id,
            'admin_notes' => $notes,
        ]);
    }

    public function markAsPackaged(AdminUser $admin): void
    {
        $this->update([
            'status' => 'packaged',
            'packaged_at' => now(),
            'packaged_by' => $admin->id,
        ]);
    }

    public function markAsDispatched(string $trackingNumber = null, string $courierName = null): void
    {
        $this->update([
            'status' => 'dispatched',
            'dispatched_at' => now(),
            'tracking_number' => $trackingNumber,
            'courier_name' => $courierName,
        ]);
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'admin_notes' => $reason,
        ]);
    }

    public function getTotalValue(): float
    {
        return $this->items->sum('total_value');
    }

    public function isDoorstepDelivery(): bool
    {
        return $this->delivery_method === 'doorstep';
    }

    public function isPickup(): bool
    {
        return $this->delivery_method === 'pickup';
    }
}

