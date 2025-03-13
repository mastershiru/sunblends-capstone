<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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

    // Default password 
    $defaultPassword = 'password123';

    // Check if customer already exists or create a new one
    $customer = Customer::firstOrCreate(
        ['customer_email' => $request->customer_email],  // Find by email
        [
            'customer_name' => $request->customer_name,
            'customer_password' => Hash::make($defaultPassword),  // ✅ Fixed column name
            'customer_picture' => $request->customer_picture,
            'customer_number' => $request->Customer_number ?? 'N/A',  // ✅ Handle missing number
            'role_id' => 4, // ✅ Set a default role (e.g., customer)
        ]
    );

    // Generate token for the user (if using Sanctum or Passport)
    $token = $customer->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Logged in with Google successfully',
        'token' => $token,
        'user' => $customer,
        'customer_id' => $customer->customer_id,
        
    ]);
}

}
