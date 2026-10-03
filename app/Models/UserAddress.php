<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'full_name',
        'phone',
        'address',
        'state',
        'lga',
        'city',
        'landmark',
        'delivery_instructions',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Addresses belonging to a specific customer. */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Make this the customer's only default address.
     *
     * Called inside a transaction by the controller so a customer can never end
     * up with two defaults (which would make checkout ambiguous).
     */
    public function makeDefault(): void
    {
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    /** Single-line rendering used in checkout summaries and order snapshots. */
    public function toDeliverySnapshot(): array
    {
        return [
            'delivery_address' => trim(collect([$this->address, $this->landmark])->filter()->implode(', ')),
            'delivery_city' => $this->city,
            'delivery_state' => $this->state,
            'delivery_lga' => $this->lga,
            'delivery_notes' => $this->delivery_instructions,
        ];
    }

    public function getSummaryAttribute(): string
    {
        return collect([$this->address, $this->city, $this->lga, $this->state])
            ->filter()
            ->implode(', ');
    }
}
