<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu'; // Define the table name

    protected $fillable = [
        'Dish_ID',
        'Dish_Img',
        'Dish_Title',
        'Dish_Type',
        'Dish_Persons',
        'Dish_Price',
        'isAvailable',
        'Dish_Rating'
    ];
    protected $appends = ['Dish_Img_URL'];

    public function getDishImgUrlAttribute()
{
    return URL::to('/storage/' . $this->Dish_Img);
}

}
