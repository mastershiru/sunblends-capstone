<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->input('email');
        $otp = rand(100000, 999999);

        try {
            // Encrypt OTP and save in the database
            Otp::updateOrCreate(
                ['email' => $email],
                ['otp' => Crypt::encryptString($otp), 'expires_at' => now()->addMinutes(10)]
            );

            // Find the user and send OTP notification
            $user = User::where('email', $email)->first();
            $user->notify(new OtpNotification($otp));

            return response()->json(['message' => 'OTP sent successfully.']);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error sending OTP: ' . $e->getMessage());

            return response()->json(['message' => 'Failed to send OTP.'], 500);
        }
    }
}