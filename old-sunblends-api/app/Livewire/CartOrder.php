<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart;
use App\Models\Dish;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate; 


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
        $this->customer = Auth::guard('customer')->user()->customer_id;
        $this->cart = Cart::where('customer_id', $this->customer)->get();
        $this->total_price = collect($this->cart)->sum(function ($item) {
            return $item->dishes->Price * $item->quantity;
        });

        $orderData = [
            'customer_id' => $this->customer,
            'total_price' => $this->total_price,
            'type_order' => 'online',
        ];

        if ($this->is_delivery === 'delivery') {
            $orderData['delivery_option'] = $this->is_delivery;
            $orderData['address'] = $this->address;
        } else {
            $orderData['delivery_option'] = $this->is_pickup;
            $orderData['pickup_in'] = $this->pickup_in;
        }

        if($this->payment_method === 'Cash')
        {
            $orderData['payment_method'] = $this->payment_method;
        }
        else
        {
            $orderData['payment_method'] =  $this->payment_method;
        }

        $order = Order::create($orderData);

        Cart::where('customer_id', $this->customer)->update([
            'order_id' => $order->order_id,  
        ]);


        Cart::where('customer_id', $this->customer)->delete();
        $this->loadCartOrder();
       
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

