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
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ActivityLogController;

use App\Http\Middleware\CustomerView;

// Login routes (accessible to all)
Route::get('/employee/login', function () {
    return view('employee_login');
})->name('employee.login');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');


// Customer routes
Route::middleware(['customer_view'])->group(function () {
    // Your customer-specific routes
});

// Employee routes with base employee authentication
Route::middleware(['employee_view'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); 
    Route::get('/Orders-Queue', [OrderQueueController::class, 'index'])->name('orders.queue');
    Route::get('/Dine-in', [dineInController::class, 'index'])->name('dine.in');
    Route::get('/reservation/Dashboard', [reservationDashboardController::class, 'index'])->name('reservation.dashboard');
    Route::get('/menu', [menuController::class, 'index']);
    Route::get('/Transaction', function () {
        return view('Transaction_Dashboard');
    })->name('transaction.index');
    Route::get('/Sales', function () {
        return view('sale_dashboard');
    })->name('sales.index');
    Route::get('/activity-logs', [ActivityLogController::class, 'index']);
});



// Manager/Admin routes that require manager privileges
Route::middleware(['manager'])->group(function () {
    
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
});

// For guests and customers
Route::get('/dish', [Dish_Controller::class, 'index'])->name('dish.index');
Route::get('/reservation', [reservationController::class, 'index'])->name('reservation.index');
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
})->name('welcome');