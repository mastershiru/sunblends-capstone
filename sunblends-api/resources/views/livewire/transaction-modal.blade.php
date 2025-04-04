<div>
    @if($isDetailModalOpen && $currentTransaction)
        <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
            <div class="relative bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto p-6">
                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-3 border-b mb-4">
                    <h3 class="text-xl font-bold text-gray-800">
                        Order #{{ $currentTransaction->order_id }} Details
                    </h3>
                    <button wire:click="closeDetailModal" class="text-gray-600 hover:text-gray-900 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Order Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <!-- Left Column -->
                    <div>
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <h4 class="font-bold text-gray-700 mb-2">Transaction Information</h4>
                            <div class="text-sm">
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">Transaction ID:</span>
                                    <span class="font-medium">{{ $currentTransaction->transaction_id }}</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">Transaction Reference:</span>
                                    <span class="font-medium">{{ $currentTransaction->transaction_reference }}</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">Transaction Date:</span>
                                    <span class="font-medium">{{ $this->formatDate($currentTransaction->transaction_date) }}</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColor($currentTransaction->transaction_status) }}">
                                        {{ ucfirst($currentTransaction->transaction_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-bold text-gray-700 mb-2">Customer Information</h4>
                            <div class="text-sm">
                                @if($currentTransaction->customer)
                                    <div class="flex justify-between py-1">
                                        <span class="text-gray-600">Name:</span>
                                        <span class="font-medium">{{ $currentTransaction->customer->customer_name }}</span>
                                    </div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-gray-600">Email:</span>
                                        <span class="font-medium">{{ $currentTransaction->customer->customer_email }}</span>
                                    </div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-gray-600">Phone:</span>
                                        <span class="font-medium">{{ $currentTransaction->customer->customer_phone ?? 'N/A' }}</span>
                                    </div>
                                @else
                                    <div class="py-1 text-gray-600">Guest Order</div>
                                    <div class="flex justify-between py-1">
                                        <span class="text-gray-600">Guest Name:</span>
                                        <span class="font-medium">{{ $currentTransaction->order->guest_name ?? 'N/A' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div>
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <h4 class="font-bold text-gray-700 mb-2">Order Information</h4>
                            <div class="text-sm">
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">Order Date:</span>
                                    <span class="font-medium">{{ $this->formatDate($currentTransaction->order->created_at) }}</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">Order Type:</span>
                                    <span class="font-medium">{{ ucfirst($currentTransaction->order->type_order) }}</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">Payment Method:</span>
                                    <span class="font-medium">{{ ucfirst($currentTransaction->order->payment_method) }}</span>
                                </div>
                                @if($currentTransaction->order->payment_method == 'Cash')
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">Cash Amount:</span>
                                    <span class="font-medium">₱{{ number_format($currentTransaction->cash_amount ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">Change:</span>
                                    <span class="font-medium">₱{{ number_format($currentTransaction->change_amount ?? 0, 2) }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColor($currentTransaction->order->status_order) }}">
                                        {{ ucfirst($currentTransaction->order->status_order) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Delivery/Pickup Information Box -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-bold text-gray-700 mb-2">
                                {{ ucfirst($currentTransaction->order->delivery_option) }} Information
                            </h4>
                            
                            @if($currentTransaction->order->delivery_option == 'delivery')
                                <!-- Delivery Information -->
                                <div class="text-sm">
                                    <div class="py-2 px-3 my-2 border-l-4 border-orange-500 bg-orange-50 rounded">
                                        <div class="font-semibold text-gray-800 mb-1">Delivery Address:</div>
                                        <div class="text-gray-700">
                                            {{ $currentTransaction->order->address ?? 'No address provided' }}
                                        </div>
                                    </div>
                                    
                                    @if($currentTransaction->order->delivered_in)
                                    <div class="flex justify-between py-1">
                                        <span class="text-gray-600">Delivered At:</span>
                                        <span class="font-medium">{{ $this->formatDate($currentTransaction->order->delivered_in) }}</span>
                                    </div>
                                    @endif
                                </div>
                            @elseif($currentTransaction->order->delivery_option == 'pickup')
                                <!-- Pickup Information -->
                                <div class="text-sm">
                                    @if($currentTransaction->order->pickup_in)
                                        <div class="py-2 px-3 my-2 border-l-4 border-blue-500 bg-blue-50 rounded">
                                            <div class="font-semibold text-gray-800 mb-1">Pickup Schedule:</div>
                                            <div class="flex flex-col">
                                                <span class="text-gray-700">
                                                    <span class="font-medium">Date:</span> 
                                                    {{ $this->formatDateOnly($currentTransaction->order->pickup_in) }}
                                                </span>
                                                <span class="text-gray-700">
                                                    <span class="font-medium">Time:</span> 
                                                    {{ $this->formatTimeOnly($currentTransaction->order->pickup_in) }}
                                                </span>
                                                
                                                @if($currentTransaction->order->status_order != 'completed' && $currentTransaction->order->status_order != 'cancelled')
                                                    <span class="text-sm text-blue-700 font-medium mt-1">
                                                        {{ $this->getTimeUntilPickup($currentTransaction->order->pickup_in) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-gray-600 italic">No pickup time specified</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="mb-6">
                    <h4 class="font-bold text-gray-700 mb-2">Order Items</h4>
                    <div class="bg-white border rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Item
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Price
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantity
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subtotal
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($currentTransaction->order->cart as $cartItem)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0">
                                                    <img class="h-10 w-10 rounded-full" src="{{ asset($cartItem->dishes->dish_picture ?? 'images/placeholder-food.jpg') }}" alt="{{ $cartItem->dishes->dishes_name ?? 'Product Image' }}">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $cartItem->dishes->dish_name ?? 'Product Name Not Available' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">₱{{ number_format($cartItem->dishes->Price, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $cartItem->quantity }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">₱{{ number_format($cartItem->dishes->Price * $cartItem->quantity, 2) }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No items found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="flex justify-end">
                    <div class="w-full">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-bold text-gray-700 mb-2">Order Summary</h4>
                            <div class="text-sm">
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">Subtotal:</span>
                                    <span class="font-medium">₱{{ number_format($currentTransaction->order->total_price - ($currentTransaction->delivery_fee ?? 0), 2) }}</span>
                                </div>
                                
                                @if($currentTransaction->order->delivery_option == 'delivery')
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">Delivery Fee:</span>
                                    <span class="font-medium">₱{{ number_format($currentTransaction->delivery_fee ?? 40, 2) }}</span>
                                </div>
                                @endif
                                
                                <div class="flex justify-between py-1 border-t border-gray-200 mt-2 pt-2">
                                    <span class="text-gray-800 font-bold">Total:</span>
                                    <span class="text-gray-800 font-bold">₱{{ number_format($currentTransaction->order->total_price, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="mt-6 border-t pt-4 flex justify-end">
                    <button wire:click="closeDetailModal" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>