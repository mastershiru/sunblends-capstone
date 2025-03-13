<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Employee extends Authenticatable
{
    use HasApiTokens, softDeletes, HasRoles, LogsActivity;

    protected $table = 'employee';
    protected $primaryKey = 'employee_id';
    protected $fillable = ['name', 'employee_password', 'employee_email', 'employee_number', 'employee_picture', 'role_id'];
    protected $hidden = ['employee_password'];
    public $timestamps = true;


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'employee_email', 'employee_number', 'employee_picture', 'role_id'])
            ->logonlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Employee Log');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
