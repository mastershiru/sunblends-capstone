<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

use Illuminate\Foundation\Auth\User as Authenticatable;


class Customer extends Authenticatable
{
    use HasApiTokens,softDeletes, HasRoles, LogsActivity, Notifiable;

    protected $table = 'customer';
    protected $primaryKey = 'customer_id';
    protected $fillable = ['customer_name', 'customer_email', 'customer_password', 'customer_number', 'customer_picture', 'role_id', 'remember_token'];
    protected $hidden = ['customer_password'];
    public $timestamps = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['customer_name', 'customer_email', 'customer_number', 'customer_picture', 'role_id'])
            ->logonlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Customer Log');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class, 'customer_id', 'customer_id');
    }

    public function order()
    {
        return $this->hasMany(Order::class, 'customer_id', 'customer_id');
    }

    public function routeNotificationForBroadcast()
    {
        return 'App.Models.Customer.'.$this->customer_id;
    }
}
