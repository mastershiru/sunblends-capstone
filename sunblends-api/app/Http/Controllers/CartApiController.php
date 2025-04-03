<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Cart;
use App\Models\Dish;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CartApiController extends Controller
{
    /**
     * Get user data by email
     */
    public function getUserData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email format',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = Customer::where('customer_email', $request->email)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'Customer_ID' => $customer->customer_id,
            'Customer_Name' => $customer->customer_name,
            'Customer_Email' => $customer->customer_email,
            'Customer_Number' => $customer->customer_number
        ]);
    }

    /**
     * Add item to cart
     */
    public function addToCart(Request $request)
    {
        Log::info('Add to cart request', $request->all());
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'dish_id' => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            Log::error('Add to cart validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = Customer::where('customer_email', $request->email)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        $dish = Dish::find($request->dish_id);

        if (!$dish) {
            return response()->json([
                'success' => false,
                'message' => 'Dish not found'
            ], 404);
        }

        // Check if item already exists in cart
        $existingCartItem = Cart::where('customer_id', $customer->customer_id)
            ->where('dish_id', $request->dish_id)
            ->where('order_id', null) 
            ->first();

        if ($existingCartItem) {
            // Update quantity if already in cart
            $existingCartItem->quantity += $request->quantity;
            $existingCartItem->save();
        } else {
            // Add new item to cart
            Cart::create([
                'customer_id' => $customer->customer_id,
                'dish_id' => $request->dish_id,
                'quantity' => $request->quantity
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart'
        ]);
    }

    /**
     * Get cart items
     */
    public function getCartItems(Request $request)
    {
        Log::info('Get cart items request', $request->all());
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            Log::error('Get cart items validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid email format',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = Customer::where('customer_email', $request->email)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        $cartItems = Cart::with('dishes')
            ->where('customer_id', $customer->customer_id)
            ->whereNull('order_id') // Only get items that aren't part of an order
            ->get();

        $formattedCartItems = $cartItems->map(function ($item) {
            return [
                'id' => $item->cart_id,
                'dish_id' => $item->dish_id,
                'title' => $item->dishes->dish_name,
                'img' => $item->dishes->dish_picture,
                'price' => (float)$item->dishes->Price,
                'quantity' => $item->quantity,
                'subtotal' => (float)($item->dishes->Price * $item->quantity),
            ];
        });

        $total = $formattedCartItems->sum('subtotal');

        return response()->json([
            'success' => true,
            'cart_items' => $formattedCartItems,
            'total' => $total
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateCartItem(Request $request)
    {
        Log::info('Update cart item request', $request->all());
        
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            Log::error('Update cart item validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $cartItem = Cart::find($request->cart_id);

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found'
            ], 404);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated'
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeCartItem(Request $request)
    {
        Log::info('Remove cart item request', $request->all());
        
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required'
        ]);

        if ($validator->fails()) {
            Log::error('Remove cart item validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $cartItem = Cart::find($request->cart_id);

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found'
            ], 404);
        }

        $cartItem->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Cart item removed'
        ]);
    }

    /**
     * Checkout and create order
     */
    public function checkout(Request $request)
    {
        Log::info('Checkout request', $request->all());
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'paymentMethod' => 'required|string',
            'deliveryMethod' => 'required|string',
            'totalAmount' => 'required|numeric',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            Log::error('Checkout validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = Customer::where('customer_email', $request->email)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        $cartItems = Cart::with('dishes')
            ->where('customer_id', $customer->customer_id)
            ->whereNull('order_id')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        // Create new order
        $orderData = [
            'customer_id' => $customer->customer_id,
            'total_price' => $request->totalAmount,
            'type_order' => 'online',
            'payment_method' => $request->paymentMethod,
            'delivery_option' => $request->deliveryMethod
        ];

        // Add address or pickup time based on delivery method
        if ($request->deliveryMethod === 'delivery') {
            $orderData['address'] = $request->notes;
        } else {
            $orderData['pickup_in'] = now()->addMinutes(30)->format('Y-m-d H:i:s'); // Default 30 min pickup time
        }

        // Create order
        $order = Order::create($orderData);
        
        // Link cart items to the order
        Cart::where('customer_id', $customer->customer_id)
            ->whereNull('order_id')
            ->update(['order_id' => $order->order_id]);

        

        // Record transaction in the same request
        try {
            // Generate a unique transaction reference
            $transactionReference = 'TRX-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT) . date('ymd');
            
            // Create transaction record with fields that match your model's fillable array
            Transaction::create([
                'transaction_reference' => $transactionReference,
                'order_id' => $order->order_id,
                'customer_id' => $customer->customer_id,
                'transaction_status' => 'pending',
                'transaction_date' => now(), 
                'cash_amount' => $order->total_price ? $request->totalAmount : 0,
                'change_amount' => 0, 
            ]);
            
            
        } catch (\Exception $e) {
            Log::error('Failed to record transaction', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => $order->order_id
            ]);
            
        }

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully',
            'Order_ID' => $order->order_id,
            'transaction_reference' => $transactionReference ?? null
        ]);
    }



    public function getCartCount($customer_id)
    {
        $cartCount = Cart::where('customer_id', $customer_id)
            ->whereNull('order_id')
            ->sum('quantity'); 

        return response()->json([
            'success' => true,
            'cart_count' => $cartCount
        ]);
    }
}