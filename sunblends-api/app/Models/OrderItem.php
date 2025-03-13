<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';
    protected $primaryKey = 'Item_ID';
    public $timestamps = true;

    protected $fillable = [
        'Order_ID',
        'Customer_Email',
        'Customer_Name',
        'Customer_Number',
        'Item_Img',
        'Item_Title',
        'Item_Quantity',
        'Item_Price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'Order_ID', 'Order_ID');
    }
}
