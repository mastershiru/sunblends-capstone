<div>
    <div class="mb-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Order Queue</h1>
        <div>
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-2 mb-2">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-2 mb-2">
                    {{ session('error') }}
                </div>
            @endif
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
                    placeholder="Search order ID, customer name..." 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >
            </div>
            
            <div class="w-full md:w-48">
                <label for="orderStatus" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select 
                    wire:model.live="orderStatus" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="ready">Ready</option>
                </select>
            </div>
            
            <div class="w-full md:w-48">
                <label for="orderType" class="block text-sm font-medium text-gray-700 mb-1">Order Type</label>
                <select 
                    wire:model.live="orderType" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option value="">All Types</option>
                    <option value="dine-in">Online</option>
                    <option value="walk-in">Walk In</option>
                </select>
            </div>
            
            <div class="w-full md:w-48">
                <label for="deliveryOption" class="block text-sm font-medium text-gray-700 mb-1">Delivery Option</label>
                <select 
                    wire:model.live="deliveryOption" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option value="">All Options</option>
                    <option value="delivery">Delivery</option>
                    <option value="pickup">Pickup</option>
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
        </div>
        
        @if($dateRange == 'custom')
        <div class="mt-4 flex flex-col md:flex-row gap-4">
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
        </div>
        @endif
    </div>

    <div class="overflow-x-auto shadow-2xl rounded-lg">
        <table class="min-w-full bg-white divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Id</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Option</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($filteredOrders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->order_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->customer->customer_name ?? $order->guest_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $order->status_order === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($order->status_order === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($order->status_order === 'cancelled' ? 'bg-red-100 text-red-800' :
                                   ($order->status_order === 'processing' ? 'bg-blue-100 text-blue-800' : 
                                   ($order->status_order === 'ready' ? 'bg-purple-100 text-purple-800' : '')))) }}">
                                {{ ucfirst($order->status_order) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($order->type_order) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($order->payment_method) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($order->delivery_option) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">₱{{ number_format($order->total_price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-wrap">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                        <td class="px-6 py-4 whitespace-wrap text-sm font-medium">
                            <button wire:click="ViewDetails({{ $order->order_id }})" class="text-white font-medium mr-2 bg-amber-500 px-3 py-1.5 rounded hover:bg-amber-600">
                                View Details
                            </button>
                            <select wire:change="updateStatus({{ $order->order_id }}, $event.target.value)" 
                                class="text-sm px-2 py-1.5 rounded-md {{ 
                                    $order->status_order === 'pending' ? 'bg-yellow-100' : 
                                    ($order->status_order === 'processing' ? 'bg-blue-100' : 
                                    ($order->status_order === 'completed' ? 'bg-green-100' : 
                                    ($order->status_order === 'cancelled' ? 'bg-red-100' :
                                    ($order->status_order === 'ready' ? 'bg-purple-100' : '')))) 
                                }}" >
                                <option value="pending" {{ $order->status_order === 'pending' ? 'selected' : '' }} class="bg-yellow-100">Pending</option>
                                <option value="processing" {{ $order->status_order === 'processing' ? 'selected' : '' }} class="bg-blue-100">Processing</option>
                                <option value="ready" {{ $order->status_order === 'ready' ? 'selected' : '' }} class="bg-purple-100">Ready</option>
                                <option value="completed" {{ $order->status_order === 'completed' ? 'selected' : '' }} class="bg-green-100">Completed</option>
                                <option value="cancelled" {{ $order->status_order === 'cancelled' ? 'selected' : '' }} class="bg-red-100">Cancelled</option>
                            </select>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                            No orders available
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $filteredOrders->links() }}
    </div>

    <!-- Dish Modal -->
    @if($isDetailModalOpen)
        @livewire('transaction-modal')
    @endif
</div>