<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Cart;


class OrderQueue extends Component
{

    
    public $orders;
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

    public function updateStatus($id, $status)
    {
        $order = Order::find($id);
        if ($order) {
            $order->update([
                'status_order' => $status
            ]);
            $this->loadOrders();
        }
    }

    public function ViewDetails($id)
    {
        $this->openDetailModal();
        $this->customerInfo = Order::with(['customer'])->find($id);
        $this->cartItems = Cart::withTrashed()->where('order_id', $id)
                           ->with('dishes')
                           ->get();
    }

    public function mount()
    {
        $this->loadOrders();
    }

    public function loadOrders()
    {
        $this->orders = Order::orderBy('created_at', 'asc')->get();
    }

    public function render()
    {
        return view('livewire.order-queue',
        [
            'orders' => $this->orders,
            'customerInfo' => $this->customerInfo,
            'cartItems' => $this->cartItems,
        ]);
    }
}
