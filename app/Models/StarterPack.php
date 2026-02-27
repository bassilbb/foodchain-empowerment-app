<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StarterPack extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_id',
        'amount_paid',
        'package_value',
        'status',
        'purchased_at',
        'delivered_at',
        'delivery_notes',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'package_value' => 'decimal:2',
        'purchased_at' => 'date',
        'delivered_at' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'paid', 'packaged', 'dispatched']);
    }

    // Helper Methods
    public function isPaid(): bool
    {
        return in_array($this->status, ['paid', 'packaged', 'dispatched', 'delivered']);
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'purchased_at' => now(),
        ]);
    }

    public function markAsDelivered(string $notes = null): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'delivery_notes' => $notes,
        ]);
    }
}
