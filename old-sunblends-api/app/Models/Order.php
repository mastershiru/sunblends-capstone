<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $table = 'order_detail';
    protected $primaryKey = 'order_id';
    
    protected $fillable = [
        'customer_id',
        'guest_name',
        'total_price',
        'payment_method',
        'status_order',
        'type_order',
        'delivery_option', // Changed from is_advance to is_delivery to match migration
        'address',
        'pickup_in',
        'delivered_in'
    ];

    protected $casts = [
        'pickup_in' => 'datetime',
        'delivered_in' => 'datetime',
        'total_price' => 'decimal:2'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class, 'order_id', 'order_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id');
    }
}
