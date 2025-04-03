<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transaction;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Notifications\OrderStatusChanged;

class TransactionDashboard extends Component
{
    use WithPagination;
    
    // Filtering properties
    public $search = '';
    public $dateRange = 'today';
    public $paymentMethod = '';
    public $status = '';
    public $startDate;
    public $endDate;
    public $perPage = 10;
    
    // Sorting properties
    public $sortField = 'transaction_date';
    public $sortDirection = 'desc';
    
    // Modal properties
    public $isDetailModalOpen = false;
    public $currentTransaction = null;
    
    // Stats properties
    public $totalTransactions = 0;
    public $totalAmount = 0;
    public $todayTransactions = 0;
    public $todayAmount = 0;
    
    public function mount()
    {
        // Set default date ranges
        $this->setDateRange('today');
        
        // Calculate initial stats
        $this->calculateStats();
    }
    
    /**
     * Set date range based on selection
     */
    public function setDateRange($range)
    {
        $this->dateRange = $range;
        
        switch ($range) {
            case 'today':
                $this->startDate = Carbon::today()->format('Y-m-d');
                $this->endDate = Carbon::today()->format('Y-m-d');
                break;
            case 'yesterday':
                $this->startDate = Carbon::yesterday()->format('Y-m-d');
                $this->endDate = Carbon::yesterday()->format('Y-m-d');
                break;
            case 'this_week':
                $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->startDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'custom':
                // Don't change custom dates if already set
                if (!$this->startDate) {
                    $this->startDate = Carbon::today()->format('Y-m-d');
                }
                if (!$this->endDate) {
                    $this->endDate = Carbon::today()->format('Y-m-d');
                }
                break;
            default:
                $this->startDate = null;
                $this->endDate = null;
                break;
        }
        
        $this->calculateStats();
    }
    
    /**
     * Calculate transaction statistics
     */
    public function calculateStats()
    {
        // Total transactions
        $this->totalTransactions = Transaction::count();
        $this->totalAmount = Transaction::where('transaction_status', 'completed')->sum('cash_amount');
        
        // Today's transactions
        $today = Carbon::today()->format('Y-m-d');
        $this->todayTransactions = Transaction::whereDate('transaction_date', $today)->count();
        $this->todayAmount = Transaction::whereDate('transaction_date', $today)
                                        ->where('transaction_status', 'completed')
                                        ->sum('cash_amount');
    }
    
    /**
     * Handle search input updates
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    /**
     * Handle date range changes
     */
    public function updatedDateRange()
    {
        $this->setDateRange($this->dateRange);
        $this->resetPage();
    }
    
    /**
     * Handle payment method filter changes
     */
    public function updatedPaymentMethod()
    {
        $this->resetPage();
    }
    
    /**
     * Handle status filter changes
     */
    public function updatedStatus()
    {
        $this->resetPage();
    }
    
    /**
     * Change sort field and direction
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    
    /**
     * View transaction details
     */
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

    public function updateTransactionStatus($transactionId, $status)
    {
        try {
            $transaction = Transaction::findOrFail($transactionId);
            
            // Validate status
            if (!in_array($status, ['completed', 'pending', 'cancelled'])) {
                throw new \Exception('Invalid status provided');
            }
            
            // Update the status
            $transaction->transaction_status = $status;
            $transaction->save();
            
            // If the transaction is linked to an order, update order status too
            if ($transaction->order_id) {
                $order = Order::find($transaction->order_id);
                if ($order) {
                    $previousStatus = $order->status_order;
                    
                    // Update order status based on payment status
                    if ($status === 'completed') {
                        // Only update if it's not already completed
                        if ($order->status_order !== 'completed') {
                            $order->status_order = 'completed';
                            $order->save();
                        }
                    } elseif ($status === 'failed') {
                        $order->status_order = 'cancelled';
                        $order->save();
                    }
                    
                    // Send notification if order status changed
                    if ($previousStatus !== $order->status_order) {
                        // If this is an online order with a customer
                        if ($order->customer_id) {
                            // Notify the specific customer
                            $customer = Customer::find($order->customer_id);
                            if ($customer) {
                                $customer->notify(new OrderStatusChanged($order));
                            }
                        }
                        
                        // Also broadcast to a general channel for admin notifications
                        Notification::route('broadcast', 'orders')
                            ->notify(new OrderStatusChanged($order));
                    }
                }
            }
            
            session()->flash('success', 'Transaction status updated successfully');
            
            // Recalculate stats since they might have changed
            $this->calculateStats();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update transaction status: ' . $e->getMessage());
        }
    }
    
    /**
     * Get paginated and filtered transactions
     */
    public function getTransactionsProperty()
    {
        $query = Transaction::query()
            ->with([
                'customer',
                'order' => function($query) {
                    // Eager load order with related cart items
                    $query->withTrashed();
                }
            ]);
        
        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('transaction_reference', 'like', '%' . $this->search . '%')
                ->orWhere('transaction.order_id', 'like', '%' . $this->search . '%');
                
                // Try to search by customer info
                try {
                    $q->orWhereHas('order', function($orderQuery) {
                        $orderQuery->where('guest_name', 'like', '%' . $this->search . '%');
                    });
                    
                    $q->orWhereHas('customer', function($customerQuery) {
                        $customerQuery->where('customer_name', 'like', '%' . $this->search . '%');
                    });
                } catch (\Exception $e) {
                    \Log::warning('Could not search by related details: ' . $e->getMessage());
                }
            });
        }
        
        // Apply date range filter
        if ($this->startDate && $this->endDate) {
            $query->whereBetween(DB::raw('DATE(transaction_date)'), [$this->startDate, $this->endDate]);
        }
        
        // Apply payment method filter (through order relationship)
        if ($this->paymentMethod) {
            $query->whereHas('order', function($q) {
                $q->where('payment_method', $this->paymentMethod);
            });
        }
        
        // Apply status filter
        if ($this->status) {
            $query->where('transaction_status', $this->status);
        }
        
        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);
        
        // Return paginated results
        return $query->paginate($this->perPage);
    }
    
    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.transaction-dashboard', [
            'transactions' => $this->getTransactionsProperty(),
            'totalTransactions' => $this->totalTransactions,
            'totalAmount' => $this->totalAmount,
            'todayTransactions' => $this->todayTransactions,
            'todayAmount' => $this->todayAmount,
        ]);
    }
}