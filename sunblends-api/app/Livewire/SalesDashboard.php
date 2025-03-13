<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\Cart;
use App\Models\Dish;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesDashboard extends Component
{
    // Date filtering properties
    public $dateRange = 'today';
    public $startDate;
    public $endDate;
    
    // Sales statistics
    public $totalSales = 0;
    public $totalOrders = 0;
    public $averageOrderValue = 0;
    
    // Top dishes data
    public $topDishes = [];
    
    // Daily sales data for chart
    public $dailySales = [];
    
    public function mount()
    {
        // Set default date range
        $this->setDateRange('today');
    }
    
    /**
     * Set date range based on selection
     */
    public function setDateRange($range)
    {
        $this->dateRange = $range;
        
        switch ($range) {
            case 'today':
                $this->startDate = Carbon::today()->format('Y-m-d');
                $this->endDate = Carbon::today()->format('Y-m-d');
                break;
            case 'yesterday':
                $this->startDate = Carbon::yesterday()->format('Y-m-d');
                $this->endDate = Carbon::yesterday()->format('Y-m-d');
                break;
            case 'this_week':
                $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->startDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'custom':
                // Don't change custom dates if already set
                if (!$this->startDate) {
                    $this->startDate = Carbon::today()->format('Y-m-d');
                }
                if (!$this->endDate) {
                    $this->endDate = Carbon::today()->format('Y-m-d');
                }
                break;
            default:
                $this->startDate = Carbon::today()->format('Y-m-d');
                $this->endDate = Carbon::today()->format('Y-m-d');
                break;
        }
        
        $this->calculateStats();
    }
    
    /**
     * Handle date range change
     */
    public function updatedDateRange()
    {
        $this->setDateRange($this->dateRange);
    }
    
    /**
     * Handle date input changes
     */
    public function updatedStartDate()
    {
        $this->dateRange = 'custom';
        $this->calculateStats();
    }
    
    public function updatedEndDate()
    {
        $this->dateRange = 'custom';
        $this->calculateStats();
    }
    
    /**
     * Calculate all sales statistics based on current date range
     */
    public function calculateStats()
    {
        // Calculate total sales (only completed transactions)
        $this->totalSales = Transaction::where('transaction_status', 'completed')
            ->whereBetween(DB::raw('DATE(transaction_date)'), [$this->startDate, $this->endDate])
            ->sum('cash_amount');
        
        // Calculate total orders
        $this->totalOrders = Transaction::where('transaction_status', 'completed')
            ->whereBetween(DB::raw('DATE(transaction_date)'), [$this->startDate, $this->endDate])
            ->count();
        
        // Calculate average order value
        $this->averageOrderValue = $this->totalOrders > 0 
            ? $this->totalSales / $this->totalOrders 
            : 0;
        
        // Get top 7 most ordered dishes
        $this->getTopDishes();
        
        // Get daily sales for line chart
        $this->getDailySales();
    }
    
    /**
     * Get top 7 most ordered dishes within the date range
     */
    private function getTopDishes()
    {
        try {
            \Log::info("Starting getTopDishes for date range: {$this->startDate} to {$this->endDate}");
            
            // Get all order IDs from completed transactions in the date range
            $orderIds = Transaction::where('transaction_status', 'completed')
                ->whereBetween(DB::raw('DATE(transaction_date)'), [$this->startDate, $this->endDate])
                ->pluck('order_id')
                ->filter() // Remove any null values
                ->toArray();
            
            \Log::info("Found " . count($orderIds) . " completed order IDs in date range");
            
            // If no orders found, return empty array
            if (empty($orderIds)) {
                \Log::info("No completed orders found in date range. Returning empty array.");
                $this->topDishes = [];
                return;
            }
            
            // Use Eloquent with proper relationships
            // We use withTrashed() because cart items are soft-deleted when checkout happens
            $topDishes = Cart::withTrashed()
                ->whereIn('cart.order_id', $orderIds)  // Specify table name
                ->whereNotNull('cart.dish_id')         // Specify table name
                ->whereNotNull('cart.deleted_at')      // Specify table name
                ->join('dish', 'cart.dish_id', '=', 'dish.dish_id')
                ->select(
                    'dish.dish_id',
                    'dish.dish_name',
                    'dish.Price as price',
                    'dish.dish_picture',
                    DB::raw('SUM(cart.quantity) as total_ordered')
                )
                ->groupBy('dish.dish_id', 'dish.dish_name', 'dish.Price', 'dish.dish_picture')
                ->orderByDesc('total_ordered')
                ->limit(7)
                ->get();
            
            \Log::info("Query returned " . count($topDishes) . " top dishes");
            
            if ($topDishes->isNotEmpty()) {
                // Format the results for the view
                $this->topDishes = $topDishes->map(function ($dish) {
                    return [
                        'dish_id' => $dish->dish_id,
                        'dish_name' => $dish->dish_name ?? 'Unknown Dish',
                        'total_ordered' => $dish->total_ordered ?? 0,
                        'total_revenue' => ($dish->price ?? 0) * ($dish->total_ordered ?? 0),
                        'price' => $dish->price ?? 0,
                        'image_url' => $dish->dish_picture ?? '', // Changed to use dish_picture instead of image_url
                    ];
                })->toArray();
                
                \Log::info("Successfully formatted " . count($this->topDishes) . " top dishes");
            } else {
                // If no results, try a more lenient approach (without requiring soft-deleted items)
                \Log::info("No results with deleted_at filter, trying without the filter");
                
                $topDishes = Cart::withTrashed()
                    ->whereIn('cart.order_id', $orderIds)  // Specify table name
                    ->whereNotNull('cart.dish_id')         // Specify table name
                    ->join('dish', 'cart.dish_id', '=', 'dish.dish_id')
                    ->select(
                        'dish.dish_id',
                        'dish.dish_name',
                        'dish.Price as price',
                        'dish.dish_picture',
                        DB::raw('SUM(cart.quantity) as total_ordered')
                    )
                    ->groupBy('dish.dish_id', 'dish.dish_name', 'dish.Price', 'dish.dish_picture')
                    ->orderByDesc('total_ordered')
                    ->limit(7)
                    ->get();
                
                \Log::info("Lenient query returned " . count($topDishes) . " dishes");
                
                if ($topDishes->isNotEmpty()) {
                    $this->topDishes = $topDishes->map(function ($dish) {
                        return [
                            'dish_id' => $dish->dish_id,
                            'dish_name' => $dish->dish_name ?? 'Unknown Dish',
                            'total_ordered' => $dish->total_ordered ?? 0,
                            'total_revenue' => ($dish->price ?? 0) * ($dish->total_ordered ?? 0),
                            'price' => $dish->price ?? 0,
                            'image_url' => $dish->dish_picture ?? '', // Changed to use dish_picture instead of image_url
                        ];
                    })->toArray();
                    
                    \Log::info("Successfully formatted " . count($this->topDishes) . " top dishes from lenient query");
                } else {
                    $this->logDiagnosticInfo($orderIds);
                    $this->topDishes = [];
                }
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error("Error in getTopDishes: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            // Set empty array to avoid frontend errors
            $this->topDishes = [];
        }
    }

/**
 * Log diagnostic information when no results are found
 * 
 * @param array $orderIds
 * @return void
 */
private function logDiagnosticInfo($orderIds)
{
    \Log::info("All attempts returned no results. Running diagnostics.");
    
    // Check if we have any cart items at all
    $anyCartItems = DB::table('cart')->count();
    \Log::info("Total cart items in database: " . $anyCartItems);
    
    // Check if we have any cart items with dish_id
    $cartItemsWithDish = DB::table('cart')->whereNotNull('dish_id')->count();
    \Log::info("Total cart items with dish_id: " . $cartItemsWithDish);
    
    // Check if we have any soft-deleted cart items
    $softDeletedItems = DB::table('cart')->whereNotNull('deleted_at')->count();
    \Log::info("Total soft-deleted cart items: " . $softDeletedItems);
    
    // Check if we have any cart items linked to these orders
    $cartItemsForOrders = DB::table('cart')->whereIn('order_id', $orderIds)->count();
    \Log::info("Total cart items linked to filtered orders: " . $cartItemsForOrders);
    
    // Check soft-deleted cart items for these orders
    $deletedCartForOrders = DB::table('cart')
        ->whereIn('order_id', $orderIds)
        ->whereNotNull('deleted_at')
        ->count();
    \Log::info("Soft-deleted cart items for filtered orders: " . $deletedCartForOrders);
    
    // Sample some data from orders
    if (!empty($orderIds)) {
        $sampleOrders = DB::table('order_detail')
            ->whereIn('order_id', array_slice($orderIds, 0, 5))
            ->get();
        \Log::info("Sample order data: " . json_encode($sampleOrders));
    }
}
    
    /**
     * Get daily sales data for chart
     */
    private function getDailySales()
    {
        try {
            // Get start and end date as Carbon objects for manipulation
            $start = Carbon::parse($this->startDate);
            $end = Carbon::parse($this->endDate);
            
            // Prepare array structure for the chart
            $labels = [];
            $data = [];
            
            // If date range is more than 31 days, group by weeks instead
            $groupBy = $start->diffInDays($end) > 31 ? 'week' : 'day';
            
            if ($groupBy === 'day') {
                // Get daily sales
                $dailySalesData = Transaction::where('transaction_status', 'completed')
                    ->whereBetween(DB::raw('DATE(transaction_date)'), [$this->startDate, $this->endDate])
                    ->select(
                        DB::raw('DATE(transaction_date) as date'),
                        DB::raw('SUM(cash_amount) as daily_total')
                    )
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                
                // Create a map of date => amount for easy lookup
                $salesMap = $dailySalesData->pluck('daily_total', 'date')->toArray();
                
                // Fill in all days within range (including zeros for days without sales)
                $current = $start->copy();
                while ($current->lte($end)) {
                    $dateStr = $current->format('Y-m-d');
                    $labels[] = $current->format('M d');
                    $data[] = $salesMap[$dateStr] ?? 0;
                    $current->addDay();
                }
            } else {
                // Group by week for longer ranges
                $weekData = Transaction::where('transaction_status', 'completed')
                    ->whereBetween(DB::raw('DATE(transaction_date)'), [$this->startDate, $this->endDate])
                    ->select(
                        DB::raw('YEARWEEK(transaction_date, 1) as week_number'),
                        DB::raw('MIN(DATE(transaction_date)) as week_start'),
                        DB::raw('SUM(cash_amount) as weekly_total')
                    )
                    ->groupBy('week_number')
                    ->orderBy('week_number')
                    ->get();
                
                foreach ($weekData as $week) {
                    $weekStart = Carbon::parse($week->week_start)->format('M d');
                    $labels[] = "Week of {$weekStart}";
                    $data[] = $week->weekly_total;
                }
                
                // If no data found, create at least one data point to show the chart
                if ($weekData->isEmpty()) {
                    $labels = ["No Data"];
                    $data = [0];
                }
            }
            
            // Ensure we have at least one data point even if no sales
            if (empty($labels)) {
                $labels = ["No Data"];
                $data = [0];
            }
            
            $this->dailySales = [
                'labels' => $labels,
                'data' => $data
            ];
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error("Error in getDailySales: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            // Default to empty data with placeholder to avoid JS errors
            $this->dailySales = [
                'labels' => ['No Data Available'],
                'data' => [0]
            ];
        }
    }
    
    public function render()
    {
        return view('livewire.sales-dashboard', [
            'totalSales' => $this->totalSales,
            'totalOrders' => $this->totalOrders,
            'averageOrderValue' => $this->averageOrderValue,
            'topDishes' => $this->topDishes,
            'dailySales' => $this->dailySales
        ]);
    }
}