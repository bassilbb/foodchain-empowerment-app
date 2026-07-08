<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodAssistancePlanInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'food_assistance_plan_id',
        'installment_number',
        'type',
        'amount_due',
        'amount_paid',
        'due_date',
        'paid_date',
        'status',
        'payment_reference',
        'payment_method',
    ];

    protected $casts = [
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function plan()
    {
        return $this->belongsTo(FoodAssistancePlan::class, 'food_assistance_plan_id');
    }

    public function markAsPaid(string $reference, string $method): void
    {
        $this->update([
            'amount_paid' => $this->amount_due,
            'paid_date' => now(),
            'status' => 'paid',
            'payment_reference' => $reference,
            'payment_method' => $method,
        ]);
    }
}
