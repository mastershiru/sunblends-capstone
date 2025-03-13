<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class OtpVerificationController extends Controller
{
    public function show()
    {
        return view('otp-verification');
    }

    public function verify(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'otp' => 'required|numeric'
            ]);

            $otpRecord = Otp::where('email', $request->input('email'))->firstOrFail();
            
            $otp = Crypt::decryptString($otpRecord->otp);

            if ($otp == $request->input('otp') && now()->lessThanOrEqualTo($otpRecord->expires_at)) {
                $user = User::where('email', $request->input('email'))->first();
                if ($user) {
                    $user->email_verified_at = now();
                    $user->save();
                }

                $otpRecord->delete(); // Clear OTP record after successful verification

                return response()->json(['message' => 'Email verified successfully!'], 200);
            }

            return response()->json(['errors' => ['otp' => 'Invalid OTP or OTP expired']], 400);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['errors' => ['otp' => 'An error occurred during OTP verification']], 500);
        }
    }
}
