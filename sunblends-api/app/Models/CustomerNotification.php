<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerNotification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    
    protected $fillable = [
        'type',
        'customer_id',
        'order_id',
        'status',
        'message',
        'data',
        'read_at'
    ];
    
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];
    
    // Relationship with customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    
    // Relationship with order
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    
    // Scope for unread notifications
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
    
    // Mark as read
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }
    
    // Check if notification is read
    public function isRead()
    {
        return $this->read_at !== null;
    }
}