<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Dish;
use App\Models\Cart;
use App\Models\Order;
use Livewire\Attributes\Validate;
use Livewire\Features\SupportRedirects\Redirector;


class DineInOrders extends Component
{
    public $dish;
    public $food;

    public $cart;
    public $cartItems;
    public $customer;
    public $total_price;
    public $payment_method;
    public $categories = [];


    #[Validate('required|string|min:3')]
    public $customer_name;

    


    public $total = 0;

    public function mount()
    {
        $this->loadCart();
       
    }

    public function decrementQuantity($id)
    {
        $this->cart = Cart::where('cart_id', $id)->first();
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
        $this->cart->quantity = $this->cart->quantity + 1;
        $this->cart->save();


        $this->loadCart();
    }

    public function getTotal()
    {
        $this->total = 0;
        $getTotal = Cart::where('guest_name', $this->customer_name)->get();
        foreach ($this->cartItems as $cartItem) {
            $this->total += $cartItem->dishes->Price * $cartItem->quantity;
        }
    }

    
    public function addToOrder($dishId)
    {
        
        $this->validate();
        try {
            

            $this->food = Dish::find($dishId);
            $existingCartItem = Cart::where('dish_id', $this->food->dish_id)->first();
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
            session()->flash('error', 'An error occurred while adding the dish to the order.');
        }
    }

    public function proceedOrder()
    {
        $this->customer = $this->customer_name;
        $this->total_price = 0;
        $getTotal = Cart::where('guest_name', $this->customer_name)->get();
        foreach ($this->cartItems as $cartItem) {
            $this->total_price += $cartItem->dishes->Price * $cartItem->quantity;
        }

        $orderData = [
            'guest_name' => $this->customer,
            'total_price' => $this->total_price,
            'type_order' => 'walk-in',
        ];

        if($this->payment_method === 'Cash')
        {
            $orderData['payment_method'] = $this->payment_method;
        }
        else
        {
            $orderData['payment_method'] =  $this->payment_method;
        }

        $order = Order::create($orderData);

        Cart::where('guest_name', $this->customer)->update([
            'order_id' => $order->order_id,  
        ]);


        Cart::where('guest_name', $this->customer)->delete();
        
        $this->loadCart();

        return redirect('/Orders-Queue');
       
    }
    
    

    public function loadCart()
    {
        $this->dish = Dish::all();
        $this->cartItems = Cart::all();
        $this->getTotal();
    }
    
    public function render()
    {
        return view('livewire.dine-in-orders', [
            'dishes' => $this->dish,
            'orderItems' => $this->cartItems,
            'total' => $this->total
        ]);
    }
}
