<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Dish;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    /**
     * Rate a dish from a completed order
     */
    public function rateDish(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'dish_id' => 'required|exists:dish,dish_id',
            'order_id' => 'required|exists:order_detail,order_id',
            'rating' => 'required|integer|min:1|max:5',
            
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get the authenticated customer
        $customer = $request->user();
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Verify that this dish was part of this order
        $order = Order::with('cart')->find($request->order_id);
        
        // First check if this order belongs to the customer
        if ($order->customer_id != $customer->customer_id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only rate dishes from your own orders'
            ], 403);
        }

        // Then check if the order is completed
        if ($order->status_order != 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'You can only rate dishes from completed orders'
            ], 400);
        }

        // Check if this dish is part of the order
        $dishInOrder = false;
        foreach ($order->cart as $cartItem) {
            if ($cartItem->dish_id == $request->dish_id) {
                $dishInOrder = true;
                break;
            }
        }

        if (!$dishInOrder) {
            return response()->json([
                'success' => false,
                'message' => 'This dish was not part of this order'
            ], 400);
        }

        // Check if the customer has already rated this dish for this order
        $existingRating = Rating::where([
            'customer_id' => $customer->customer_id,
            'dish_id' => $request->dish_id,
            'order_id' => $request->order_id
        ])->first();

        if ($existingRating) {
            // Update existing rating
            $existingRating->rating = $request->rating;
            if ($request->has('review')) {
                $existingRating->review = $request->review;
            }
            $existingRating->save();
            
            // Update the dish's rating in the dish table
            $this->updateDishRating($request->dish_id);
            
            return response()->json([
                'success' => true,
                'message' => 'Rating updated successfully',
                'rating' => $existingRating
            ]);
        }

        // Create a new rating
        $rating = new Rating();
        $rating->dish_id = $request->dish_id;
        $rating->customer_id = $customer->customer_id;
        $rating->order_id = $request->order_id;
        $rating->rating = $request->rating;
        $rating->review = $request->review ?? null;
        $rating->save();

        // Update the dish's rating in the dish table
        $this->updateDishRating($request->dish_id);

        return response()->json([
            'success' => true,
            'message' => 'Rating submitted successfully',
            'rating' => $rating
        ]);
    }

    /**
     * Get all ratings for a dish
     */
    public function getDishRatings($id)
    {
        $dish = Dish::find($id);
        
        if (!$dish) {
            return response()->json([
                'success' => false,
                'message' => 'Dish not found'
            ], 404);
        }

        $ratings = Rating::where('dish_id', $id)
            ->with(['customer:customer_id,customer_name,customer_picture'])
            ->orderBy('created_at', 'desc')
            ->get();

        $averageRating = $ratings->avg('rating') ?? 0;
        $ratingsCount = $ratings->count();

        // Group ratings by stars (1-5)
        $ratingBreakdown = [
            '5' => $ratings->where('rating', 5)->count(),
            '4' => $ratings->where('rating', 4)->count(),
            '3' => $ratings->where('rating', 3)->count(),
            '2' => $ratings->where('rating', 2)->count(),
            '1' => $ratings->where('rating', 1)->count(),
        ];

        return response()->json([
            'success' => true,
            'dish_id' => $id,
            'dish_name' => $dish->dish_name,
            'average_rating' => round($averageRating, 1),
            'ratings_count' => $ratingsCount,
            'rating_breakdown' => $ratingBreakdown,
            'ratings' => $ratings
        ]);
    }

    /**
     * Update the dish's rating in the dish table
     */
    private function updateDishRating($dishId)
    {
        $dish = Dish::find($dishId);
        
        if (!$dish) {
            return;
        }

        $averageRating = Rating::where('dish_id', $dishId)->avg('rating') ?? 0;
        
        // Update the dish_rating field with the new average
        $dish->dish_rating = round($averageRating, 1);
        $dish->save();
    }

    public function checkRating(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dish_id' => 'required|exists:dish,dish_id',
            'order_id' => 'required|exists:order_detail,order_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = $request->user();
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $rating = Rating::where([
            'customer_id' => $customer->customer_id,
            'dish_id' => $request->dish_id,
            'order_id' => $request->order_id
        ])->first();

        return response()->json([
            'success' => true,
            'has_rating' => (bool) $rating,
            'rating' => $rating ? $rating->rating : null
        ]);
    }

    /**
     * Get featured menu items - top ordered and highest rated
     */
    public function getFeaturedMenuItems()
    {
        try {
            // Get the top 6 most ordered dishes
            $topOrderedDishes = \DB::table('cart')
                ->select('dish_id', \DB::raw('COUNT(*) as order_count'))
                ->groupBy('dish_id')
                ->orderBy('order_count', 'desc')
                ->limit(6)
                ->get()
                ->pluck('dish_id')
                ->toArray();
            
            // Get the dishes with at least 50 ratings, ordered by highest rating
            $highestRatedDishes = \DB::table('ratings')
                ->select('dish_id', \DB::raw('AVG(rating) as avg_rating'), \DB::raw('COUNT(*) as ratings_count'))
                ->groupBy('dish_id')
                ->having('ratings_count', '>=', 50)
                ->orderBy('avg_rating', 'desc')
                ->get()
                ->pluck('dish_id')
                ->toArray();
            
            // Combine both arrays, removing duplicates
            $featuredDishIds = array_unique(array_merge($topOrderedDishes, $highestRatedDishes));
            
            // Get the actual dish data
            $topOrderedDishesData = Dish::whereIn('dish_id', $topOrderedDishes)
                ->where('dish_available', 'yes')
                ->withCount('ratings')
                ->get();
            
            $highestRatedDishesData = Dish::whereIn('dish_id', $highestRatedDishes)
                ->where('dish_available', 'yes')
                ->withCount('ratings')
                ->get();
            
            // Format the response data
            $formattedTopOrdered = $topOrderedDishesData->map(function ($dish) {
                return [
                    'id' => $dish->dish_id,
                    'Dish_Title' => $dish->dish_name,
                    'Dish_Img' => $dish->dish_picture,
                    'Dish_Price' => floatval($dish->Price),
                    'Dish_Type' => $dish->category,
                    'Dish_Rating' => floatval($dish->dish_rating),
                    'ratings_count' => $dish->ratings_count,
                    'Dish_Available' => $dish->dish_available,
                    'feature_type' => 'most_ordered'
                ];
            });
            
            $formattedHighestRated = $highestRatedDishesData->map(function ($dish) {
                return [
                    'id' => $dish->dish_id,
                    'Dish_Title' => $dish->dish_name,
                    'Dish_Img' => $dish->dish_picture,
                    'Dish_Price' => floatval($dish->Price),
                    'Dish_Type' => $dish->category,
                    'Dish_Rating' => floatval($dish->dish_rating),
                    'ratings_count' => $dish->ratings_count,
                    'Dish_Available' => $dish->dish_available,
                    'feature_type' => 'highest_rated'
                ];
            });
            
            // Combine all dishes
            $allFeaturedDishes = $formattedTopOrdered->merge($formattedHighestRated);

            // Add fallback for when we don't have enough highly-rated dishes
            if ($allFeaturedDishes->count() < 12) {
                $regularDishes = Dish::where('dish_available', 'yes')
                    ->whereNotIn('dish_id', $featuredDishIds)
                    ->withCount('ratings')
                    ->orderBy('dish_rating', 'desc')
                    ->limit(12 - $allFeaturedDishes->count())
                    ->get()
                    ->map(function ($dish) {
                        return [
                            'id' => $dish->dish_id,
                            'Dish_Title' => $dish->dish_name,
                            'Dish_Img' => $dish->dish_picture,
                            'Dish_Price' => floatval($dish->Price),
                            'Dish_Type' => $dish->category,
                            'Dish_Rating' => floatval($dish->dish_rating),
                            'ratings_count' => $dish->ratings_count,
                            'Dish_Available' => $dish->dish_available,
                            'feature_type' => 'regular'
                        ];
                    });
                
                $allFeaturedDishes = $allFeaturedDishes->merge($regularDishes);
            }
            
            return response()->json([
                'success' => true,
                'most_ordered' => $formattedTopOrdered->values(),
                'highest_rated' => $formattedHighestRated->values(),
                'all_featured' => $allFeaturedDishes->values()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching featured menu items: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load featured menu items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get flexible featured menu items with adjustable parameters
     */
    public function getSmartFeaturedMenu(Request $request)
    {
        try {
            // Parse parameters with defaults
            $topOrderedLimit = $request->input('top_ordered_limit', 6);
            $minRatingsCount = $request->input('min_ratings', 50);
            $totalItemsLimit = $request->input('total_items', 12);
            
            // Get the top most ordered dishes
            $topOrderedDishes = \DB::table('cart')
                ->select('dish_id', \DB::raw('COUNT(*) as order_count'))
                ->groupBy('dish_id')
                ->orderBy('order_count', 'desc')
                ->limit($topOrderedLimit)
                ->get();
            
            $topOrderedIds = $topOrderedDishes->pluck('dish_id')->toArray();
            
            // Get rating data for all dishes
            $ratingsData = \DB::table('ratings')
                ->select('dish_id', \DB::raw('AVG(rating) as avg_rating'), \DB::raw('COUNT(*) as ratings_count'))
                ->groupBy('dish_id')
                ->get();
            
            // Get all available dishes
            $availableDishes = Dish::where('dish_available', 'yes')
                ->withCount('ratings')
                ->get();
            
            // Prepare featured dishes collections
            $mostOrdered = collect();
            $highestRated = collect();
            $regular = collect();
            
            // Process the top ordered dishes first
            foreach ($topOrderedIds as $dishId) {
                $dish = $availableDishes->firstWhere('dish_id', $dishId);
                if ($dish) {
                    $orderCount = $topOrderedDishes->firstWhere('dish_id', $dishId)->order_count;
                    $mostOrdered->push([
                        'id' => $dish->dish_id,
                        'Dish_Title' => $dish->dish_name,
                        'Dish_Img' => $dish->dish_picture,
                        'Dish_Price' => floatval($dish->Price),
                        'Dish_Type' => $dish->category,
                        'Dish_Rating' => floatval($dish->dish_rating),
                        'ratings_count' => $dish->ratings_count,
                        'order_count' => $orderCount,
                        'Dish_Available' => $dish->dish_available,
                        'feature_type' => 'most_ordered'
                    ]);
                }
            }
            
            // Process dishes with high ratings, excluding those already in mostOrdered
            $processedIds = $topOrderedIds;
            
            foreach ($ratingsData as $ratingData) {
                if (in_array($ratingData->dish_id, $processedIds)) {
                    continue; // Skip dishes we've already processed
                }
                
                if ($ratingData->ratings_count >= $minRatingsCount) {
                    $dish = $availableDishes->firstWhere('dish_id', $ratingData->dish_id);
                    if ($dish) {
                        $highestRated->push([
                            'id' => $dish->dish_id,
                            'Dish_Title' => $dish->dish_name,
                            'Dish_Img' => $dish->dish_picture,
                            'Dish_Price' => floatval($dish->Price),
                            'Dish_Type' => $dish->category,
                            'Dish_Rating' => floatval($dish->dish_rating),
                            'ratings_count' => $ratingData->ratings_count,
                            'avg_rating' => $ratingData->avg_rating,
                            'Dish_Available' => $dish->dish_available,
                            'feature_type' => 'highest_rated'
                        ]);
                        
                        $processedIds[] = $ratingData->dish_id;
                    }
                }
            }
            
            // Sort highest rated by average rating
            $highestRated = $highestRated->sortByDesc('avg_rating')->values();
            
            // Calculate how many more dishes we need for the desired total
            $currentCount = $mostOrdered->count() + $highestRated->count();
            $remainingCount = max(0, $totalItemsLimit - $currentCount);
            
            // Add regular dishes if needed to reach the total limit
            if ($remainingCount > 0) {
                // Get IDs to exclude
                $excludeIds = $processedIds;
                
                foreach ($availableDishes as $dish) {
                    if (in_array($dish->dish_id, $excludeIds)) {
                        continue; // Skip dishes we've already processed
                    }
                    
                    $regular->push([
                        'id' => $dish->dish_id,
                        'Dish_Title' => $dish->dish_name,
                        'Dish_Img' => $dish->dish_picture,
                        'Dish_Price' => floatval($dish->Price),
                        'Dish_Type' => $dish->category,
                        'Dish_Rating' => floatval($dish->dish_rating),
                        'ratings_count' => $dish->ratings_count,
                        'Dish_Available' => $dish->dish_available,
                        'feature_type' => 'regular'
                    ]);
                    
                    $excludeIds[] = $dish->dish_id;
                    
                    if ($regular->count() >= $remainingCount) {
                        break;
                    }
                }
            }
            
            // Sort regular dishes by rating
            $regular = $regular->sortByDesc('Dish_Rating')->values();
            
            // Combine all collections for the final result
            $allFeatured = $mostOrdered->merge($highestRated)->merge($regular)->take($totalItemsLimit);
            
            return response()->json([
                'success' => true,
                'most_ordered' => $mostOrdered->values(),
                'highest_rated' => $highestRated->values(),
                'regular' => $regular->values(),
                'all_featured' => $allFeatured->values()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching smart featured menu: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load featured menu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}