<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class Cart extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'cart';
    protected $primaryKey = 'cart_id';
    protected $fillable = ['dish_id', 'quantity', 'customer_id', 'order_id', 'guest_name'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Cart Log');
    }


    public function dishes()
    {
        return $this->belongsTo(Dish::class, 'dish_id', 'dish_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
