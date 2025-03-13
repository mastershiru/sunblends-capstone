<div>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Total Transactions</h3>
                    <p class="text-2xl font-bold">{{ $totalTransactions }}</p>
                </div>
                <div class="text-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Today's Transactions</h3>
                    <p class="text-2xl font-bold">{{ $todayTransactions }}</p>
                </div>
                <div class="text-green-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-yellow-500">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Today's Amount</h3>
                    <p class="text-2xl font-bold">₱{{ number_format($todayAmount, 2) }}</p>
                </div>
                <div class="text-yellow-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-indigo-500">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Total Amount</h3>
                    <p class="text-2xl font-bold">₱{{ number_format($totalAmount, 2) }}</p>
                </div>
                <div class="text-indigo-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search transaction reference, customer..." 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >
            </div>
            
            <div class="w-full md:w-48">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select 
                    wire:model.live="status" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option value="">All Statuses</option>
                    <option value="completed">Completed</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
            
            <div class="w-full md:w-48">
                <label for="dateRange" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                <select 
                    wire:model.live="dateRange" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="this_week">This Week</option>
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>
            
            @if($dateRange == 'custom')
            <div class="w-full md:w-48">
                <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input 
                    type="date" 
                    wire:model.live="startDate" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >
            </div>
            
            <div class="w-full md:w-48">
                <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input 
                    type="date" 
                    wire:model.live="endDate" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >
            </div>
            @endif
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="overflow-x-auto shadow-xl rounded-lg">
        <table class="min-w-full bg-white divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center cursor-pointer" wire:click="sortBy('transaction_reference')">
                            Reference
                            @if($sortField === 'transaction_reference')
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    @if($sortDirection === 'asc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    @endif
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Order ID
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Customer
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center cursor-pointer" wire:click="sortBy('transaction_status')">
                            Payment Status
                            @if($sortField === 'transaction_status')
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    @if($sortDirection === 'asc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    @endif
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center cursor-pointer" wire:click="sortBy('cash_amount')">
                            Amount
                            @if($sortField === 'cash_amount')
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    @if($sortDirection === 'asc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    @endif
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Change
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center cursor-pointer" wire:click="sortBy('transaction_date')">
                            Date
                            @if($sortField === 'transaction_date')
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    @if($sortDirection === 'asc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    @endif
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($transactions as $transaction)
                    <tr>
                        
                        <td class="px-6 py-4 whitespace-wrap text-sm text-gray-500">
                            {{ $transaction->transaction_reference }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            #{{ $transaction->order_id ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-wrap text-sm text-gray-500">
                            {{ $transaction->order->guest_name ?? $transaction->customer->customer_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $transaction->transaction_status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($transaction->transaction_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   'bg-red-100 text-red-800') }}">
                                {{ ucfirst($transaction->transaction_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ₱{{ number_format($transaction->cash_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ₱{{ number_format($transaction->change_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-wrap text-sm text-gray-500">
                            {{ $transaction->transaction_date ? $transaction->transaction_date->format('M d, Y h:i A') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <!-- Status Dropdown - Select Option Style -->
                                <select 
                                    wire:change="updateTransactionStatus({{ $transaction->transaction_id }}, $event.target.value)" 
                                    class="text-sm px-2 py-2 rounded-md {{ 
                                        $transaction->transaction_status === 'pending' ? 'bg-yellow-100' : 
                                        ($transaction->transaction_status === 'completed' ? 'bg-green-100' : 
                                        ($transaction->transaction_status === 'failed' ? 'bg-red-100' : '')) 
                                    }}"
                                >
                                    <option value="pending" {{ $transaction->transaction_status === 'pending' ? 'selected' : '' }} class="bg-yellow-100">Pending</option>
                                    <option value="completed" {{ $transaction->transaction_status === 'completed' ? 'selected' : '' }} class="bg-green-100">Completed</option>
                                    <option value="failed" {{ $transaction->transaction_status === 'failed' ? 'selected' : '' }} class="bg-red-100">Failed</option>
                                </select>
                                
                                <button wire:click="viewTransactionDetails({{ $transaction->transaction_id }})" class="text-white bg-amber-500 px-4 py-2 rounded-md hover:bg-amber-600">
                                    View Details
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No transactions found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $transactions->links() }}
    </div>

    <!-- Transaction Detail Modal -->
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