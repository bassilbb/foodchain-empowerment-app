<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodSupportPlanContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'food_support_plan_id',
        'amount',
        'contribution_date',
        'payment_reference',
        'payment_method',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'contribution_date' => 'date',
    ];

    public function plan()
    {
        return $this->belongsTo(FoodSupportPlan::class, 'food_support_plan_id');
    }
}
