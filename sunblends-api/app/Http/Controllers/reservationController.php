<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Livewire\CustomerReservation;
use App\Models\Reservation;
use App\Models\Customer;

class reservationController extends Controller
{
    public function index()
    {
        return view('reservation');
    }

    public function create(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'customer_id' => 'required|exists:customer,customer_id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required|date_format:H:i',
            'reservation_people' => 'required|integer|min:1|max:20',
            'order_id' => 'nullable|exists:orders,order_id',
        ]);
        
        // Check if time is within business hours (10:00 AM - 5:00 PM)
        $time = \DateTime::createFromFormat('H:i', $validated['reservation_time']);
        $openTime = \DateTime::createFromFormat('H:i', '10:00');
        $closeTime = \DateTime::createFromFormat('H:i', '17:00');
        
        if ($time < $openTime || $time > $closeTime) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation time must be between 10:00 AM and 5:00 PM'
            ], 422);
        }
        
        try {
            // Create a new reservation
            $reservation = Reservation::create([
                'customer_id' => $validated['customer_id'],
                'reservation_date' => $validated['reservation_date'],
                'reservation_time' => $validated['reservation_time'],
                'reservation_people' => $validated['reservation_people'],
                'order_id' => $validated['order_id'] ?? null,
                'reservation_type' => 'dine-in',
                'reservation_status' => 'pending',
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Reservation created successfully',
                'reservation' => $reservation
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create reservation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCustomerReservations($customerId)
{
    try {
        // Validate the customer exists
        $customer = Customer::find($customerId);
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }
        
        // Get all reservations for this customer, newest first
        $reservations = Reservation::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'reservations' => $reservations
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve reservations',
            'error' => $e->getMessage()
        ], 500);
    }
}

// Add this method to cancel a reservation
public function cancelReservation($reservationId)
{
    try {
        $reservation = Reservation::find($reservationId);
        
        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found'
            ], 404);
        }
        
        // Check if reservation can be cancelled
        if ($reservation->reservation_status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Reservation is already cancelled'
            ], 400);
        }
        
        if ($reservation->reservation_status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel a completed reservation'
            ], 400);
        }
        
        // Cancel the reservation
        $reservation->reservation_status = 'cancelled';
        $reservation->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Reservation cancelled successfully',
            'reservation' => $reservation
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to cancel reservation',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
