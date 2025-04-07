<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class LoginController extends Controller
{
    
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $customer = Customer::where('customer_email', $request->email)->first();
        $employee = Employee::where('employee_email', $request->email)->first();

        try {
            // Customer login logic (unchanged)
            if ($customer && (Hash::check($request->password, $customer->customer_password) || 
                hash('sha256', $request->password) === $customer->customer_password)) {
                // First, delete any existing tokens to prevent accumulation
                $customer->tokens()->delete();
                
                // Properly log the user in using Laravel's auth
                Auth::guard('customer')->login($customer, $request->remember ?? false);
                
                // Create a single Sanctum token named 'customer-token'
                $token = $customer->createToken('customer-token', ['customer'])->plainTextToken;
                
                // Save in session for compatibility with session-based auth
                session([
                    'logged_in_customer' => $customer,
                    'guard' => 'customer'
                ]);
                
                // Return response with token
                return response()->json([
                    'success' => true,
                    'message' => 'Customer login successful!',
                    'token' => $token,
                    'redirect' => '/dish',
                    'user' => $customer
                ])->withCookie(cookie('laravel_session', Session::getId(), 120));
            }
            
            // Employee login logic (updated)
            if ($employee) {
                // Check if using new hashed password format
                if (Hash::check($request->password, $employee->employee_password)) {
                    // Password is verified with Hash::check
                    
                    // First, delete any existing tokens to prevent accumulation
                    $employee->tokens()->delete();
                    
                    // Properly log the user in using Laravel's auth
                    Auth::guard('employee')->login($employee, $request->remember ?? false);
                    
                    // Get employee roles and permissions
                    $roles = $employee->getRoleNames();
                    $permissions = $employee->getAllPermissions()->pluck('name');
                    
                    // Create Sanctum token - for API access
                    $token = $employee->createToken('employee-token', ['employee'])->plainTextToken;
                    
                    // Determine redirect based on role
                    $redirect = '/dashboard';
                    
                    // Save in session for compatibility with session-based auth
                    session([
                        'logged_in_employee' => $employee,
                        'guard' => 'employee',
                        'roles' => $roles,
                        'permissions' => $permissions
                    ]);
                    
                    // Return response with redirect to a web route, not API route
                    return response()->json([
                        'success' => true,
                        'message' => 'Employee login successful!',
                        'token' => $token,
                        'redirect' => $redirect,
                        'user' => $employee,
                        'roles' => $roles,
                        'permissions' => $permissions
                    ]);
                }
                // Fallback for old password format
                else if (hash('sha256', $request->password) === $employee->employee_password) {
                    // This is using the old password format, but we should update it to the new format
                    $employee->employee_password = Hash::make($request->password);
                    $employee->save();
                    
                    // First, delete any existing tokens to prevent accumulation
                    $employee->tokens()->delete();
                    
                    // Properly log the user in using Laravel's auth
                    Auth::guard('employee')->login($employee, $request->remember ?? false);
                    
                    // Get employee roles and permissions
                    $roles = $employee->getRoleNames();
                    $permissions = $employee->getAllPermissions()->pluck('name');
                    
                    // Create Sanctum token - for API access
                    $token = $employee->createToken('employee-token', ['employee'])->plainTextToken;
                    
                    // Determine redirect based on role
                    $redirect = '/dashboard';
                    
                    // Save in session for compatibility with session-based auth
                    session([
                        'logged_in_employee' => $employee,
                        'guard' => 'employee',
                        'roles' => $roles,
                        'permissions' => $permissions
                    ]);
                    
                    // Return response with redirect to a web route, not API route
                    return response()->json([
                        'success' => true,
                        'message' => 'Employee login successful!',
                        'token' => $token,
                        'redirect' => $redirect,
                        'user' => $employee,
                        'roles' => $roles,
                        'permissions' => $permissions
                    ]);
                }
            }

            // If no matching user was found
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
                
        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        // Fetch the user by ID
        $user = Auth::user();

        // Check if the user exists
        if (!$user) {
            return redirect()->back()->withErrors(['user_error' => 'User not found.']);
        }

        return view('home', compact('user'));
    }

    public function show(Request $request)
    {
        // Fetch the user by ID
        $user = Customer::where('customer_name', $request->id)->get();

        // Check if the user exists
        if ($user->isEmpty()) {
            return json_encode(['error' => 'User not found.']);
        }

        return json_encode(['user' => $user,
                           'message' => 'User found.']);
    }
}