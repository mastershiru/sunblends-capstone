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

class OrderQueue extends Component
{
    public $orders;
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
            
            // Reload the orders
            $this->loadOrders();
            
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
                'notify_type' => $this->getNotifyType($status)
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
     * 
     * @param string $status The order status
     * @return string Notification type (success, info, warning, error)
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
        
        // Get order with customer information
        $this->customerInfo = Order::with(['customer'])->find($id);
        
        // Get cart items including soft-deleted items
        $this->cartItems = Cart::withTrashed()
                               ->where('order_id', $id)
                               ->with('dishes')
                               ->get();
    }

    public function mount()
    {
        $this->loadOrders();
    }

    public function loadOrders()
    {
        // Get all orders ordered by creation date (newest first)
        $this->orders = Order::with(['customer', 'cart' => function($query) {
                // Include soft deleted items in count
                $query->withTrashed();
            }])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.order-queue', [
            'orders' => $this->orders,
            'customerInfo' => $this->customerInfo,
            'cartItems' => $this->cartItems,
        ]);
    }
}