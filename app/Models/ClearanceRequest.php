<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClearanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_account_id',
        'user_id',
        'status',
        'fee_amount',
        'fee_paid_at',
        'payment_reference',
        'approved_by',
        'approved_at',
        'admin_notes',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
        'fee_paid_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function userAccount()
    {
        return $this->belongsTo(UserAccount::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(AdminUser::class, 'approved_by');
    }
}
