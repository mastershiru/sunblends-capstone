<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use Illuminate\Http\Request;

class MenuApiController extends Controller
{
    /**
     * Get all available dishes for the menu
     */
    public function index()
    {
        // Get all dishes, including only necessary fields
        $dishes = Dish::select(
            'dish_id',
            'dish_name',
            'dish_picture',
            'category',
            'dish_available',
            'dish_rating',
            'Price'
        )
        ->where('dish_available', 1)
        ->get();
        
        // Transform data to match React component's expected format
        $menuItems = $dishes->map(function ($dish) {
            return [
                'id' => $dish->dish_id,
                'Dish_Title' => $dish->dish_name,
                'Dish_Img' => asset($dish->dish_picture),
                'Dish_Type' => $dish->category,
                'Dish_Rating' => $dish->dish_rating ?? 4.5,
                'Dish_Price' => $dish->Price,
                'Dish_Persons' => 1, // Default value if not in your DB
                'isAvailable' => $dish->dish_available
            ];
        });
        
        return response()->json($menuItems);
    }
    
    /**
     * Get a specific dish by ID
     */
    public function show($id)
    {
        $dish = Dish::findOrFail($id);
        
        return response()->json([
            'id' => $dish->dish_id,
            'Dish_Title' => $dish->dish_name,
            'Dish_Img' => asset($dish->dish_picture),
            'Dish_Type' => $dish->category,
            'Dish_Rating' => $dish->dish_rating ?? 4.5,
            'Dish_Price' => $dish->Price,
            'Dish_Persons' => 1, // Default value
            'isAvailable' => $dish->dish_available
        ]);
    }
}