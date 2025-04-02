<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\Admin;
use App\Models\Customer;
use Laravel\Socialite\Facades\Socialite; // Import Socialite

class GoogleLoginController extends Controller
{
    // Google Login--------------------------------------------------------------------------------------------------------------------------------
    public function googleLogin(Request $request)
    {
        // Validate incoming request
        $validated = Validator::make($request->all(), [
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_picture' => 'nullable|string',
            'Customer_number' => 'nullable|string',
        ]);

        if ($validated->fails()) {
            return response()->json(['message' => 'Invalid data', 'errors' => $validated->errors()], 400);
        }

        try {
            // Default password 
            $defaultPassword = 'password123';

            // Check if customer already exists or create a new one
            $customer = Customer::firstOrCreate(
                ['customer_email' => $request->customer_email],  // Find by email
                [
                    'customer_name' => $request->customer_name,
                    'customer_password' => Hash::make($defaultPassword),  // Using Hash::make for consistency
                    'customer_picture' => $request->customer_picture,
                    'customer_number' => $request->Customer_number ?? 'N/A',  // Handle missing number
                    'role_id' => 4, // Set a default role (e.g., customer)
                ]
            );

            // First, delete any existing tokens to prevent accumulation - just like in LoginController
            $customer->tokens()->delete();
            
            // Properly log the user in using Laravel's auth system
            Auth::guard('customer')->login($customer, true); // Remember the user
            
            // Create a single Sanctum token named 'customer-token' with customer abilities
            $token = $customer->createToken('customer-token', ['customer'])->plainTextToken;
            
            // Save in session for compatibility with session-based auth
            session([
                'logged_in_customer' => $customer,
                'guard' => 'customer'
            ]);
            
            // Return response with token and cookie - matching format of LoginController
            return response()->json([
                'success' => true,
                'message' => 'Logged in with Google successfully',
                'token' => $token,
                'redirect' => '/dish',
                'user' => $customer,
                'customer_id' => $customer->customer_id,
            ])->withCookie(cookie('laravel_session', Session::getId(), 120));
            
        } catch (\Exception $e) {
            \Log::error('Google login error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during Google login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // You might also want to add a logout method to match LoginController's functionality
    public function logout(Request $request)
    {
        try {
            // Get the authenticated user
            $user = $request->user();
            
            if ($user) {
                // Revoke all tokens
                $user->tokens()->delete();
            }
            
            // Logout the user from the session
            Auth::guard('customer')->logout();
            
            // Clear session data
            Session::flush();
            Session::regenerate();
            
            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}