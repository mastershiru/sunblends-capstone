<div>


<div class="bg-gray-100 rounded-lg  py-12 relative bg-cover bg-center bg-no-repeat min-h-screen" style="background-image: url('{{ asset('images/menu-bg.png') }}'); background-size: 100% 100%;">
        <div class=" mx-auto px-4">
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Filters and Search Sidebar -->
                <div class="w-full md:w-1/4 bg-white shadow-lg rounded-lg p-6">
                    <!-- Category Filter -->
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Categories</h3>
                        <div class="flex flex-wrap gap-2">
                            <button wire:click="loadDish" class="px-3 py-1 bg-gray-100 hover:bg-orange-500 hover:text-white text-gray-700 rounded-full text-sm transition duration-300">
                                All
                            </button>
                            @foreach($categories as $category)
                                <button wire:click="filterCategory('{{ $category->category }}')" class="px-3 py-1 bg-gray-100 hover:bg-orange-500 hover:text-white text-gray-700 rounded-full text-sm transition duration-300">
                                    {{ $category->category }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                <!-- Search Input -->
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Search</h3>
                        <div class="relative">
                            <input 
                                type="text" 
                                placeholder="Search dishes..." 
                                wire:model.live="search"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                            >
                            <svg class="absolute right-3 top-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
            </div>

            <!-- Menu Items Column -->
            <div class="w-full md:w-3/4 h-[700px] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4">
                    @foreach($dish as $item)
                        <div class="w-full">
                            <div class="dish-box text-center bg-white rounded-lg shadow-md p-3">
                                <div class="dist-img">
                                    <img src="{{ asset($item->dish_picture) }}" alt="{{ $item->dish_name }}" class="w-full h-auto object-cover">
                                </div>
                                <div class="dish-title mt-3">
                                    <h3 class="text-lg font-semibold">{{ $item->dish_name }}</h3>
                                    <p class="text-sm">Calories</p>
                                </div>
                                <div class="dish-info my-3">
                                    <ul>
                                        <li>
                                            <p class="text-sm">Type</p>
                                            <b>{{ $item->category }}</b>
                                        </li>
                                        <li>
                                            <p class="text-sm">Persons</p>
                                            <b>2</b>
                                        </li>
                                    </ul>
                                </div>
                                <div class="px-4 py-2">
                                    <ul class="flex justify-between items-center">
                                        <li class="flex items-center">
                                            <b class="text-2xl font-semibold">${{ $item->Price }}</b>
                                        </li>
                                        <li class="flex items-center">
                                            @if($item->dish_available)
                                            <button wire:click="addItem({{ $item->dish_id }})" class="w-10 h-10 flex items-center justify-center rounded-lg bg-gradient-to-r from-amber-400 to-amber-500 text-white hover:from-amber-500 hover:to-amber-600 shadow-lg hover:shadow-inner transition duration-300">
                                                <i class="uil uil-plus text-xl"></i>
                                            </button>
                                            @else
                                            <button disabled class="bg-gray-400 text-black px-2 py-2 rounded cursor-not-allowed">
                                                Not Available
                                            </button>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@if($isCustomerLogin)
<div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        
        <div class="relative bg-white rounded-lg p-8 max-w-lg w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Alert!</h3>
                <button class="text-gray-400 hover:text-gray-500" wire:click="closeCustomerModal">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>


            <p class="text-gray-500 text-center">You Need to be logged-in To Continue</p>
        </div>
    </div>
</div>

@endif



</div>
