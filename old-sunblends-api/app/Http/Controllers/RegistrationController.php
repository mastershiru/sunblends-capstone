<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Otp;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Crypt;

class RegistrationController extends Controller
{
    public function register(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'number' => 'required|string|max:15',
            'password' => 'required|string|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create a new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'number' => $request->number,
            'password' => Hash::make($request->password),
        ]);

        // Generate and send OTP
        $otp = rand(100000, 999999);
        $otpEncrypted = Crypt::encryptString($otp);

        // Save OTP to database
        Otp::updateOrCreate(
            ['email' => $request->email],
            ['otp' => $otpEncrypted, 'expires_at' => now()->addMinutes(10)]
        );

        // Send OTP via email
        $user->notify(new OtpNotification($otp));

        return response()->json([
            'message' => 'Registration successful. Please check your email to verify your account.',
            'redirect_url' => url('/otp-verification') // Redirect to OTP verification page
        ], 201);
    }
}
