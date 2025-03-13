<div>
    <!-- Main container - change from flex to block on smaller screens -->
    <div class="md:flex block">
        <!-- Left side - Dish List -->
        <div class="w-full md:w-3/5 md:pr-4 mb-6 md:mb-0">
            <div class="mb-4">
                <h2 class="text-xl font-bold">Dine in Order</h2>
            </div>

            <!-- Search and filter section - stack vertically on smaller screens -->
            <div class="flex flex-col md:flex-row gap-4 md:space-x-4 mb-4">
                <div class="flex-1">
                    <input type="text" 
                           wire:model.live="search" 
                           placeholder="Search dishes..." 
                           class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="w-full md:w-auto">
                    <select wire:model.live="categoryFilter" 
                            class="w-full px-4 py-2 border rounded-lg">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Dish listing table - adjust for tablet -->
            <div class="overflow-x-auto flex-grow max-h-[36rem] lg:max-h-[40rem] md:max-h-[36rem] overflow-y-auto rounded-lg shadow">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100 sticky top-0">
                        <tr>
                            <th class="px-3 md:px-6 py-3 text-left w-24"></th>
                            <th class="px-3 md:px-6 py-3 text-left">Name</th>
                            <th class="px-3 md:px-6 py-3 text-left hidden md:table-cell">Category</th>
                            <th class="px-3 md:px-6 py-3 text-left">Price</th>
                            <th class="px-3 md:px-6 py-3 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($dishes ?? [] as $dish)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-3 md:px-6 py-4">
                                <img src="{{ asset($dish->dish_picture) }}" class="w-16 h-16 md:w-20 md:h-20 object-cover rounded-full">
                            </td>
                            <td class="px-3 md:px-6 py-4 font-bold text-sm md:text-base">{{ $dish->dish_name }}</td>
                            <td class="px-3 md:px-6 py-4 hidden md:table-cell">{{ $dish->category }}</td>
                            <td class="px-3 md:px-6 py-4">₱{{ number_format($dish->Price, 2) }}</td>
                            <td class="px-3 md:px-6 py-4">
                                <button 
                                wire:click="addToOrder({{ $dish->dish_id }})"
                                class="{{ $dish->dish_available ? 'bg-amber-500 hover:bg-amber-600' : 'bg-gray-500 cursor-not-allowed' }} text-white px-2 py-1 md:px-4 md:py-2 text-sm rounded"
                                {{ !$dish->dish_available ? 'disabled' : '' }}>
                                {{ $dish->dish_available ? 'Add' : 'N/A' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 md:px-6 py-4 text-center text-gray-500">No dishes available</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right side - Order Summary -->
        <div class="w-full md:w-2/5 mt-6 md:mt-10 md:ml-5 max-h-[36rem] lg:max-h-[44rem] md:max-h-[40rem] overflow-y-auto bg-white rounded-lg shadow p-4">
            <h2 class="text-xl font-bold mb-4">Your Order</h2>
            
            <!-- Order items -->
            <div class="max-h-60 md:max-h-96 overflow-y-auto mb-4">
                @forelse($orderItems as $item)
                    <div class="flex justify-between items-center mb-2 p-2 border-b">
                        <div class="flex-1 pr-2">
                            <h4 class="font-semibold whitespace-normal text-sm md:text-base">{{ $item->dishes->dish_name }}</h4>
                            <p class="text-xs md:text-sm text-gray-600">₱{{ number_format($item->dishes->Price, 2) }} x {{ $item['quantity'] }}</p>
                        </div>
                        <div class="flex items-center">
                            <button wire:click="decrementQuantity({{ $item->cart_id }})" class="px-2 py-1 bg-gray-200 rounded">-</button>
                            <span class="mx-2 md:mx-5 text-sm md:text-base">{{ $item['quantity'] }}</span>
                            <button wire:click="incrementQuantity({{ $item->cart_id }})" class="px-2 py-1 bg-gray-200 rounded">+</button>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Your order is empty</p>
                @endforelse
            </div>

            <!-- Order form -->
            <div class="border-t pt-4">
                <!-- Payment method -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Payment Method</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model.defer="payment_method" value="Cash" class="form-radio">
                            <span class="ml-2">Cash</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model.defer="payment_method" value="e-wallet" class="form-radio">
                            <span class="ml-2">E-Wallet</span>
                        </label>
                    </div>
                    @error('payment_method') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>
                
                <!-- Customer name -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="customer_name">
                        Customer Name
                    </label>
                    <input type="text" 
                        wire:model.live="customer_name" 
                        placeholder="Customer Name" 
                        class="w-full px-4 py-2 border rounded-lg mb-2">
                    @error('customer_name') <p class="text-red-500 text-base mb-5">{{ $message }}</p> @enderror
                </div>
                
                <!-- Total -->
                <div class="flex justify-between font-bold mb-4">
                    <span>Total:</span>
                    <span>₱{{ number_format($total ?? 0, 2) }}</span>
                </div>

                <!-- Cash amount -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">
                        Cash Amount
                    </label>
                    <input type="number"
                        wire:model.live="cash"       
                        placeholder="Cash" 
                        class="w-full px-4 py-2 border rounded-lg mb-2">
                    @error('cash') <p class="text-red-500 text-base mb-5">{{ $message }}</p> @enderror
                </div>

                <!-- Change -->
                <div class="flex justify-between font-bold mb-4">
                    <span>Change:</span>
                    <span>₱{{ number_format($change ?? 0, 2) }}</span>
                </div>
                
                <!-- Order button -->
                <button 
                    wire:click="proceedOrder"
                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 w-full font-bold"
                    {{ empty($orderItems) ? 'disabled' : '' }}>
                    Proceed Order
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="fixed bottom-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md">
            {{ session('error') }}
        </div>
    @endif
</div>