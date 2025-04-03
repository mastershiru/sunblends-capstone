<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes, HasRoles, LogsActivity;

    protected $table = 'employee';
    protected $primaryKey = 'employee_id';
    protected $fillable = [
        'name', 
        'employee_email', 
        'employee_password', 
        'employee_number', 
        'employee_picture', 
        'role_id'
    ];
    
    protected $hidden = [
        'employee_password',
    ];
    
    // IMPORTANT: Tell Spatie to use the web guard
    protected $guard_name = 'web';
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'employee_email', 'employee_number', 'employee_picture', 'role_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Employee Log');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    
    // Make sure the password field is correctly mapped
    public function getAuthPassword()
    {
        return $this->employee_password;
    }
}