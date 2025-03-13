<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use softDeletes, HasFactory;

    protected $table = 'reservation';
    protected $primaryKey = 'reservation_id';
    protected $fillable = [
        'order_id',
        'customer_id',
        'reservation_date',
        'reservation_time',
        'reservation_type',
        'reservation_status',
        'reservation_people',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }


    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}
