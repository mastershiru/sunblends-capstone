<div>
    <!-- Detail Modal -->

    @if($isDetailModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="relative bg-white rounded-lg w-full max-w-4xl">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-xl font-semibold">Order Details</h3>
                    <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal content -->
                <div class="p-6">
                    <!-- Customer & Order Info Card -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h4 class=" font-bold mb-2">Customer Information</h4>
                                <p><b>Name:</b> {{ $customerInfo->customer->customer_name ?? $customerInfo->guest_name }}</p>
                                <p><b>Email:</b> {{ $customerInfo->customer->customer_email ?? 'null'}}</p>
                                <p><b>Phone:</b> {{ $customerInfo->customer->customer_number ?? 'null' }}</p>
                            </div>
                            <div>
                                <h4 class="font-bold mb-2">Order Information</h4>
                                <p><b>Order ID:</b> #{{ $customerInfo->order_id }}</p>
                                <p><b>Status:</b> {{ ucfirst($customerInfo->status_order) }}</p>
                                <p><b>Order Type:</b> {{ $customerInfo->type_order }}</p>
                                <p><b>Payment Method:</b> {{ $customerInfo->payment_method }}</p>
                                <p><b>Deliver Option:</b> {{ $customerInfo->delivery_option }}</p>

                                @if($customerInfo->delivery_option == 'delivery')
                                    <p><b>Delivery Address:</b> {{ $customerInfo->address }}</p>
                                @elseif($customerInfo->delivery_option == 'pickup' )
                                    <p><b>Pick-Up Time:</b> {{ $customerInfo->pickup_in->format('M d, Y H:i A') }}</p>
                                @endif
                                
                                <p><b>Order Date:</b> {{ $customerInfo->created_at->format('M d, Y H:i A') }}</p>

                            </div>
                        </div>
                    </div>

                    <!-- Order Items List -->
                    <div>
                        <h4 class="font-semibold mb-4">Order Items</h4>
                        <div class="border rounded-lg divide-y">
                            <div class="p-4 flex justify-between items-center bg-gray-100">
                                <div>
                                    <p class="font-medium">Item Details</p>
                                </div>
                                <div class="flex">
                                    <p class="font-medium text-right pr-10">Dish Price</p>
                                    <p class="font-medium text-right">Subtotal</p>
                                </div>
                            </div>
                            @foreach($cartItems as $item)
                                <div class="p-4 flex justify-between items-center">
                                    <div class="flex items-center space-x-4">
                                        <img src="{{ asset($item->dishes->dish_picture) }}" alt="{{ $item->dishes->dish_name }}" class="h-12 w-12 object-cover rounded-full">
                                        <div>
                                            <p class="font-medium">{{ $item->dishes->dish_name }}</p>
                                            <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                                        </div>
                                    </div>
                                    <div class="flex">
                                        <p class="font-medium text-right pr-10">₱{{ number_format($item->dishes->Price, 2) }}</p>
                                        <p class="font-medium text-right ">₱{{ number_format($item->dishes->Price * $item->quantity, 2) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-right font-bold mt-4">
                            <h1>Total: ₱{{ number_format($customerInfo->total_price, 2) }}</h1>
                        </div>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="px-6 py-4 border-t flex justify-end">
                    <button wire:click="closeDetailModal" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    
</div>
