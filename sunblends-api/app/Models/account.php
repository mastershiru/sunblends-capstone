<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class Account extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'account';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'password', 'email','number'];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
}
