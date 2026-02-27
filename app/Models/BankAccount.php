<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_name',
        'account_number',
        'account_name',
        'account_type',
        'instructions',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function manualPayments()
    {
        return $this->hasMany(ManualPayment::class, 'bank_account_id');
    }

    public function isDefault(): bool
    {
        return $this->is_default === true;
    }

    public static function getActiveForPayment(): ?self
    {
        return static::active()->default()->first() ?? static::active()->first();
    }

    public static function getAllActive(): array
    {
        return static::active()->orderByDesc('is_default')->get()->toArray();
    }
}
