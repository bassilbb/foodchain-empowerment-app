<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountTransaction extends Model
{
    protected $fillable = [
        'primary_user_id',
        'user_account_id',
        'amount',
        'type',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function primaryUser()
    {
        return $this->belongsTo(User::class, 'primary_user_id');
    }

    public function account()
    {
        return $this->belongsTo(UserAccount::class, 'user_account_id');
    }
}
