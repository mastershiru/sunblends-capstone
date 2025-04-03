<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Transaction;


class OrderController extends Controller
{
    public function getCustomerOrders($id)
    {
        $orders = Order::where('customer_id', $id)
            ->orderBy('created_at', 'desc')
            ->with(['cart'])
            ->get()
            ->map(function ($order) {
                // Make sure data is properly formatted
                return [
                    'order_id' => $order->order_id,
                    'customer_id' => $order->customer_id,
                    'status' => $order->status_order ?? 'pending', // Default status if null
                    'total_price' => floatval($order->total_price) ?? 0, // Ensure it's a number
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $order->updated_at->format('Y-m-d H:i:s'),
                    'cart_count' => $order->cart->count(),
                    'cart' => $order->cart,
                    'payment_method' => $order->payment_method,
                    'delivery_option' => $order->delivery_option,
                    'address' => $order->address,
                    'pickup_in' => $order->pickup_in ? $order->pickup_in->format('Y-m-d H:i:s') : null,
                ];
            });
        
        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }
    
    public function show($id)
    {
        $order = Order::with(['customer'])->find($id);
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
        
        // Format the order data to ensure proper values
        $formattedOrder = [
            'order_id' => $order->order_id,
            'customer_id' => $order->customer_id,
            'status' => $order->status_order ?? 'pending', // Default status if null
            'total_price' => floatval($order->total_price) ?? 0, // Ensure it's a number
            'created_at' => $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
            'updated_at' => $order->updated_at ? $order->updated_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
            'customer' => $order->customer ? [
                'Customer_ID' => $order->customer->customer_id,
                'Customer_Name' => $order->customer->customer_name ?? 'Unknown',
                'Customer_Email' => $order->customer->customer_email ?? 'Not provided',
                'Customer_Number' => $order->customer->customer_number ?? 'Not provided'
            ] : null
        ];
        
        return response()->json($formattedOrder);
    }

    public function getOrderItems($id)
    {
        $cartItems = Cart::withTrashed()
            ->where('order_id', $id)
            ->with('dishes')
            ->get();
        
        $formattedItems = $cartItems->map(function ($cartItem) {
            // Check if dishes relation exists
            if (!$cartItem->dishes) {
                return [
                    'Item_ID' => $cartItem->cart_id,
                    'Item_Title' => 'Unknown Item',
                    'Item_Price' => 0,
                    'Item_Quantity' => $cartItem->quantity,
                    'Item_Category' => 'Unknown',
                    'Item_Img' => $cartItem->dishes->dish_picture
                ];
            }
            
            return [
                'Item_ID' => $cartItem->cart_id,
                'Item_Dish_ID' => $cartItem->dishes->dish_id,
                'Item_Title' => $cartItem->dishes->dish_name ?? 'Unknown Item',
                'Item_Price' => floatval($cartItem->dishes->Price) ?? 0,
                'Item_Quantity' => intval($cartItem->quantity) ?? 1,
                'Item_Category' => $cartItem->dishes->category ?? 'Unknown',
                'Item_Img' => asset($cartItem->dishes->dish_picture)
            ];
        });
        
        return response()->json([
            'success' => true,
            'items' => $formattedItems
        ]);
    }

    public function cancelOrder($id)
    {
        try {
            $order = Order::findOrFail($id);
            
            // Check if order can be cancelled (only pending or processing orders)
            if (!in_array(strtolower($order->status_order), ['pending', 'processing', null])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order cannot be cancelled due to its current status.'
                ], 400);
            }
            
            // Update order status
            $order->status_order = 'cancelled';
            $order->save();
            
            // Also update any related transaction status
            if ($transaction = Transaction::where('order_id', $order->order_id)->first()) {
                // Change transaction status from "failed" to "cancelled"
                
            $transaction->transaction_status = 'cancelled';
                
                $transaction->save();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Order successfully cancelled',
                'order' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}