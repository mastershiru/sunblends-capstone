<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuApiController extends Controller
{
    /**
     * Get all available dishes for the menu with intelligent sorting
     * - Shows most ordered items first (if any)
     * - Then shows highest rated items (if any)
     * - Then shows all remaining available items
     */
    public function index()
    {
        try {
            // Get all available dishes
            $availableDishes = Dish::where('dish_available', 1)
                ->withCount('ratings')
                ->get();
            
            if ($availableDishes->isEmpty()) {
                // Return empty array if no dishes available
                return response()->json([]);
            }

            // Get order counts for dishes (if any orders exist)
            $orderCounts = [];
            try {
                $orderCountData = DB::table('cart')
                    ->select('dish_id', DB::raw('COUNT(*) as order_count'))
                    ->groupBy('dish_id')
                    ->get();
                
                foreach ($orderCountData as $item) {
                    $orderCounts[$item->dish_id] = $item->order_count;
                }
            } catch (\Exception $e) {
                // If cart table doesn't exist or has issues, continue without order data
                \Log::warning('Could not fetch order counts: ' . $e->getMessage());
            }
            
            // Transform data to match React component's expected format
            $menuItems = $availableDishes->map(function ($dish) use ($orderCounts) {
                $orderCount = $orderCounts[$dish->dish_id] ?? 0;
                
                return [
                    'id' => $dish->dish_id,
                    'Dish_Title' => $dish->dish_name,
                    'Dish_Img' => asset($dish->dish_picture),
                    'Dish_Type' => $dish->category,
                    'Dish_Rating' => $dish->dish_rating ?? 0,
                    'ratings_count' => $dish->ratings_count,
                    'order_count' => $orderCount,
                    'Dish_Price' => (float)$dish->Price,
                    'Dish_Persons' => 1, // Default value if not in your DB
                    'Dish_Available' => $dish->dish_available,
                    'feature_type' => $this->determineFeatureType($dish->dish_rating, $orderCount, $dish->ratings_count)
                ];
            });
            
            // Sort items: first by feature type, then by rating/order count
            $sortedItems = $menuItems->sortBy([
                // First sort by feature type priority (most_ordered > highest_rated > regular)
                ['feature_type_priority', 'desc'],
                // Then by rating (for highest rated items)
                ['Dish_Rating', 'desc'],
                // Then by order count (for most ordered items)
                ['order_count', 'desc']
            ])->values();
            
            return response()->json($sortedItems);
        } catch (\Exception $e) {
            \Log::error('Error in MenuApiController index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load menu items',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Determine the feature type of a dish based on its metrics
     */
    private function determineFeatureType($rating, $orderCount, $ratingsCount)
    {
        // Log the inputs for debugging
        \Log::debug('Determining feature type:', [
            'dish_rating' => $rating,
            'order_count' => $orderCount,
            'ratings_count' => $ratingsCount
        ]);
        
        // Add a numeric priority for sorting
        $result = [
            'type' => 'regular',
            'priority' => 0
        ];
        
        // Consider a dish "most ordered" if it has at least 2 orders (lowered from 5)
        if ($orderCount >= 2) {
            $result = [
                'type' => 'most_ordered',
                'priority' => 2 
            ];
        }
        // Consider a dish "highest rated" if it has at least a 3.5 rating with at least 1 rating
        // Lowered thresholds for testing
        else if ($rating >= 3.5 && $ratingsCount >= 1) {
            $result = [
                'type' => 'highest_rated',
                'priority' => 1
            ];
        }
        
        return $result;
    }
    
    /**
     * Get a specific dish by ID
     */
    public function show($id)
    {
        try {
            $dish = Dish::with(['ratings' => function($query) {
                $query->latest()->limit(5);
            }])->findOrFail($id);
            
            // Get average rating and count
            $ratingsInfo = Rating::where('dish_id', $id)
                ->selectRaw('COUNT(*) as count, AVG(rating) as average')
                ->first();
            
            // Get order count 
            $orderCount = 0;
            try {
                $orderCount = DB::table('cart')
                    ->where('dish_id', $id)
                    ->count();
            } catch (\Exception $e) {
                // Continue without order data if there's an issue
            }
            
            return response()->json([
                'success' => true,
                'id' => $dish->dish_id,
                'Dish_Title' => $dish->dish_name,
                'Dish_Img' => asset($dish->dish_picture),
                'Dish_Type' => $dish->category,
                'Dish_Rating' => (float)($dish->dish_rating ?? 0),
                'Dish_Price' => (float)$dish->Price,
                'Dish_Persons' => 1, // Default value
                'Dish_Available' => $dish->dish_available,
                'ratings_count' => $ratingsInfo->count ?? 0,
                'average_rating' => $ratingsInfo->average ? round($ratingsInfo->average, 1) : 0,
                'order_count' => $orderCount,
                'recent_ratings' => $dish->ratings->map(function($rating) {
                    return [
                        'rating' => $rating->rating,
                        'review' => $rating->review,
                        'created_at' => $rating->created_at
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dish details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * For debugging: Get raw data about ratings and orders
     */
    public function debug()
    {
        try {
            $dishes = Dish::withCount('ratings')->get();
            
            $orderCounts = [];
            try {
                $orderCountData = DB::table('cart')
                    ->select('dish_id', DB::raw('COUNT(*) as order_count'))
                    ->groupBy('dish_id')
                    ->get();
                
                foreach ($orderCountData as $item) {
                    $orderCounts[$item->dish_id] = $item->order_count;
                }
            } catch (\Exception $e) {
                $orderCounts = ['error' => $e->getMessage()];
            }
            
            $ratings = Rating::select('dish_id', 'rating')
                ->orderBy('dish_id')
                ->get()
                ->groupBy('dish_id');
            
            return response()->json([
                'success' => true,
                'dishes_count' => $dishes->count(),
                'dishes' => $dishes->map(function($dish) use ($orderCounts) {
                    return [
                        'id' => $dish->dish_id,
                        'name' => $dish->dish_name,
                        'rating' => $dish->dish_rating,
                        'ratings_count' => $dish->ratings_count,
                        'order_count' => $orderCounts[$dish->dish_id] ?? 0
                    ];
                }),
                'order_counts' => $orderCounts,
                'ratings_by_dish' => $ratings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error in debug endpoint',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Advanced menu endpoint with featured items and categories
     */
    public function advancedMenu(Request $request)
    {
        try {
            // Get filter parameters with validation
            $category = $request->input('category');
            $minRating = $request->input('min_rating') ? floatval($request->input('min_rating')) : null;
            $maxPrice = $request->input('max_price') ? floatval($request->input('max_price')) : null;
            
            // Add a force refresh option
            $forceRefresh = $request->has('refresh') && $request->refresh === 'true';
            
            // Clear any method-level caching if force refresh is requested
            if ($forceRefresh) {
                \Log::info('Forced menu refresh requested');
                // If you have any caching, clear it here
            }
            
            // Base query - eager load ratings for better performance
            $query = Dish::where(function($query) {
                $query->where('dish_available', 'yes')
                      ->orWhere('dish_available', 1);
            })
            ->withCount('ratings');
            
            // Apply filters 
            if ($category) {
                $query->where('category', $category);
            }
            
            if ($minRating) {
                $query->where('dish_rating', '>=', $minRating);
            }
            
            if ($maxPrice) {
                $query->where('Price', '<=', $maxPrice);
            }
            
            // Get the dishes
            $dishes = $query->get();
            
            // Get ratings data directly from ratings table for accuracy
            $ratingsData = \DB::table('ratings')
                ->select('dish_id', 
                         \DB::raw('AVG(rating) as avg_rating'), 
                         \DB::raw('COUNT(*) as ratings_count'))
                ->groupBy('dish_id')
                ->get()
                ->keyBy('dish_id');
            
            // Get order counts
            $orderCounts = [];
            try {
                $orderCountData = \DB::table('cart')
                    ->select('dish_id', \DB::raw('COUNT(*) as order_count'))
                    ->groupBy('dish_id')
                    ->get();
                
                // Convert to associative array for faster lookups
                $orderCounts = $orderCountData->pluck('order_count', 'dish_id')->toArray();
            } catch (\Exception $e) {
                \Log::warning('Failed to get order counts in advancedMenu: ' . $e->getMessage());
                // Continue without order data
            }
            
            // Format the response with live ratings data
            $menuItems = $dishes->map(function ($dish) use ($orderCounts, $ratingsData) {
                $orderCount = $orderCounts[$dish->dish_id] ?? 0;
                
                // Use the live rating data instead of dish.dish_rating field
                $ratingInfo = $ratingsData[$dish->dish_id] ?? null;
                $liveRating = $ratingInfo ? round($ratingInfo->avg_rating, 1) : 0;
                $ratingsCount = $ratingInfo ? $ratingInfo->ratings_count : 0;
                
                // Determine feature type with live rating data
                $featureInfo = $this->determineFeatureType(
                    $liveRating, 
                    $orderCount, 
                    $ratingsCount
                );
                
                // Debug log to compare dish_rating vs live rating
                if ($liveRating != $dish->dish_rating) {
                    \Log::info("Rating difference detected for dish #{$dish->dish_id}:", [
                        'dish_name' => $dish->dish_name,
                        'stored_rating' => $dish->dish_rating,
                        'live_rating' => $liveRating,
                        'ratings_count' => $ratingsCount
                    ]);
                }
                
                return [
                    'id' => $dish->dish_id,
                    'Dish_Title' => $dish->dish_name,
                    'Dish_Img' => asset($dish->dish_picture),
                    'Dish_Type' => $dish->category,
                    'Dish_Rating' => (float)$liveRating, // Use live rating instead of dish_rating
                    'ratings_count' => $ratingsCount,
                    'order_count' => $orderCount,
                    'Dish_Price' => (float)$dish->Price,
                    'Dish_Persons' => 1,
                    'Dish_Available' => $dish->dish_available,
                    'feature_type' => $featureInfo['type'],
                    'feature_priority' => $featureInfo['priority']
                ];
            });
            
            // Get unique categories
            $categories = Dish::where(function($query) {
                    $query->where('dish_available', 'yes')
                          ->orWhere('dish_available', 1);
                })
                ->select('category')
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category');
            
            // Sort and organize dishes by feature type
            $mostOrdered = $menuItems->where('feature_type', 'most_ordered')
                ->sortByDesc('order_count')
                ->values();
                
            $highestRated = $menuItems->where('feature_type', 'highest_rated')
                ->sortByDesc('Dish_Rating')
                ->values();
                
            $regular = $menuItems->where('feature_type', 'regular')
                ->sortByDesc('Dish_Rating')
                ->values();
            
            // Get featured items (combination of most ordered and highest rated)
            $featured = $mostOrdered->merge($highestRated)
                ->sortByDesc('feature_priority')
                ->take(6)
                ->values();
            
            // Return comprehensive response with filtering metadata
            return response()->json([
                'success' => true,
                'categories' => $categories,
                'featured' => $featured,
                'most_ordered' => $mostOrdered,
                'highest_rated' => $highestRated,
                'regular' => $regular,
                'menu_items' => $menuItems->sortByDesc('feature_priority')->values(),
                'filter_applied' => !empty($category) || !empty($minRating) || !empty($maxPrice),
                'filter_details' => [
                    'category' => $category ?? null,
                    'min_rating' => $minRating ?? null,
                    'max_price' => $maxPrice ?? null
                ],
                'total_items' => $menuItems->count(),
                'refresh_performed' => $forceRefresh
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in advancedMenu: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load advanced menu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}