<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class Dish extends Model
{
    use HasFactory, SoftDeletes, logsActivity;

    protected $table = 'dish';
    protected $primaryKey = 'dish_id';
    protected $fillable = ['dish_name', 'dish_picture', 'category', 'dish_available','dish_rating','Price'];

    public function getActivitylogOptions(): logOptions
    {
        return logOptions::defaults()
        ->logOnly($this->fillable)
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->useLogName('Dish Log');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class, 'dish_id', 'dish_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'dish_id', 'dish_id');
    }

    // Add a method to calculate average rating
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }
    
}
