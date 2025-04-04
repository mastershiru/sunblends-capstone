<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\User;
use App\Notifications\OrderStatusChanged as OrderStatusNotification;
use App\Events\OrderStatusChanged as OrderStatusEvent;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\CustomerNotification;
use App\Models\Transaction;
use Livewire\WithPagination;

class OrderQueue extends Component
{
    use WithPagination;
    
    // Filter properties
    public $search = '';
    public $dateRange = 'today';
    public $orderType = '';
    public $orderStatus = '';
    public $deliveryOption = '';
    public $startDate;
    public $endDate;
    public $perPage = 10;
    
    public $isDetailModalOpen = false;
    public $customerInfo;
    public $cartItems;
    
    public $statusDescriptions = [
        'pending' => 'Your order has been received and is awaiting processing.',
        'processing' => 'Our kitchen team is now preparing your order.',
        'ready' => 'Your order is ready for pickup!', 
        'completed' => 'Your order has been completed. Thank you!',
        'cancelled' => 'This order has been cancelled.',
    ];

    // When a property is updated, reset pagination
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingOrderStatus()
    {
        $this->resetPage();
    }
    
    public function updatingOrderType()
    {
        $this->resetPage();
    }
    
    public function updatingDeliveryOption()
    {
        $this->resetPage();
    }
    

    public function mount()
    {
        // Set default date range
        $this->setDateRange('today');
    }
    
    /**
     * Set date range based on selection
     */
    public function setDateRange($range)
    {
        $this->dateRange = $range;
        
        switch ($range) {
            case 'today':
                $this->startDate = now()->startOfDay()->format('Y-m-d');
                $this->endDate = now()->endOfDay()->format('Y-m-d');
                break;
            case 'yesterday':
                $this->startDate = now()->subDay()->startOfDay()->format('Y-m-d');
                $this->endDate = now()->subDay()->endOfDay()->format('Y-m-d');
                break;
            case 'this_week':
                $this->startDate = now()->startOfWeek()->format('Y-m-d');
                $this->endDate = now()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $this->startDate = now()->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->startDate = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'custom':
                // Keep current dates if already set, otherwise set to last 7 days
                if (!$this->startDate) {
                    $this->startDate = now()->subDays(7)->format('Y-m-d');
                }
                if (!$this->endDate) {
                    $this->endDate = now()->format('Y-m-d');
                }
                break;
            default:
                $this->startDate = now()->startOfDay()->format('Y-m-d');
                $this->endDate = now()->endOfDay()->format('Y-m-d');
                break;
        }
    }
    
