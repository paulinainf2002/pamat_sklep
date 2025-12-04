<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'total',
        'status',

        'delivery_method',
        'delivery_point',
        'delivery_address',
        'shipping_price',
        'payment_method',
        'payment_status',
        'payment_reference',
    ];


    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
