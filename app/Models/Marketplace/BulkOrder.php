<?php

namespace App\Models\Marketplace;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BulkOrder extends Model
{
    protected $table = 'marketplace_bulk_orders';

    protected $fillable = [
        'created_by', 'title', 'description', 'status', 'target_date',
        'delivery_type', 'delivery_location', 'discount_percentage',
        'total_required_qty', 'total_contributed_qty', 'total_contributors',
        'total_value', 'completed_at',
    ];

    protected $casts = [
        'target_date' => 'date',
        'discount_percentage' => 'decimal:2',
        'total_value' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public const STATUSES = [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BulkOrderItem::class, 'bulk_order_id');
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(BulkOrderContribution::class, 'bulk_order_id');
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['open', 'in_progress']);
    }

    public function recalculateTotals(): void
    {
        $this->refresh();

        $this->update([
            'total_required_qty' => $this->items->sum('required_quantity'),
            'total_contributed_qty' => $this->items->sum('contributed_quantity'),
            'total_contributors' => $this->contributions()->distinct('user_id')->count('user_id'),
            'total_value' => $this->items->sum(fn ($item) => $item->contributed_quantity * $item->unit_price),
        ]);

        if ($this->total_required_qty > 0 && $this->total_contributed_qty >= $this->total_required_qty) {
            $this->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }
    }
}
