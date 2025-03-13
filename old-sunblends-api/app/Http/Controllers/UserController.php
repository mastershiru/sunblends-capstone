<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function getUserData(Request $request)
{
    try {
        $request->validate([
            'email' => 'required|email',
        ]);

        $customer = DB::table('customer')->where('Customer_Email', $request->email)->first();

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json([
            'Customer_ID' => $customer->Customer_ID,
            'Customer_Name' => $customer->Customer_Name,
            'Customer_Email' => $customer->Customer_Email,
            'Customer_Number' => $customer->Customer_Number,
            'Customer_Password' => $customer->Customer_Password, // Include password if needed
            'Customer_Img' => $customer->Customer_Img, // Include image field
        ], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Server Error', 'message' => $e->getMessage()], 500);
    }
}

}
