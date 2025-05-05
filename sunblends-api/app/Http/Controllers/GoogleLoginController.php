<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
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
        try {
            // Get the base URL of the application
            $baseUrl = config('app.url');
            
            // Construct a redirect URL without /api prefix
            $redirectUrl = $baseUrl . '/auth/google/callback';
            
            \Log::info('Redirecting to Google OAuth with callback URL: ' . $redirectUrl);
            
            return Socialite::driver('google')
                ->stateless()
                ->redirectUrl($redirectUrl) // Explicitly set the redirect URL
                ->with(['hd' => 'tua.edu.ph'])
                ->redirect();
        } catch (\Exception $e) {
            \Log::error('Error redirecting to Google: ' . $e->getMessage());
            return redirect()->away('https://sunblends.store/login?error=' . 
                urlencode('Failed to connect to Google. Please try again.'));
        }
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

            // Check if email is from TUA domain (optional - you can also enforce this in the redirectToGoogle method)
            if (!str_ends_with($googleUser->getEmail(), '@tua.edu.ph')) {
                \Log::warning('Non-TUA email attempted login', ['email' => $googleUser->getEmail()]);
                return redirect()->away('https://sunblends.store/login?error=' . 
                    urlencode('Please use your TUA organizational email (@tua.edu.ph) to login.'));
            }

            // Check if customer already exists or create a new one
            $customer = Customer::firstOrCreate(
                ['customer_email' => $googleUser->getEmail()],
                [
                    'customer_name' => $googleUser->getName(),
                    'customer_password' => Hash::make(Str::random(16)), // Random secure password
                    'customer_picture' => $googleUser->getAvatar(),
                    'customer_number' => 'N/A', // Default phone number
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
            
            // Create a user data object to pass to frontend
            $userData = [
                'customer_id' => $customer->customer_id,
                'customer_name' => $customer->customer_name,
                'customer_email' => $customer->customer_email,
                'customer_picture' => $customer->customer_picture
            ];
            
            // Set session cookie with proper domain settings
            $sessionCookie = cookie(
                'laravel_session', 
                Session::getId(), 
                120,     // minutes
                '/',     // path
                '.sunblends.store', // domain - note the leading dot to include all subdomains
                true,    // secure (HTTPS only)
                false,   // httpOnly
                false,   // raw
                'none'   // sameSite policy
            );
            
            // Redirect to frontend with token
            $frontendUrl = 'https://sunblends.store';
            $callbackUrl = "{$frontendUrl}/auth/callback?token={$token}&user=" . urlencode(json_encode($userData));
            
            return redirect()->away($callbackUrl)->withCookie($sessionCookie);
            
        } catch (\Exception $e) {
            \Log::error('Google callback error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Redirect back to frontend with error
            return redirect()->away('https://sunblends.store' . 
                '/login?error=' . urlencode('Google authentication failed. Please try again.'));
        }
    }

    /**
     * Legacy method to support the existing frontend implementation
     * This can be kept for backward compatibility
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
            'Customer_Number' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data', 'errors' => $validator->errors()], 400);
        }

        try {
            // Check if email is from TUA domain
            if (!str_ends_with($request->customer_email, '@tua.edu.ph')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please use your TUA organizational email (@tua.edu.ph) to login.'
                ], 400);
            }

            // Check if customer already exists or create a new one
            $customer = Customer::firstOrCreate(
                ['customer_email' => $request->customer_email],
                [
                    'customer_name' => $request->customer_name,
                    'customer_password' => Hash::make(Str::random(16)),
                    'customer_picture' => $request->customer_picture,
                    'customer_number' => $request->Customer_Number ?? 'N/A',
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
            // Log the logout attempt for debugging
            \Log::info('Logout attempt', [
                'user_id' => $request->user() ? $request->user()->customer_id : null,
                'has_session' => $request->hasSession(),
            ]);
            
            // Revoke all user tokens
            if ($request->user()) {
                $request->user()->tokens()->delete();
            }
            
            // Clear all guards
            Auth::guard('customer')->logout();
            Auth::guard('web')->logout();
            Auth::guard('sanctum')->logout();
            
            // Clear all session data
            if ($request->hasSession()) {
                $request->session()->flush();
                $request->session()->invalidate();
                $request->session()->regenerate();
            }
            
            // Create an array of cookies to clear
            $cookiesToClear = [
                'laravel_session',
                'XSRF-TOKEN',
                'remember_web',
                'remember_customer',
                'sunblends_session'
            ];
            
            // Build response
            $response = response()->json([
                'success' => true,
                'message' => 'Successfully logged out',
                'cleared_session' => true,
            ]);
            
            // Clear all cookies
            foreach ($cookiesToClear as $cookieName) {
                $response->withCookie(
                    cookie()->forget($cookieName)
                );
                
                // Also clear with explicit domains
                $response->withCookie(
                    cookie($cookieName, '', -1, '/', '.sunblends.store')
                );
                $response->withCookie(
                    cookie($cookieName, '', -1, '/', 'sunblends.store')
                );
                $response->withCookie(
                    cookie($cookieName, '', -1, '/', 'api.sunblends.store')
                );
            }
            
            return $response;
        } catch (\Exception $e) {
            \Log::error('Logout error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}