<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Refresh the user's session based on customer_id
     */
    public function refreshSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $customer = Customer::find($request->customer_id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }
        
        // PRIORITIZE CUSTOMER-TOKEN CHECK
        $hasCustomerToken = $customer->tokens()->where('name', 'customer-token')->exists();
        
        // Log for debugging
        \Log::info("Token refresh request", [
            'customer_id' => $customer->customer_id,
            'has_customer_token' => $hasCustomerToken
        ]);
        
        // If customer-token doesn't exist, and this is a refresh attempt (not initial login),
        // require a full re-authentication
        if (!$hasCustomerToken && $request->has('is_refresh') && $request->is_refresh) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please login again',
                'require_reauth' => true
            ], 401);
        }
        
        // Only check expiration if customer-token exists
        if ($hasCustomerToken) {
            // Check if tokens are expired based on dates
            $tokenMaxAge = config('sanctum.expiration', 60 * 24); // Default 24 hours in minutes
            $tokenCreationThreshold = now()->subMinutes($tokenMaxAge);
            
            $hasValidCustomerToken = $customer->tokens()
                ->where('name', 'customer-token')
                ->where('created_at', '>=', $tokenCreationThreshold)
                ->exists();
            
            if (!$hasValidCustomerToken && $request->has('is_refresh') && $request->is_refresh) {
                // Customer token exists but is expired, delete it and require re-auth
                $customer->tokens()->where('name', 'customer-token')->delete();
                
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired due to inactivity. Please login again',
                    'require_reauth' => true
                ], 401);
            }
        }
        
        // If we got here, either:
        // 1. The customer-token exists and is valid, or
        // 2. This isn't a refresh request (it's an initial login)
        
        // Clean up tokens if requested to prevent accumulation
        $recentTokens = $customer->tokens()
        ->where('name', 'customer-token')
        ->orderByDesc('created_at')
        ->take(2)
        ->pluck('id');
        
        // Delete all other customer-tokens
        $customer->tokens()
        ->where('name', 'customer-token')
        ->whereNotIn('id', $recentTokens)
        ->delete();

        if ($request->has('clean_tokens') && $request->clean_tokens) {
            // Delete all tokens EXCEPT customer-token
            $customer->tokens()->where('name', '!=', 'customer-token')->delete();
        }
        
        // Always create a new customer-token (previous one remains valid until expiry)
        $token = $customer->createToken('customer-token')->plainTextToken;
        
        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $customer,
            'message' => 'Session refreshed successfully'
        ]);
    }

    public function validateToken(Request $request)
    {
        // Get the auth token from the header
        $bearerToken = $request->bearerToken();
        
        if (!$bearerToken) {
            return response()->json([
                'valid' => false,
                'message' => 'No token provided',
                'require_reauth' => true
            ], 401);
        }
        
        // Get token parts - Laravel Sanctum tokens have format: "id|token_hash"
        $tokenParts = explode('|', $bearerToken);
        
        // Check if the token has the correct format
        if (count($tokenParts) !== 2) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid token format',
                'require_reauth' => true
            ], 401);
        }
        
        $tokenId = $tokenParts[0];
        $tokenHash = hash('sha256', $tokenParts[1]); // Hash the second part
        
        // Find the token in the database directly
        $token = \Laravel\Sanctum\PersonalAccessToken::where('id', $tokenId)
            ->where('token', $tokenHash)
            ->first();
        
        if (!$token) {
            return response()->json([
                'valid' => false,
                'message' => 'Token not found in database',
                'require_reauth' => true
            ], 401);
        }
        
        // IMPORTANT: Check if this is specifically a customer-token
        // This is the key part that matches your desired logic
        if ($token->name !== 'customer-token') {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid token type',
                'require_reauth' => true
            ], 401);
        }
        
        // Extra check - get the user/customer associated with the token
        $tokenable = $token->tokenable;
        
        if (!$tokenable) {
            return response()->json([
                'valid' => false,
                'message' => 'Token owner not found',
                'require_reauth' => true
            ], 401);
        }
        
        // If we made it here, the token is valid and exists in the database
        return response()->json([
            'valid' => true,
            'user_id' => $tokenable->customer_id ?? $tokenable->id
        ]);
    }

    public function checkTokenType(Request $request)
    {
        // Get the auth token from the request
        $bearerToken = $request->header('Authorization');
        
        if (!$bearerToken || !str_starts_with($bearerToken, 'Bearer ')) {
            return response()->json([
                'valid' => false,
                'message' => 'No token provided',
                'require_reauth' => true
            ], 401);
        }
        
        // Extract token from Authorization header
        $bearerToken = trim(str_replace('Bearer ', '', $bearerToken));
        
        // Get token parts - Laravel Sanctum tokens have format: "id|token_hash"
        $tokenParts = explode('|', $bearerToken);
        
        // Check if the token has the correct format
        if (count($tokenParts) !== 2) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid token format',
                'require_reauth' => true
            ], 401);
        }
        
        $tokenId = $tokenParts[0];
        
        // Find the token in the database directly
        $token = \Laravel\Sanctum\PersonalAccessToken::find($tokenId);
        
        if (!$token) {
            return response()->json([
                'valid' => false,
                'message' => 'Token not found in database',
                'require_reauth' => true
            ], 401);
        }
        
        // Check if this is a customer-token
        return response()->json([
            'valid' => $token->name === 'customer-token',
            'token_type' => $token->name,
            'user_id' => $token->tokenable_id
        ]);
    }

    public function checkToken(Request $request)
    {
        // Get the auth token from the request
        $bearerToken = $request->header('Authorization');
        
        if (!$bearerToken || !str_starts_with($bearerToken, 'Bearer ')) {
            return response()->json([
                'valid' => false,
                'message' => 'No token provided',
                'require_reauth' => true
            ], 401);
        }
        
        // Extract token from Authorization header
        $bearerToken = trim(str_replace('Bearer ', '', $bearerToken));
        
        // Get token parts - Laravel Sanctum tokens have format: "id|token_hash"
        $tokenParts = explode('|', $bearerToken);
        
        // Check if the token has the correct format
        if (count($tokenParts) !== 2) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid token format',
                'require_reauth' => true
            ], 401);
        }
        
        $tokenId = $tokenParts[0];
        $tokenHash = hash('sha256', $tokenParts[1]); // Hash the second part
        
        // Find the token in the database directly
        $token = \Laravel\Sanctum\PersonalAccessToken::where('id', $tokenId)
            ->where('token', $tokenHash)
            ->first();
        
        if (!$token) {
            return response()->json([
                'valid' => false,
                'message' => 'Token not found in database',
                'require_reauth' => true
            ], 401);
        }
        
        // Token exists and is valid
        return response()->json([
            'valid' => true,
            'user_id' => $token->tokenable_id
        ]);
    }

    public function revokeTokens(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['success' => true]);
    }

}