<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Dish_Controller;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\reservationDashboardController;
use App\Http\Controllers\dineInController;
use App\Http\Controllers\OrderQueueController;
use App\Http\Controllers\menuController;
use App\Http\Controllers\MenuApiController;
use App\Http\Controllers\CartApiController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\reservationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\EmployeeController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', [TestController::class, 'index']);

Route::get('/dish', [Dish_Controller::class, 'index']);

Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->post('/logout', [GoogleLoginController::class, 'logout']);

Route::get('/menu-items', [MenuApiController::class, 'index']);
Route::get('/menu-items/{id}', [MenuApiController::class, 'show']);
Route::get('/menu-debug', [MenuApiController::class, 'debug']);
Route::get('/advanced-menu', [MenuApiController::class, 'advancedMenu']);

Route::get('/test/login', [LoginController::class, 'show']);

Route::post('/auth/google/callback', [GoogleLoginController::class, 'googleLogin']);

Route::post('/reservation/create', [reservationController::class, 'create']);
Route::get('reservations/customer/{customerId}', [ReservationController::class, 'getCustomerReservations']);
Route::post('reservations/{reservationId}/cancel', [ReservationController::class, 'cancelReservation']);

Route::post('/refresh-session', [AuthController::class, 'refreshSession']);

Route::middleware('auth:sanctum')->post('/broadcasting/auth', function (Request $request) {
    return Broadcast::auth($request);
});


//Rating
Route::middleware('auth:sanctum')->get('/ratings/check', [RatingController::class, 'checkRating']);
Route::middleware('auth:sanctum')->post('/dishes/rate', [RatingController::class, 'rateDish']);
Route::get('/dishes/{id}/ratings', [RatingController::class, 'getDishRatings']);
Route::get('/featured-menu', [RatingController::class, 'getFeaturedMenuItems']);
Route::get('/smart-featured-menu', [RatingController::class, 'getSmartFeaturedMenu']);

// Public token validation endpoint (doesn't require authentication middleware)
Route::post('/check-token', [AuthController::class, 'checkToken']);

Route::post('/check-token-type', [AuthController::class, 'checkTokenType']);

// Add the original protected route for compatibility
Route::middleware('auth:sanctum')->get('/validate-token', [AuthController::class, 'validateToken']);

Route::middleware('auth:sanctum')->get('/validate-token', [AuthController::class, 'validateToken']);

//notification
Route::get('/notifications', [NotificationController::class, 'index']);
Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);

//order details
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::get('/orders/{id}/items', [OrderController::class, 'getOrderItems']);
Route::get('/orders/customer/{id}', [OrderController::class, 'getCustomerOrders']);
Route::middleware('auth:sanctum')->post('/orders/{id}/cancel', [OrderController::class, 'cancelOrder']);

//report
Route::get('/reports/sales', [ReportController::class, 'exportSalesReport'])->name('api.reports.sales');

Route::middleware(['auth', 'role:Super Admin|Manager'])->group(function () {
    Route::get('/employees', [EmployeeController::class, 'index']);
});

Route::get('/csrf-token', function (Request $request) {
    return response()->json(['csrf_token' => csrf_token()]);
});

Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/reservation/Dashboard', [reservationDashboardController::class, 'index']);

    Route::get('/Dine-in', [dineInController::class, 'index']);

    Route::get('/Orders-Queue', [OrderQueueController::class, 'index']);

    // Cart & Checkout API Routes
    Route::post('/getUserData', [CartApiController::class, 'getUserData']);
    Route::post('/addToCart', [CartApiController::class, 'addToCart']);
    Route::post('/getCartItems', [CartApiController::class, 'getCartItems']);
    Route::post('/updateCartItem', [CartApiController::class, 'updateCartItem']);
    Route::post('/removeCartItem', [CartApiController::class, 'removeCartItem']);
    Route::post('/checkout', [CartApiController::class, 'checkout']);
    Route::get('/cart/{customer_id}/count', [CartApiController::class, 'getCartCount']);




