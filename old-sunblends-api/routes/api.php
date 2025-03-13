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
use App\Http\Controllers\GoogleLoginController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', [TestController::class, 'index']);

Route::get('/dish', [Dish_Controller::class, 'index']);

Route::post('/login', [LoginController::class, 'login']);

Route::get('/menu-items', [MenuApiController::class, 'index']);
Route::get('/menu-items/{id}', [MenuApiController::class, 'show']);

Route::post('auth/google/callback', [GoogleLoginController::class, 'googleLogin']);


Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/reservation/Dashboard', [reservationDashboardController::class, 'index']);

    Route::get('/Dine-in', [dineInController::class, 'index']);

    Route::get('/Orders-Queue', [OrderQueueController::class, 'index']);

    Route::get('/menu', [menuController::class, 'index']);

    // Cart & Checkout API Routes
    Route::post('/getUserData', [CartApiController::class, 'getUserData']);
    Route::post('/addToCart', [CartApiController::class, 'addToCart']);
    Route::post('/getCartItems', [CartApiController::class, 'getCartItems']);
    Route::post('/updateCartItem', [CartApiController::class, 'updateCartItem']);
    Route::post('/removeCartItem', [CartApiController::class, 'removeCartItem']);
    Route::post('/checkout', [CartApiController::class, 'checkout']);
    Route::get('/cart/{customer_id}/count', [CartApiController::class, 'getCartCount']);





