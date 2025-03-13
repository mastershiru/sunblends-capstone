<div>
    <div class="flex ">
        <!-- Left side - Dish List -->
        <div class="flex-grow *:pr-4">
            <div class="mb-4">
            <h2 class="text-xl font-bold">Dine in Order</h2>
            </div>

            <div class="flex space-x-4 mb-4">
                <div class="flex-1">
                    <input type="text" 
                           wire:model.live="search" 
                           placeholder="Search dishes..." 
                           class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <select wire:model.live="categoryFilter" 
                            class="px-4 py-2 border rounded-lg">
                        <option value="">All Categories</option>
                        @foreach($dishes as $dish)
                            <option value="{{ $dish->category }}">{{ $dish->category }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto flex-grow max-h-[32rem] overflow-y-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100 sticky top-0">
                <tr>
                    <th class="px-6 py-3 text-left"></th>
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Category</th>
                    <th class="px-6 py-3 text-left">Price</th>
                    <th class="px-6 py-3 text-left">Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($dishes ?? [] as $dish)
                    <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <img src="{{ asset($dish->dish_picture) }}" class="w-20 h-20 object-cover rounded-full">
                    </td>
                    <td class="px-6 py-4 font-bold">{{ $dish->dish_name }}</td>
                    <td class="px-6 py-4">{{ $dish->category }}</td>
                    <td class="px-6 py-4">₱{{ number_format($dish->Price, 2) }}</td>
                    <td class="px-6 py-4">
                        <button 
                        wire:click="addToOrder({{ $dish->dish_id }})"
                        class="{{ $dish->dish_available ? 'bg-amber-500 hover:bg-amber-600' : 'bg-gray-500 cursor-not-allowed' }} text-white px-4 py-2 rounded"
                        {{ !$dish->dish_available ? 'disabled' : '' }}>
                        {{ $dish->dish_available ? 'Add to Order' : 'Not Available' }}
                        </button>
                    </td>
                    </tr>
                @empty
                    <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No dishes available</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            </div>
        </div>

        <!-- Right side - Order Summary -->
        <div class="flex-grow mt-10 ml-5 bg-white rounded-lg shadow p-4">
            <h2 class="text-xl font-bold mb-4">Your Order</h2>
            
            <div class="max-h-96 overflow-y-auto mb-4">
                @forelse($orderItems as $item)
                    <div class="flex justify-between items-center mb-2 p-2 border-b">
                        <div>
                            <h4 class="font-semibold whitespace-normal">{{ $item->dishes->dish_name }}</h4>
                            <p class="text-sm text-gray-600">₱{{ number_format($item->dishes->Price, 2) }} x {{ $item['quantity'] }}</p>
                        </div>
                        <div class="flex items-center">
                            <button wire:click="decrementQuantity({{ $item->cart_id }})" class="px-2 py-1 bg-gray-200 rounded">-</button>
                            <span class="mx-5">{{ $item['quantity'] }}</span>
                            <button wire:click="incrementQuantity({{ $item->cart_id }})" class="px-2 py-1 bg-gray-200 rounded">+</button>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center">Your order is empty</p>
                @endforelse
            </div>

            <div class="border-t pt-4">
                <div class="flex justify-between mb-2">
                    <span>Subtotal:</span>
                    <span>₱{{ number_format($subtotal ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold mb-4">
                    <span>Total:</span>
                    <span>₱{{ number_format($total ?? 0, 2) }}</span>
                </div>

                <div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Payment Method</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model.defer="payment_method" value="cash" class="form-radio">
                            <span class="ml-2">Cash</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model.defer="payment_method" value="e-wallet" class="form-radio">
                            <span class="ml-2">E-Wallet</span>
                        </label>
                    </div>
                    @error('payment_method') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>
                
                <label class="block text-gray-700 font-bold mb-2" for="customer_name">
                    Customer Name
                </label>
                <input type="text" 
                       wire:model.defer="customer_name" 
                       placeholder="Customer Name" 
                       class="w-full px-4 py-2 border rounded-lg mb-2">
                @error('customer_name') <p class="text-red-500 text-base mb-5">{{ $message }}</p> @enderror
                </div>
                
                <button 
                    wire:click="proceedOrder"
                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 w-full"
                    {{ empty($orderItems) ? 'disabled' : '' }}>
                    Proceed Order
                </button>
            </div>
        </div>
    </div>
</div>
