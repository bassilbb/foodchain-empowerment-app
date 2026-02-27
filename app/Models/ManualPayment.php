<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_id',
        'bank_account_id',
        'type',
        'amount',
        'reference',
        'sender_name',
        'sender_account_number',
        'sender_bank_name',
        'transfer_date',
        'proof_image_path',
        'status',
        'admin_notes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transfer_date' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function verifier()
    {
        return $this->belongsTo(AdminUser::class, 'verified_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function verify(int $adminId, string $notes = null): void
    {
        $this->update([
            'status' => 'verified',
            'verified_by' => $adminId,
            'verified_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    public function reject(int $adminId, string $notes): void
    {
        $this->update([
            'status' => 'rejected',
            'verified_by' => $adminId,
            'verified_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    public static function generateReference(): string
    {
        do {
            $reference = 'MP-' . strtoupper(uniqid());
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }
}
