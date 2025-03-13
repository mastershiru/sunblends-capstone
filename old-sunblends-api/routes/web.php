<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dish_Controller;
use App\Http\Controllers\LoginController;
use App\Notifications\OtpNotification;
use App\Http\Controllers\OtpVerificationController;
use Illuminate\Foundation\Auth\EmailVerificationNotification;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\menuController;
use App\Http\Controllers\OrderQueueController;
use App\Http\Controllers\reservationController;
use App\Http\Controllers\reservationDashboardController;
use App\Http\Controllers\dineInController;

use App\Http\Middleware\CustomerView;


// routes for customer

Route::middleware(['customer_view'])->group(function () {
    
});

// routes for employee with middleware

Route::middleware(['employee_view'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']); 

    Route::get('/reservation/Dashboard', [reservationDashboardController::class, 'index']);

    Route::get('/Dine-in', [dineInController::class, 'index']);

    Route::get('/Orders-Queue', [OrderQueueController::class, 'index']);

    Route::get('/menu', [menuController::class, 'index']);

});

// For guest and customers

Route::get('/employee/login', function () {
    return view('employee_login');
});

Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');

Route::post('/login', [LoginController::class, 'login']);

Route::get('/dish', [Dish_Controller::class, 'index']);

Route::get('/reservation', [reservationController::class, 'index']);

Route::get('/home', function () {
    return view('dish.home');
})->name('home');




Route::get('/test-email/{id}', function ($id) {
    // Find the user by ID
    $user = User::find($id);

    if ($user) {
        $otp = rand(100000, 999999);
        $user->notify(new OtpNotification($otp));
        return 'Email sent to user with ID ' . $id . '!';
    } else {
        return 'User not found!';
    }
});

Route::get('/', function () {
    return view('welcome');
});














