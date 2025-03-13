<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class Admin extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'admin_acc'; // Table name in the database

    protected $primaryKey = 'Admin_ID'; // Primary key of the table

    protected $fillable = ['Admin_Name', 'Admin_Password']; // Allow mass assignment

    public $timestamps = false; // Set to true if you have created_at and updated_at columns
}
