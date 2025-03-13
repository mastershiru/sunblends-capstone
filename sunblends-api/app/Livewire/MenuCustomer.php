<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Dish;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class MenuCustomer extends Component
{
    public $dishes;
    public $cart;
    public $isCustomerLogin;

    public $categories = [];
    public $search = '';

    public $dish_name;
    public $dish_picture;
    public $category;
    public $dish_available;
    public $Price;

    public function mount()
    {
        $this->loadDish();
        
    }

    public function loadDish()
    {
        $this->dishes = $this->searchDishes();
        $this->dishes = Dish::all();
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = Dish::select('category')->distinct()->get();
    }

    public function closeCustomerModal()
    {
        $this->isCustomerLogin = false;
    }

    public function filterCategory($category)
    {
        $this->dishes = Dish::where('category', $category)->get();
        $this->loadCategories();
    }

    public function updatedSearch()
    {
        $this->dishes = $this->searchDishes();
        $this->loadCategories();
    }

    private function searchDishes()
    {
        return Dish::when($this->search, function ($query) {
            return $query->where('dish_name', 'like', '%' . $this->search . '%');
        })->get();
    }

    public function addItem($id)
    {
        try {
            if(Auth::guard('customer')->check())
            {
                $dish = Dish::findOrFail($id);
                $customer_id = Auth::guard('customer')->user()->customer_id;

                $carts = Cart::where('dish_id', $dish->dish_id)
                            ->where('customer_id', $customer_id)
                            ->first();

                if ($carts) 
                {
                    $carts->quantity += 1;
                    $carts->save();
                    $this->cart = $carts;
                } 
                else 
                {
                    $this->cart = Cart::create([
                        'dish_id' => $dish->dish_id,
                        'quantity' => 1,
                        'customer_id' => $customer_id
                    ]);
                }
                $this->dispatch('updateCount');
            }
            else
            {
                $this->isCustomerLogin = true;
            }
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error adding item to cart: ' . $e->getMessage());
        }
    }



    public function render()
    {
        return view('livewire.menu-customer', [
            'dish' => $this->dishes,
            'categories' => $this->categories
        ]);
    }
}
