<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'guard_name', 'description'];

    public static function boot()
    {
        parent::boot();
        
        // Sync with Spatie roles
        static::created(function ($role) {
            SpatieRole::create(['name' => $role->name, 'guard_name' => 'web']);
        });
        
        static::updated(function ($role) {
            $spatieRole = SpatieRole::findByName($role->name);
            if ($spatieRole) {
                $spatieRole->name = $role->name;
                $spatieRole->save();
            }
        });
        
        static::deleted(function ($role) {
            $spatieRole = SpatieRole::findByName($role->name);
            if ($spatieRole) {
                $spatieRole->delete();
            }
        });
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'role_id');
    }
}