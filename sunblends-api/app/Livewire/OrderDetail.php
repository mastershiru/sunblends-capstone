<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On; 
use App\Models\Order;
use App\Models\Cart;

class OrderDetail extends Component
{
    public $isDetailModalOpen = false;
    public $customerInfo;
    public $cartItems;


    public function openDetailModal()
    {
        $this->isDetailModalOpen = true;
    }

    public function closeDetailModal()
    {
        $this->isDetailModalOpen = false;
    }

    #[On('viewDetail')]
    public function ViewDetails($id)
    {
        $this->openDetailModal();
        $this->customerInfo = Order::with(['customer'])->find($id);
        $this->cartItems = Cart::withTrashed()->where('order_id', $id)
                           ->with('dishes')
                           ->get();
    }



    public function render()
    {
        return view('livewire.order-detail');
    }
}
