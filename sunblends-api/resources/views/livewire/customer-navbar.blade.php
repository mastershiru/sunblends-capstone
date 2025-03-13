

<div>

    <nav class="fixed top-0 left-0 w-full h-20 flex items-center justify-around bg-white z-50 transition duration-500">
        <a href="{{ url('/home') }}">
            <img class="w-32 cursor-pointer" src="{{ asset('images/logo.png') }}" alt="Sunblends Logo" />
        </a>

        <div class="flex">
            <ul id="navbar" class="flex ">
                <li class="list-none px-5 relative top-[15px]">
                    <a href="{{ url('/dish') }}" class="font-poppins text-lg text-black font-extralight px-3 py-1 rounded-3xl transition duration-300 hover:shadow-inner hover:shadow-gray-400">Menu</a>
                </li>
                <li class="list-none px-5 relative top-[15px]">
                    <a href="#about" class="font-poppins text-lg text-black font-extralight px-3 py-1 rounded-3xl transition duration-300 hover:shadow-inner hover:shadow-gray-400">About</a>
                </li>
                <li class="list-none px-5 relative top-[15px]">
                    <a href="#contact" class="font-poppins text-lg text-black font-extralight px-3 py-1 rounded-3xl transition duration-300 hover:shadow-inner hover:shadow-gray-400">Contact</a>
                </li>
                <li class="list-none px-5 relative top-[15px]">
                    <a href="{{ url('/reservation') }}" class="font-poppins text-lg text-black font-extralight px-3 py-1 rounded-3xl transition duration-300 hover:shadow-inner hover:shadow-gray-400">Reservation</a>
                </li>


                <li class="hidden md:block list-none px-5 relative top-[15px]">
                    <button class="hidden font-poppins text-lg font-extralight px-3 py-1 rounded-3xl transition duration-300 hover:shadow-inner hover:shadow-gray-400" id="mobile-account-button">Account</button>
                </li>

                <form action="#" class="relative top-[15px] hidden lg:block">
                    <input type="search" class="w-[200px] h-10 rounded-lg px-4 pr-10 outline-none shadow-inner shadow-gray-300 text-sm" placeholder="Search Here...">
                    <button type="submit" class="absolute right-0 top-1/2 -translate-y-1/2 w-12 h-full opacity-50">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
                
                <a wire:click="cartOpen" class="inline-flex justify-center items-center w-10 h-10 ml-3 rounded-lg shadow-inner shadow-gray-300 relative top-[15px] bg-white">
                    <i class="uil uil-shopping-bag text-black"></i>
                    <span class="absolute -top-2.5 -right-2.5 w-[22px] h-[22px] flex justify-center items-center rounded-full bg-white text-xs border border-gray-400 text-[#ff8243]">{{$cartItem}}</span>
                </a>
            
                <div class="relative">
                    <a wire:click="toggleDropdownLogin" class="inline-flex justify-center items-center w-12 h-12 ml-3 rounded-full shadow-inner shadow-gray-300 relative top-[15px] bg-white">
                        @if(Auth::guard('customer')->check())
                            <img src="{{ Auth::guard('customer')->user()->customer_picture ? asset(Auth::guard('customer')->user()->customer_picture) : asset('images/profile.png') }}" 
                                alt="Profile" 
                                class="w-12 h-12 rounded-full">
                        @else
                            <i class="fa-solid fa-circle-user text-2xl"></i>
                        @endif
                    </a>  

                        @if($isDropdownLoginOpen)
                            <div class="absolute right-0 top-[65px] w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                                @if(Auth::guard('employee')->check())
                                    <button wire:click="GoToDashboard" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboad</button>
                                @endif
                                @if(Auth::guard('customer')->check())
                                    <button wire:click="setActiveModalOrder" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Orders</button>
                                    <button wire:click="setActiveModalAccount" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Account</button>
                                @endif
                                @if(Auth::guard('employee')->check() || Auth::guard('customer')->check())    
                                    <button wire:click="logout" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                                @else
                                    <button wire:click="setActiveModalLogin" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Login</button>
                                @endif
                            </div>  
                        @endif
                        
            </ul>

            <!-- <div id="mobile" class="hidden md:block">
                <a class="relative top-2.5">
                    <i id="bar" class="fas fa-bars text-black p-2.5 rounded-xl"></i>
                </a>
            </div> -->
        </div>
    </nav>

    


    @if($isOpen)
        @if($activeModal === 'cart')
        <!-- Cart Modal -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl w-[500px] max-w-[95%] relative">
                <button wire:click="toggleModal" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700">
                    <i class="uil uil-times text-xl"></i>
                </button>
                <div class="p-6">
                    <h2 class="text-2xl font-medium text-center mb-4">Cart</h2>
                    <button class="w-full bg-black text-white py-2 px-4 rounded-lg hover:bg-gray-800">Checkout</button>
                </div>
            </div>
        </div>
        @endif

        @if($activeModal === 'account')
        <!-- Account Modal -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl w-[500px] max-w-[95%] relative">
                <button wire:click="closeModal" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700">
                    <i class="uil uil-times text-xl"></i>
                </button>
                <div class="p-6">
                    <h2 class="text-2xl font-medium text-center mb-6">My Account</h2>
                    
                    <div class="flex flex-col items-center mb-6">
                        <div class="relative">
                            <img src="{{ Auth::guard('customer')->user()->customer_picture ? asset(Auth::guard('customer')->user()->customer_picture) : asset('images/profile.png') }}" 
                                 alt="Profile" 
                                 class="w-24 h-24 rounded-full mb-2">
                            <label for="profile-upload" class="absolute bottom-0 right-0 bg-gray-100 rounded-full p-2 cursor-pointer hover:bg-gray-200">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" id="profile-upload" class="hidden" wire:model="new_profile_picture">
                        </div>
                    </div>

                    <form wire:submit.prevent="updateAccount" class="space-y-4">
                        <div class="flex flex-col">
                            <label class="text-sm mb-1">Name</label>
                            <input type="text" wire:model="customer_name" 
                                   class="rounded-lg border-gray-200 shadow-inner p-2"
                                   value="{{ Auth::guard('customer')->user()->customer_name ?? '' }}">
                        </div>

                        <div class="flex flex-col">
                            <label class="text-sm mb-1">Email</label>
                            <input type="email" wire:model="customer_email" 
                                   class="rounded-lg border-gray-200 shadow-inner p-2"
                                   value="{{ Auth::guard('customer')->user()->customer_email ?? '' }}">
                        </div>

                        <div class="flex flex-col">
                            <label class="text-sm mb-1">Phone Number</label>
                            <input type="text" wire:model="customer_number" 
                                   class="rounded-lg border-gray-200 shadow-inner p-2"
                                   value="{{ Auth::guard('customer')->user()->customer_number ?? '' }}">
                        </div>

                        <div class="flex flex-col">
                            <label class="text-sm mb-1">New Password</label>
                            <input type="password" wire:model="new_password" 
                                   class="rounded-lg border-gray-200 shadow-inner p-2"
                                   placeholder="Leave blank to keep current password">
                        </div>

                        <div class="flex flex-col">
                            <label class="text-sm mb-1">Confirm New Password</label>
                            <input type="password" wire:model="new_password_confirmation" 
                                   class="rounded-lg border-gray-200 shadow-inner p-2">
                        </div>

                        <button type="submit" class="w-full bg-black text-white py-2 rounded-lg hover:bg-gray-800">
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>  @endif

        @if($activeModal === 'login')
            <!-- Login Modal -->
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
                <div class="bg-white rounded-lg shadow-xl w-[500px] max-w-[95%] relative">
                <button wire:click="closeModal" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700">
                    <i class="uil uil-times text-xl"></i>
                </button>
                <div class="p-6">
                    <h2 class="text-2xl font-medium mb-6 text-center">Log in</h2>
                    <form wire:submit.prevent="login" class="space-y-4">
                    <div class="flex flex-col ">
                        <label for="login-email" class="text-sm mb-1">Email</label>
                        <input type="email" id="login-email" wire:model="email" 
                        class="rounded-lg border-gray-200 shadow-inner p-2 " required>
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="login-password" class="text-sm mb-1">Password</label>
                        <input type="password" id="login-password" wire:model="password" 
                        class="rounded-lg border-gray-200 shadow-inner p-2" required>
                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="remember-me" class="mr-2" wire:model="remember">
                        <label for="remember-me" class="text-sm">Remember me</label>
                    </div>
                    <div class="space-y-3">
                        <button wire:click="goToEmployeeLogin" class="w-full bg-black text-white py-2 rounded-lg hover:bg-gray-800">Employee Login</button>
                        <button type="submit" class="w-full bg-black text-white py-2 rounded-lg hover:bg-gray-800">Login</button>
                        <button type="button" wire:click="setActiveModalRegister" class="w-full border border-gray-300 py-2 rounded-lg hover:bg-gray-50">Register</button>
                        <a href="#" class="block text-center text-sm text-gray-600 hover:text-gray-800">Forgot password?</a>
                    </div>
                    </form>
                </div>
                </div>
            </div>
        @endif

        @if($activeModal === 'register')
            <!-- Register Modal -->
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
                <div class="bg-white rounded-lg shadow-xl w-[500px] max-w-[95%] relative">
                    <button wire:click="closeModal" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700">
                        <i class="uil uil-times text-xl"></i>
                    </button>
                    <div class="p-6">
                        <form wire:submit.prevent="store" class="space-y-4">
                            <h2 class="text-2xl font-medium mb-6 text-center">Register</h2>
                            <div class="flex flex-col">
                                <label for="customer_name" class="text-sm mb-1">Name</label>
                                <input type="text" id="customer_name" wire:model="customer_name" class="rounded-lg border-gray-200 shadow-inner p-2" required>
                                @error('customer_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex flex-col">
                                <label for="customer_email" class="text-sm mb-1">Email</label>
                                <input type="email" id="customer_email" wire:model="customer_email" class="rounded-lg border-gray-200 shadow-inner p-2" required>
                                @error('customer_email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex flex-col">
                                <label for="customer_number" class="text-sm mb-1">Phone Number</label>
                                <input type="text" id="customer_number" wire:model="customer_number" class="rounded-lg border-gray-200 shadow-inner p-2" required>
                                @error('customer_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex flex-col">
                                <label for="customer_password" class="text-sm mb-1">Password</label>
                                <input type="password" id="customer_password" wire:model="customer_password" class="rounded-lg border-gray-200 shadow-inner p-2" required>
                                @error('customer_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex flex-col">
                                <label for="confirm-password" class="text-sm mb-1">Confirm Password</label>
                                <input type="password" id="confirm-password" wire:model="password_confirmation" class="rounded-lg border-gray-200 shadow-inner p-2" required>
                            </div>
                            
                            <button type="submit" class="w-full bg-black text-white py-2 rounded-lg hover:bg-gray-800">Sign up</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if($activeModal === 'orders')
            <!-- Orders Modal -->
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
                <div class="bg-white rounded-lg shadow-xl w-[800px] max-w-[95%] relative">
                <button wire:click="closeModal" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700">
                    <i class="uil uil-times text-xl"></i>
                </button>
                <div class="p-6">
                    <h2 class="text-2xl font-medium text-center mb-6">My Orders</h2>
                    
                    @if($orders->isEmpty())
                    <div class="text-center py-8 text-gray-500">
                        <p>You don't have any orders yet.</p>
                    </div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                            <tr>
                            <td class="px-6 py-4 whitespace-nowrap">#{{ $order->order_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $order->created_at->format('M d, Y h:i A') }}</td>  
                            <td class="px-6 py-4 whitespace-nowrap">₱{{ number_format($order->total_price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $order->status_order == 'completed' ? 'bg-green-100 text-green-800' : 
                                    ($order->status_order == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($order->status_order) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="ViewDetails({{$order->order_id}})" class="text-blue-600 hover:text-blue-900">View Details</button>
                            </td>
                            </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                    @endif
                </div>
                </div>
            </div>
        @endif

        

    @endif

        @livewire('order-detail')    
        @livewire('cart-order')
    
        
</div>