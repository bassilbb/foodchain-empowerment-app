<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $table = 'marketplace_order_status_histories';

    protected $fillable = [
        'order_id', 'status', 'note', 'changed_by_type', 'changed_by_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