    /**
     * Handle date range change
     */
    public function updatedDateRange()
    {
        $this->resetPage();
        $this->setDateRange($this->dateRange);
    }

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
        try {
            Log::info("Updating order status", ['order_id' => $id, 'status' => $status]);
            
            $order = Order::findOrFail($id);
            
            // Store previous status for comparison
            $previousStatus = $order->status_order;
            
            // Don't do anything if status hasn't changed
            if ($previousStatus === $status) {
                return;
            }
            
            // Get timestamp for notification
            $timestamp = now();
            
            // Update the order status
            $order->update([
                'status_order' => $status
            ]);

            $transaction = Transaction::where('order_id', $id)->first();
            if($status == 'cancelled' && $transaction) {
                // Change transaction status from "failed" to "cancelled"
                $transaction->update([
                    'transaction_status' => 'cancelled'
                ]);
            }elseif($status == 'completed' && $transaction) {
                // Change transaction status from "pending" to "completed"
                $transaction->update([
                    'transaction_status' => 'completed'
                ]);
            }
            
            // Create notification payload with rich data
            $notificationData = [
                'order_id' => $order->order_id,
                'customer_id' => $order->customer_id,
                'guest_name' => $order->guest_name,
                'customer_name' => $order->customer->customer_name ?? null,
                'status' => $status,
                'previous_status' => $previousStatus,
                'total_price' => $order->total_price,
                'updated_at' => $timestamp->format('M d, Y h:i A'),
                'timestamp' => $timestamp->timestamp,
                'message' => "Order #{$order->order_id} status updated to " . ucfirst($status),
                'description' => $this->statusDescriptions[$status] ?? null,
                'is_important' => in_array($status, ['completed', 'ready', 'cancelled']),
                'items_count' => $order->cart->count(),
                'notify_type' => $this->getNotifyType($status),
                'delivery_option' => $order->delivery_option,
                'address' => $order->address,
                'pickup_in' => $order->pickup_in ? $order->pickup_in->format('Y-m-d H:i:s') : null,
                'delivered_in' => $order->delivered_in ? $order->delivered_in->format('Y-m-d H:i:s') : null,
                'payment_method' => $order->payment_method,
                'created_at' => $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : null,
            ];
            
            // If this is an online order with a customer
            if ($order->customer_id) {
                try {
                    $customerNotification = CustomerNotification::create([
                        'type' => 'order.status_changed',
                        'customer_id' => $order->customer_id,
                        'order_id' => $order->order_id,
                        'status' => $status,
                        'message' => "Order #{$order->order_id} status updated to " . ucfirst($status),
                        'data' => $notificationData, // Stores the full notification data as JSON
                    ]);

                    // Notify the specific customer using notification
                    $customer = Customer::find($order->customer_id);
                    if ($customer) {
                        $customer->notify(new OrderStatusNotification($order, $notificationData));
                        
                        Log::info('Customer notification sent', [
                            'customer_id' => $customer->customer_id,
                            'order_id' => $order->order_id,
                            'status' => $status
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send customer notification', [
                        'error' => $e->getMessage(),
                        'order_id' => $order->order_id
                    ]);
                }
            }
            
            try {
                // Also broadcast using event for realtime updates
                event(new OrderStatusEvent($notificationData));
                
                Log::info('Event broadcast sent', [
                    'order_id' => $order->order_id,
                    'status' => $status
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to broadcast event', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'order_id' => $order->order_id
                ]);
            }
            
            session()->flash('success', "Order #{$order->order_id} status updated to " . ucfirst($status));
        } catch (\Exception $e) {
            Log::error('Error updating order status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => $id
            ]);
            session()->flash('error', 'Failed to update order status: ' . $e->getMessage());
        }
    }
    
    /**
     * Get the appropriate notification type based on status
     */
    protected function getNotifyType($status)
    {
        switch ($status) {
            case 'completed':
            case 'ready':
                return 'success';
            case 'processing':
                return 'info';
            case 'pending':
                return 'info';
            case 'cancelled':
                return 'error';
            default:
                return 'info';
        }
    }

    public function ViewDetails($id)
    {
        $this->openDetailModal();
        
        $transactionId = Transaction::where('order_id', $id)->value('transaction_id');
        
        if (!$transactionId) {
            session()->flash('error', 'No transaction found for this order.');
            return;
        }
        
        $this->dispatch('openTransactionModal', transactionId: $transactionId);                      
    }

    /**
     * Get filtered orders query
     */
    public function getOrdersProperty()
    {
        return Order::query()
            ->with(['customer', 'cart' => function($query) {
                // Include soft deleted items in count
                $query->withTrashed();
            }])
            ->when($this->search, function ($query, $search) {
                // Search in order fields and related customer
                return $query->where(function($q) use ($search) {
                    $q->where('order_id', 'like', "%{$search}%")
                      ->orWhere('guest_name', 'like', "%{$search}%")
                      ->orWhere('total_price', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($customerQuery) use ($search) {
                          $customerQuery->where('customer_name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($this->orderStatus, function ($query, $status) {
                return $query->where('status_order', $status);
            })
            ->when($this->orderType, function ($query, $type) {
                return $query->where('type_order', $type);
            })
            ->when($this->deliveryOption, function ($query, $option) {
                return $query->where('delivery_option', $option);
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                return $query->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.order-queue', [
            'filteredOrders' => $this->orders,
            'customerInfo' => $this->customerInfo,
            'cartItems' => $this->cartItems,
        ]);
    }
}