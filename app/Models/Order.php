<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'items', 'order_date', 'status', 'total_amount', 'address', 'payment_method', 'payment_status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}