<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;
use App\Models\Customer;
use Laravel\Socialite\Facades\Socialite; // Import Socialite

class AuthController extends Controller
{
    // Customer login--------------------------------------------------------------------------------------------------------------------------------
    public function customerLogin(Request $request)
{
    // Validate input
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required'
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 400); // Return 400 Bad Request if validation fails
    }

    // Find the customer by email
    $customer = Customer::where('Customer_Email', $request->email)->first();

    // Check if customer exists and the password is correct
    if ($customer && Hash::check($request->password, $customer->Customer_Password)) {
        // Generate API token
        $token = $customer->createToken('customer_auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login Successfully',
            'token' => $token,
            'customer' => $customer
        ], 200);
    }

    // If the credentials don't match, return an error message
    return response()->json(['message' => 'Invalid credentials'], 401); // Return 401 Unauthorized if login fails
}

    // Admin login--------------------------------------------------------------------------------------------------------------------------------
    public function adminLogin(Request $request)
{
    $credentials = $request->only('Admin_Name', 'Admin_Password');

    // Find admin by name in the admin_acc table
    $admin = Admin::where('Admin_Name', $credentials['Admin_Name'])->first();

    if ($admin && Hash::check($credentials['Admin_Password'], $admin->Admin_Password)) {
        $token = $admin->createToken('admin_auth_token')->plainTextToken;
        return response()->json(['message' => 'Admin login successful', 'token' => $token, 'admin' => $admin]);
    }

    return response()->json(['message' => 'Invalid credentials'], 401);
}

// Google login--------------------------------------------------------------------------------------------------------------------------------
public function googleLogin(Request $request)
{
    // Validate incoming request
    $validated = Validator::make($request->all(), [
        'Customer_Name' => 'required|string',
        'Customer_Email' => 'required|email',
        'Customer_Img' => 'nullable|string'
    ]);

    if ($validated->fails()) {
        return response()->json(['message' => 'Invalid data', 'errors' => $validated->errors()], 400);
    }

    // Default password 
    $defaultPassword = 'password123';

    // Check if customer already exists or create a new one
    $customer = Customer::firstOrCreate(
        ['Customer_Email' => $request->Customer_Email],  // Find by email
        [
            'Customer_Name' => $request->Customer_Name,
            'Customer_Password' => Hash::make($defaultPassword),  // Hash default password
            'Customer_Img' => $request->Customer_Img,
            'Customer_Number' => 'N/A',  // Or provide some default value for Customer_Number
        ]
    );

    // Generate token for the user (if using Sanctum or Passport)
    $token = $customer->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Logged in with Google successfully',
        'token' => $token,
        'user' => $customer
    ]);
}

// Register--------------------------------------------------------------------------------------------------------------------------------
public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'Customer_Name' => 'required|string|max:255',
        'Customer_Email' => 'required|email|unique:customer,Customer_Email',
        'Customer_Number' => 'nullable|string|max:20',
        'Customer_Password' => 'required|string|min:6|confirmed',
        'Customer_Img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    // Handle Image Upload
    $imagePath = null;
    if ($request->hasFile('Customer_Img')) {
        $imagePath = $request->file('Customer_Img')->store('uploads', 'public');
    }

    // Create Customer
    $customer = Customer::create([
        'Customer_Name' => $request->Customer_Name,
        'Customer_Email' => $request->Customer_Email,
        'Customer_Number' => $request->Customer_Number,
        'Customer_Password' => Hash::make($request->Customer_Password),
        'Customer_Img' => $imagePath,
    ]);

    return response()->json([
        'message' => 'Customer registered successfully',
        'customer' => $customer,
    ], 201);
}
}
