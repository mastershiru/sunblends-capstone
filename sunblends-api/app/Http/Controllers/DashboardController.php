<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;



class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // For API requests, return JSON
        if ($request->expectsJson()) {
            $employee = $request->user();
            
            if (!$employee) {
                return response()->json([
                    'error' => 'Unauthenticated',
                ], 401);
            }
            
            return response()->json([
                'user' => $employee,
                'dashboard_data' => [
                    // Other dashboard data you want to return
                ]
            ]);
        }
        
        // For web requests, show the Blade view
        // Get employee from auth guard
        $employee = Auth::guard('employee')->user();
        
        if (!$employee) {
            return redirect('/login')->with('error', 'Please login to access the dashboard');
        }
        
        // Return the Blade view
        return view('dashboard', ['employee' => $employee]);
    }
}
