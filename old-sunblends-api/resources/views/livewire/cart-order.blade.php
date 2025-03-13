<div>
@if($openCart)
<div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        
        <div class="relative bg-white rounded-lg p-8 max-w-lg w-full h-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Shopping Cart</h3>
                <button class="text-gray-400 hover:text-gray-500" wire:click="CartClose">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @if($cart && $cart->count() > 0)
                @foreach($cart as $cartItem)
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <img src="{{ asset($cartItem->dishes->dish_picture) }}" 
                                alt="{{ $cartItem->dishes->dish_name }}" 
                                class="h-16 w-16 object-cover rounded">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium">{{ $cartItem->dishes->dish_name }}</h4>
                                <p class="text-sm text-gray-500">Quantity: {{ $cartItem->quantity }}</p>
                                <p class="text-sm font-medium">₱{{ $cartItem->dishes->Price }}</p>
                            </div>
                            
                                <button wire:click="decrementQuantity({{ $cartItem->cart_id }})" class="bg-gray-200 px-2 py-1 rounded">
                                    -
                                </button>
                                <span class="text-gray-700 ml-5 ">{{ $cartItem->quantity }}</span>
                                <button wire:click="incrementQuantity({{ $cartItem->cart_id }})" class="bg-gray-200 px-2 py-1 rounded">
                                    +
                                </button>
                            
                            <button wire:click="removeFromCart({{ $cartItem->cart_id }})" class="text-red-500 hover:text-red-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach

                <!-- bottom area -->
                <div class="mt-8"> 
                    <form>
                        <div class="mt-10">
                            <label for="deliver" class="inline-flex items-center">
                                <input wire:model="is_delivery" type="radio" wire:click="deliverOption" class="form-radio" name="deliveryOption" value="delivery">
                                <span class="ml-2">Delivery</span>
                            </label>
                            <label for="pickup" class="inline-flex items-center ml-6">
                                <input type="radio" wire:model="is_pickup" wire:click="pickupOption" class="form-radio" name="deliveryOption" value="pickup">
                                <span class="ml-2">Pick-Up</span>
                            </label>
                        </div>

                        @if($deliveryOption === 'delivery')
                            <div class="mt-4">
                                <label for ="address" class="block text-sm font-medium text-gray-700">Delivery Address:</label>
                                <input type="text" wire:model="address" name="address" id="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Enter delivery address">
                            </div>
                        @elseif($deliveryOption === 'pickup')
                            <div class="mt-4">
                                <label for="pickupTime" class="block text-sm font-medium text-gray-700">Pick-up Time</label>
                                <input type="time" wire:model="pickup_in" name="pickupTime" id="pickupTime" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        @endif

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method:</label>
                            <label for="cod" class="inline-flex items-center">
                                <input wire:model="payment_method" type="radio" class="form-radio" name="paymentMethod" value="Cash">
                                <span class="ml-2">Cash on Delivery</span>
                            </label>
                            <label for="ewallet" class="inline-flex items-center ml-6">
                                <input wire:model="payment_method" type="radio" class="form-radio" name="paymentMethod" value="Gcash">
                                <span class="ml-2">E-Wallet</span>
                            </label>
                        </div>


                    </form> 
                </div>
                
                <div class="mt-6">
                    <div class="flex justify-between text-base font-medium text-gray-900">
                        <p>Total</p>
                        <p>₱{{ $cart->sum(function($item) { return $item->dishes->Price * $item->quantity; }) }}</p>
                    </div>
                    <button wire:click="checkout" class="mt-4 w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Proceed to Checkout
                    </button>
                </div>
            @else
                <p class="text-gray-500 text-center">Your cart is empty</p>
            @endif
        </div>
    </div>
</div>
@endif
</div>
