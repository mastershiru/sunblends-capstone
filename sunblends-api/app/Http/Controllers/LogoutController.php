<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        // Log the logout event if the user is an employee
        if (Auth::guard('employee')->check()) {
            $employee = Auth::guard('employee')->user();
            activity()
                ->causedBy($employee)
                ->withProperties(['ip_address' => $request->ip()])
                ->log('employee logged out');
        }
        
        // Clear session and tokens
        if (Auth::guard('employee')->check()) {
            Auth::guard('employee')->user()->tokens()->delete();
        }
        
        Auth::guard('employee')->logout();
        Auth::guard('customer')->logout();
        Session::flush();
        
        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }
}