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
    
    /**
     * Format date for better readability
     */
    public function formatDate($date)
    {
        if (!$date) return 'N/A';
        
        return Carbon::parse($date)->format('M d, Y h:i A');
    }
    
    /**
     * Format date without time
     */
    public function formatDateOnly($date)
    {
        if (!$date) return 'N/A';
        
        return Carbon::parse($date)->format('M d, Y');
    }
    
    /**
     * Format time only
     */
    public function formatTimeOnly($date)
    {
        if (!$date) return 'N/A';
        
        return Carbon::parse($date)->format('h:i A');
    }
    
    /**
     * Get time remaining until pickup
     */
    public function getTimeUntilPickup($pickupTime)
    {
        if (!$pickupTime) return null;
        
        $now = Carbon::now();
        $pickup = Carbon::parse($pickupTime);
        
        if ($now->gt($pickup)) {
            return 'Past pickup time';
        }
        
        $diff = $now->diff($pickup);
        
        if ($diff->d > 0) {
            return $diff->d . ' day(s), ' . $diff->h . ' hour(s), ' . $diff->i . ' minute(s)';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hour(s), ' . $diff->i . ' minute(s)';
        } else {
            return $diff->i . ' minute(s)';
        }
    }
    
    /**
     * Get appropriate status color
     */
    public function getStatusColor($status)
    {
        switch (strtolower($status)) {
            case 'pending':
                return 'bg-yellow-100 text-yellow-800 border-yellow-200';
            case 'processing':
                return 'bg-blue-100 text-blue-800 border-blue-200';
            case 'completed':
                return 'bg-green-100 text-green-800 border-green-200';
            case 'cancelled':
                return 'bg-red-100 text-red-800 border-red-200';
            case 'ready':
                return 'bg-purple-100 text-purple-800 border-purple-200';
            default:
                return 'bg-gray-100 text-gray-800 border-gray-200';
        }
    }
    
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