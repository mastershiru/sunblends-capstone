<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Customer;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            // Get user data from Google
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            \Log::info('Google OAuth callback received', [
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName()
            ]);

            // Check if customer already exists or create a new one
            $customer = Customer::firstOrCreate(
                ['customer_email' => $googleUser->getEmail()],
                [
                    'customer_name' => $googleUser->getName(),
                    'customer_password' => Hash::make(str_random(16)), // Random secure password
                    'customer_picture' => $googleUser->getAvatar(),
                    'customer_number' => 'N/A', // Handle missing number
                    'role_id' => 4, // Customer role
                ]
            );

            // Delete any existing tokens
            $customer->tokens()->delete();
            
            // Login the user
            Auth::guard('customer')->login($customer, true);
            
            // Create a token
            $token = $customer->createToken('customer-token', ['customer'])->plainTextToken;
            
            // Save in session
            session([
                'logged_in_customer' => $customer,
                'guard' => 'customer'
            ]);
            
            // Redirect to frontend with token
            return redirect()->away("https://sunblends.store/auth/callback?token={$token}&user=" . json_encode([
                'customer_id' => $customer->customer_id,
                'name' => $customer->customer_name,
                'email' => $customer->customer_email
            ]));
            
        } catch (\Exception $e) {
            \Log::error('Google callback error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Redirect back to frontend with error
            return redirect()->away('https://sunblends.store/login?error=google_auth_failed');
        }
    }

    /**
     * Process frontend-initiated Google login
     * This maintains compatibility with your existing implementation
     */
    public function googleLogin(Request $request)
    {
        \Log::info('Manual Google login request received', [
            'data' => $request->except(['customer_picture'])
        ]);

        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_picture' => 'nullable|string',
            'Customer_number' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data', 'errors' => $validator->errors()], 400);
        }

        try {
            // Check if customer already exists or create a new one
            $customer = Customer::firstOrCreate(
                ['customer_email' => $request->customer_email],
                [
                    'customer_name' => $request->customer_name,
                    'customer_password' => Hash::make(str_random(16)),
                    'customer_picture' => $request->customer_picture,
                    'customer_number' => $request->Customer_number ?? 'N/A',
                    'role_id' => 4,
                ]
            );

            // Delete any existing tokens
            $customer->tokens()->delete();
            
            // Login the user
            Auth::guard('customer')->login($customer, true);
            
            // Create a token
            $token = $customer->createToken('customer-token', ['customer'])->plainTextToken;
            
            // Save in session
            session([
                'logged_in_customer' => $customer,
                'guard' => 'customer'
            ]);
            
            // Configure cookie for cross-domain
            $sessionCookie = cookie(
                'laravel_session', 
                Session::getId(), 
                120,
                '/',
                '.sunblends.store',
                true,
                false,
                false,
                'none'
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Logged in with Google successfully',
                'token' => $token,
                'redirect' => '/dish',
                'user' => $customer,
                'customer_id' => $customer->customer_id,
            ])->withCookie($sessionCookie);
            
        } catch (\Exception $e) {
            \Log::error('Manual Google login error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during Google login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout the user
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user) {
                $user->tokens()->delete();
            }
            
            Auth::guard('customer')->logout();
            Session::flush();
            Session::regenerate();
            
            $clearCookie = cookie(
                'laravel_session', 
                '', 
                -1,
                '/',
                '.sunblends.store',
                true,
                false,
                false,
                'none'
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ])->withCookie($clearCookie);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}