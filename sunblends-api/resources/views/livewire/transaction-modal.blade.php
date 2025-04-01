<div>
@if($isDetailModalOpen && $currentTransaction)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="relative bg-white rounded-lg w-full max-w-4xl">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-xl font-semibold">Transaction Details</h3>
                    <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal content -->
                <div class="p-6">
                    <!-- Transaction Info Card -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h4 class="font-bold mb-4 text-lg">Transaction Information</h4>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="mb-2"><span class="font-semibold">ID:</span> {{ $currentTransaction->transaction_id }}</p>
                                <p class="mb-2"><span class="font-semibold">Reference:</span> {{ $currentTransaction->transaction_reference }}</p>
                                <p class="mb-2"><span class="font-semibold">Payment Status:</span> 
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $currentTransaction->transaction_status === 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($currentTransaction->transaction_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                           'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($currentTransaction->transaction_status) }}
                                    </span>
                                </p>
                                <p class="mb-2"><span class="font-semibold">Date:</span> {{ $currentTransaction->transaction_date->format('M d, Y h:i A') }}</p>
                            </div>
                            <div>
                                <p class="mb-2"><span class="font-semibold">Amount:</span> ₱{{ number_format($currentTransaction->cash_amount, 2) }}</p>
                                <p class="mb-2"><span class="font-semibold">Change:</span> ₱{{ number_format($currentTransaction->change_amount, 2) }}</p>
                                <p class="mb-2"><span class="font-semibold">Total:</span> ₱{{ number_format($currentTransaction->cash_amount - $currentTransaction->change_amount, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Information -->
                    @if($currentTransaction->order)
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h4 class="font-bold mb-4 text-lg">Order Information</h4>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="mb-2"><span class="font-semibold">Order ID:</span> #{{ $currentTransaction->order->order_id }}</p>
                                <p class="mb-2"><span class="font-semibold">Customer:</span> {{ $currentTransaction->order->guest_name ?? $currentTransaction->customer->customer_name }}</p>
                                <p class="mb-2"><span class="font-semibold">Order Type:</span> {{ ucfirst($currentTransaction->order->type_order) }}</p>
                            </div>
                            <div>
                                <p class="mb-2"><span class="font-semibold">Order Status:</span> 
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $currentTransaction->order->order_status === 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($currentTransaction->order->order_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                           'bg-blue-100 text-blue-800') }}">
                                        {{ ucfirst($currentTransaction->order->status_order) }}
                                    </span>
                                </p>
                                <p class="mb-2"><span class="font-semibold">Payment Method:</span> {{ ucfirst($currentTransaction->order->payment_method) }}</p>
                                <p class="mb-2"><span class="font-semibold">Order Date:</span> {{ $currentTransaction->order->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        
                        <!-- Order Items - Update the relationship access based on how it's defined in your model -->
                        @if($currentTransaction->order && $currentTransaction->order->cart->count() > 0)
                        <div class="mt-4">
                            <h4 class="font-semibold mb-2">Order Items</h4>
                            <div class="border rounded-lg divide-y">
                                <div class="p-3 flex justify-between items-center bg-gray-100">
                                    <div class="font-medium">Item</div>
                                    <div class="flex space-x-10">
                                        <div class="font-medium">Quantity</div>
                                        <div class="font-medium">Price</div>
                                        <div class="font-medium">Subtotal</div>
                                    </div>
                                </div>
                                
                                @foreach($currentTransaction->order->cart as $item)
                                <div class="p-3 flex justify-between items-center">
                                    <div class="flex items-center space-x-3">
                                        @if($item->dishes)
                                            <img src="{{ asset($item->dishes->dish_picture ?? 'images/placeholder-food.jpg') }}" 
                                                alt="{{ $item->dishes->dish_name ?? 'Food Item' }}" 
                                                class="h-10 w-10 object-cover rounded"
                                                onerror="this.src='{{ asset('images/placeholder-food.jpg') }}'">
                                            <div>{{ $item->dishes->dish_name ?? 'Unknown Food Item' }}</div>
                                        @else
                                            <img src="{{ asset('images/placeholder-food.jpg') }}" 
                                                alt="Food Item" 
                                                class="h-10 w-10 object-cover rounded">
                                            <div>Unknown Food Item</div>
                                        @endif
                                    </div>
                                    <div class="flex space-x-10">
                                        <div class="text-center">{{ $item->quantity }}</div>
                                        <div>₱{{ number_format($item->dishes->Price ?? 0, 2) }}</div>
                                        <div>₱{{ number_format(($item->dishes->Price ?? 0) * $item->quantity, 2) }}</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <div class="mt-4 text-center text-gray-500 py-4 border rounded-lg">
                            No items found for this order.
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="bg-gray-50 rounded-lg p-6 mb-6 text-center text-gray-500">
                        <p>No order information associated with this transaction.</p>
                    </div>
                    @endif
                </div>

                <!-- Modal footer -->
                <div class="flex justify-end px-6 py-4 border-t">
                    <button wire:click="closeDetailModal" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>