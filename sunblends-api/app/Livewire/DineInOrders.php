<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Dish;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Transaction;
use Livewire\Attributes\Validate;
use Livewire\Features\SupportRedirects\Redirector;
use Illuminate\Support\Facades\DB;

class DineInOrders extends Component
{
    public $dish;
    public $food;

    public $cart;
    public $cartItems;
    public $customer;
    public $total_price;
    public $payment_method;
    public $cash = 0;
    public $change = 0;
    public $categories = [];

    #[Validate('required|string|min:3')]
    public $customer_name;

    public $total = 0;

    public function mount()
    {
        $this->loadCart();
    }

    public function updatedCash()
    {
        
        $this->getChange();
    }
    
    public function updatedCustomerName()
    {
        $this->loadCart();
    }

    public function decrementQuantity($id)
    {
        $this->cart = Cart::where('cart_id', $id)->first();
        
        if (!$this->cart) {
            session()->flash('error', 'Cart item not found.');
            return;
        }
        
        $this->cart->quantity = $this->cart->quantity - 1;

        if ($this->cart->quantity <= 0) {
            $this->cart->forceDelete();
        } else {
            $this->cart->save();
        }

       $this->loadCart();
    }

    public function incrementQuantity($id)
    {
        $this->cart = Cart::where('cart_id', $id)->first();
        
        if (!$this->cart) {
            session()->flash('error', 'Cart item not found.');
            return;
        }
        
        $this->cart->quantity = $this->cart->quantity + 1;
        $this->cart->save();

        $this->loadCart();
    }

    public function getTotal()
    {
        $this->total = 0;
        
        foreach ($this->cartItems as $cartItem) {
            if ($cartItem->dishes) { // Check to prevent null reference errors
                $this->total += $cartItem->dishes->Price * $cartItem->quantity;
            }
        }
        
        return $this->total;
    }

    public function getChange()
    {
        
        $this->change = (float)$this->cash - (float)$this->total;
        
        if ($this->change < 0) {
            $this->change = 0;
        }
        
        return $this->change;
    }

    public function addToOrder($dishId)
    {
        $this->validate();
        
        try {
            $this->food = Dish::find($dishId);
            
            if (!$this->food) {
                session()->flash('error', 'Dish not found.');
                return;
            }
            
            $existingCartItem = Cart::where('dish_id', $this->food->dish_id)
                                    ->where('guest_name', $this->customer_name)
                                    ->first();
            
            $guest_name = $this->customer_name;

            if ($existingCartItem) {
                $existingCartItem->quantity += 1;
                $existingCartItem->save();
            } else {
                $this->cart = Cart::create([
                    'dish_id' => $this->food->dish_id,
                    'quantity' => 1,
                    'guest_name' => $guest_name
                ]);
            }

            $this->loadCart();

            session()->flash('message', 'Dish added to order successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while adding the dish to the order: ' . $e->getMessage());
        }
    }

    public function proceedOrder()
    {
        // Validate required fields
        $this->validate([
            'customer_name' => 'required|string|min:3',
            'payment_method' => 'required',
        ]);
        
        // For cash payments, validate that cash amount is sufficient
        if ($this->payment_method === 'Cash' && (float)$this->cash < $this->total) {
            session()->flash('error', 'Cash amount must be equal to or greater than the total.');
            return;
        }
        
        $this->customer = $this->customer_name;
        $this->total_price = 0;
        
        // Get cart items for this specific customer
        $cartItems = Cart::where('guest_name', $this->customer_name)->get();
        
        // Check if cart is empty
        if ($cartItems->isEmpty()) {
            session()->flash('error', 'Cart is empty. Please add items before proceeding.');
            return;
        }
        
        // Calculate total price
        foreach ($cartItems as $cartItem) {
            if ($cartItem->dishes) {
                $this->total_price += $cartItem->dishes->Price * $cartItem->quantity;
            }
        }
        
        // Begin database transaction
        DB::beginTransaction();
        
        try {
            // Create order data
            $orderData = [
                'guest_name' => $this->customer,
                'total_price' => $this->total_price,
                'type_order' => 'walk-in',
                'payment_method' => $this->payment_method, 
                'order_status' => 'pending',
            ];

            // Create the order
            $order = Order::create($orderData);

            // Update cart items to link them to this order
            Cart::where('guest_name', $this->customer)->update([
                'order_id' => $order->order_id,  
            ]);

            // Record the transaction
            $transaction = $this->recordTransaction($order->order_id);
            
            if (!$transaction) {
                DB::rollBack();
                session()->flash('error', 'Failed to create transaction record.');
                return;
            }
            
            // Delete cart items
            Cart::where('guest_name', $this->customer)->delete();
            
            // Commit the transaction if everything succeeded
            DB::commit();
            
            // Reset the form
            $this->reset(['customer_name', 'payment_method', 'cash', 'change']);
            $this->loadCart();

            // Flash success message
            session()->flash('success', 'Order processed successfully!');
            
            // Redirect to the orders queue page
            return redirect('/Orders-Queue');
            
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
                session()->flash('error', 'Order not found.');
                return null;
            }
            
            $transactionReference = 'TRX-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT) . date('ymd');
        
            
            // Create transaction record
            $transaction = Transaction::create([
                'transaction_reference' => $transactionReference,
                'order_id' => $order->order_id,
                'customer_id' => null, // For walk-in orders with guest_name, customer_id might be null
                'cash_amount' => $this->cash,
                'change_amount' => $this->change,
                'transaction_status' => 'completed', // Default to completed for walk-in orders
                'transaction_date' => now(),
            ]);


            
            return $transaction;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to record transaction: ' . $e->getMessage());
            return null;
        }
    }

    public function loadCart()
    {
        $this->dish = Dish::all();
        
        // Only load cart items for the current customer
        if (!empty($this->customer_name)) {
            $this->cartItems = Cart::where('guest_name', $this->customer_name)
                                   ->with('dishes') // Eager load dishes to prevent N+1 query problem
                                   ->get();
        } else {
            $this->cartItems = collect(); // Empty collection if no customer name set
        }
        
        $this->getTotal();
        $this->getChange();
    }
    
    public function render()
    {
        return view('livewire.dine-in-orders', [
            'dishes' => $this->dish,
            'orderItems' => $this->cartItems,
            'total' => $this->total,
            'change' => $this->change,
        ]);
    }
}