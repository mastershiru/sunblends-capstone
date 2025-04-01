<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transaction;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;


class TransactionModal extends Component
{
    use WithPagination;
    
    // Modal properties
    public $isDetailModalOpen = false;
    public $currentTransaction = null;
    
    protected $listeners = [
        'openTransactionModal' => 'viewTransactionDetails',
        'closeTransactionModal' => 'closeDetailModal'
    ];
    
   
    public function viewTransactionDetails($transactionId)
    {
        // Get transaction with order, and eager load cart items with dishes
        $this->currentTransaction = Transaction::with([
            'order', 
            'customer',
            'order.cart' => function($query) {
                // This is the key change - include soft deleted cart items
                $query->withTrashed()->with('dishes');
            }
        ])->find($transactionId);
 
        
        $this->isDetailModalOpen = true;
    }
    
    /**
     * Close transaction detail modal
     */
    public function closeDetailModal()
    {
        $this->isDetailModalOpen = false;
        $this->currentTransaction = null;
    }

    
    public function render()
    {
        return view('livewire.transaction-modal');
    }
}