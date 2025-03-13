<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller
{
    // Create an order
    public function checkout(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'orderDateTime' => 'required|date',
            'orderStatus' => 'required|string',
            'orderType' => 'required|string',
            'paymentMethod' => 'required|string',
            'deliveryMethod' => 'required|string',
            'totalAmount' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        // Get customer details
        $customer = DB::table('customer')->where('Customer_Email', $request->email)->first();
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        // Insert order
        $orderID = DB::table('orders')->insertGetId([
            'Customer_ID' => $customer->Customer_ID,
            'Order_DateTime' => Carbon::parse($request->orderDateTime),
            'Order_Status' => $request->orderStatus,
            'Order_Type' => $request->orderType,
            'Payment_Method' => $request->paymentMethod,
            'Delivery_Method' => $request->deliveryMethod,
            'Total_Payment' => $request->totalAmount,
            'Notes' => $request->notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Order placed successfully', 'Order_ID' => $orderID], 200);
    }

    // Insert order items
    public function insertOrderItem(Request $request)
    {
        $request->validate([
            'orderID' => 'required|integer|exists:orders,Order_ID',
            'customerEmail' => 'required|email',
            'customerName' => 'required|string',
            'itemImg' => 'nullable|string',
            'itemTitle' => 'required|string',
            'itemQuantity' => 'required|integer',
            'itemPrice' => 'required|numeric',
        ]);

        DB::table('order_items')->insert([
            'Order_ID' => $request->orderID,
            'Customer_Email' => $request->customerEmail,
            'Customer_Name' => $request->customerName,
            'Customer_Number' => '09123456789', // Example placeholder
            'Item_Img' => $request->itemImg,
            'Item_Title' => $request->itemTitle,
            'Item_Quantity' => $request->itemQuantity,
            'Item_Price' => $request->itemPrice,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Order item added successfully'], 200);
    }

    public function getOrderDetails($orderId)
    {
        try {
            // Fetch order and items
            $order = Order::where('Order_ID', $orderId)->first();
            $orderItems = OrderItem::where('Order_ID', $orderId)->get();

            // Check if order exists
            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            return response()->json([
                'orderStatus' => $order->Order_Status,
                'items' => $orderItems
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getHistoryOrders(Request $request)
    {
        try{
            $request->validate([
                'email' => 'required|email',
            ]);

             // Fetch orders where Customer_ID matches the email (adjust if needed)
             $orders = Order::join('customer', 'orders.Customer_ID', '=', 'customer.Customer_ID')
             ->where('customer.Customer_Email', $request->email)
             ->orderBy('orders.Order_DateTime', 'desc')
             ->select('orders.*') // Select all order fields
             ->get();

         if ($orders->isEmpty()) {
             return response()->json(['message' => 'No orders found.'], 404);
         }

         return response()->json($orders, 200);
     } catch (\Exception $e) {
         return response()->json(['error' => 'Server Error', 'message' => $e->getMessage()], 500);
     }
 }
        }
    
    
    

