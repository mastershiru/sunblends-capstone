<?php

namespace App\Http\Controllers;

use App\Models\CustomerNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class NotificationController extends Controller
{
    public function index(Request $request)
{
    $customer_id = $request->input('customer_id');
    $include_details = $request->input('include_details', false);
    
    if (!$customer_id) {
        return response()->json([
            'success' => false,
            'message' => 'Customer ID is required'
        ], 400);
    }
    
    $notifications = CustomerNotification::where('customer_id', $customer_id)
        ->orderBy('created_at', 'desc')
        ->take(50)
        ->get();
    
    $formattedNotifications = $notifications->map(function($notification) use ($include_details) {
        $notificationData = [
            'id' => $notification->id,
            'message' => $notification->message,
            'order_id' => $notification->order_id,
            'status' => $notification->status,
            'timestamp' => $notification->created_at,
            'read' => $notification->isRead(),
        ];
        
        // Include additional data if requested
        if ($include_details && $notification->order_id) {
            $order = Order::find($notification->order_id);
            if ($order) {
                $notificationData['total_price'] = $order->total_price;
                $notificationData['items_count'] = $order->cart->count();
                // Add any other order details you want
            }
        }
        
        // Include any JSON data stored with the notification
        if ($notification->data) {
            $notificationData['data'] = $notification->data;
        }
        
        return $notificationData;
    });
    
    return response()->json([
        'success' => true,
        'notifications' => $formattedNotifications
    ]);
}
    
    public function markAsRead(Request $request, $id)
    {
        $notification = CustomerNotification::findOrFail($id);
        
        // Security check - only the customer can mark their notifications as read
        if ($notification->customer_id != $request->input('customer_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }
    
    public function markAllAsRead(Request $request)
    {
        $customer_id = $request->input('customer_id');
        
        CustomerNotification::where('customer_id', $customer_id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }
}