<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart;
use App\Models\Dish;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction; 


class CartOrder extends Component
{

    
    public $openCart = false;
    public $cartItems = [];
    public $total_price = 0;
    public $cart;
    public $customer;
    public $dish;
    public $cartUpdate;

    public $is_pickup = '';
    public $is_delivery = '';

    public $deliveryOption = '';  
    public $address = '';
    public $pickup_in = '';
    public $payment_method = '';

    protected $listeners = 
    [
    
    'openCart' => 'CartOpen', 
    'closeCart' => 'CartClose'
    ];


    public function CartOpen()
    {
        $this->openCart = true;
        if(Auth::guard('customer')->check())
        {
        $this->loadCartOrder();
        }
    }

    public function CartClose()
    {
        $this->openCart = false;
    }

    public function deliverOption()
    {
        
        $this->deliveryOption = 'delivery';
    }

    public function pickupOption()
    {
        $this->deliveryOption  = 'pickup';
    }


    public function mount()
    {
        if(Auth::guard('customer')->check())
        {
            $this->customer = Auth::guard('customer')->user()->customer_id;   
            $this->cart = Cart::with('dishes')->where('customer_id', $this->customer)->get();
        }
        
    }

    public function removeFromCart($id)
    {
        $this->cart = Cart::where('cart_id', $id)->first();
        if($this->cart)
        {
            $this->cart->forceDelete();
        }
        $this->loadCartOrder();
    }

    public function incrementQuantity($id)
    {
        $this->cart = Cart::where('cart_id', $id)->first();
        if($this->cart)
        {
            $this->cart->quantity += 1;
            $this->cart->save();
        }
        $this->loadCartOrder();
    }

    public function decrementQuantity($id)
    {
        $this->cart = Cart::where('cart_id', $id)->first();
        if($this->cart)
        {
            $this->cart->quantity -= 1;
            if ($this->cart->quantity <= 0) {
                $this->cart->forceDelete();
            } else {
                $this->cart->save();
            }
        }
        $this->loadCartOrder();
    }
    
    
    public function loadCartOrder()
    {
        $this->customer = Auth::guard('customer')->user()->customer_id;   
        $this->cart = Cart::where('customer_id', $this->customer)->get();
        
    }

    private function calculateTotal()
    {
        $this->total_price = collect($this->cartItems)->sum(function ($item) {
            return $item['product']['Price'] * $item['quantity'];
        });
    }

    
    public function checkout()
    {
        // Validate customer is logged in
        if (!Auth::guard('customer')->check()) {
            session()->flash('error', 'Please log in to checkout');
            return;
        }
        
        // Validate required fields
        if (empty($this->payment_method)) {
            session()->flash('error', 'Please select a payment method');
            return;
        }
        
        if ($this->deliveryOption === 'delivery' && empty($this->address)) {
            session()->flash('error', 'Please provide a delivery address');
            return;
        }
        
        if ($this->deliveryOption === 'pickup' && empty($this->pickup_in)) {
            session()->flash('error', 'Please provide a pickup time');
            return;
        }
        
        // Get customer ID and cart items
        $this->customer = Auth::guard('customer')->user()->customer_id;
        $this->cart = Cart::where('customer_id', $this->customer)->get();
        
        // Check if cart is empty
        if ($this->cart->isEmpty()) {
            session()->flash('error', 'Your cart is empty');
            return;
        }
        
        // Calculate total price
        $this->total_price = collect($this->cart)->sum(function ($item) {
            return $item->dishes->Price * $item->quantity;
        });

        // Begin database transaction
        DB::beginTransaction();
        
        try {
            // Create order data
            $orderData = [
                'customer_id' => $this->customer,
                'total_price' => $this->total_price,
                'type_order' => 'online',
                'order_status' => 'pending',
                'payment_method' => $this->payment_method,
            ];

            // Add delivery/pickup details
            if ($this->deliveryOption === 'delivery') {
                $orderData['delivery_option'] = 'delivery';
                $orderData['address'] = $this->address;
            } else {
                $orderData['delivery_option'] = 'pickup';
                $orderData['pickup_in'] = $this->pickup_in;
            }

            // Create the order
            $order = Order::create($orderData);

            // Update cart items to link them to this order
            Cart::where('customer_id', $this->customer)->update([
                'order_id' => $order->order_id,  
            ]);

            // Record transaction
            $transaction = $this->recordTransaction($order->order_id);
            
            if (!$transaction) {
                DB::rollBack();
                session()->flash('error', 'Failed to create transaction record');
                return;
            }
            
            // Delete cart items
            Cart::where('customer_id', $this->customer)->delete();
            
            // Commit the transaction if everything succeeded
            DB::commit();
            
            // Reset component state
            $this->reset(['is_pickup', 'is_delivery', 'deliveryOption', 'address', 'pickup_in', 'payment_method']);
            $this->loadCartOrder();
            
            // Close cart modal
            $this->CartClose();
            
            // Flash success message
            session()->flash('success', 'Order placed successfully! Order #' . $order->order_id);
            
            // Redirect to order confirmation page (optional)
            return redirect()->route('order.confirmation', ['order_id' => $order->order_id]);
            
        } catch (\Exception $e) {
            // Roll back the transaction if anything fails
            DB::rollBack();
            session()->flash('error', 'Failed to process order: ' . $e->getMessage());
        }
    }

    
    public function recordTransaction($orderId)
    {
        try {
            // Find the order by ID
            $order = Order::find($orderId);
            
            if (!$order) {
                session()->flash('error', 'Order not found');
                return null;
            }
            
            // Generate transaction reference
            $transactionReference = 'TRX-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT) . date('ymd');
            
            // Set transaction status based on payment method
            $transactionStatus = 'pending'; // Online orders default to pending
            
            if ($this->payment_method === 'Cash') {
                // Cash on delivery will be completed when delivered
                $transactionStatus = 'pending';
            }
            
            // Create transaction record
            $transaction = Transaction::create([
                'transaction_reference' => $transactionReference,
                'order_id' => $order->order_id,
                'customer_id' => $this->customer, // Link to customer record
                'cash_amount' => $order->total_price, // For online orders, cash amount equals total price
                'change_amount' => 0, // No change for online orders
                'transaction_status' => $transactionStatus,
                'transaction_date' => now(),
            ]);
            
            return $transaction;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to record transaction: ' . $e->getMessage());
            return null;
        }
    }


    public function render()
    {

        return view('livewire.cart-order',
        [
            'cartItems' => $this->cartItems,
            'total' => $this->total_price,
            'cart' => $this->cart,
            'customer' => $this->customer

        ]);
    }
}

