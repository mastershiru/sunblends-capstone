<div>
    <div class="mb-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Menu Dashboard</h1>
        <button wire:click="openDishModal" class="bg-amber-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
            Add Dish
        </button>
    </div>

    <div class="overflow-x-auto shadow-2xl rounded-lg">
        <table class="min-w-full bg-white divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dish Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Picture</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Availability</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($dishes ?? [] as $dish)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $dish->dish_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <img src="{{ asset($dish->dish_picture) }}" alt="{{ $dish->dish_name }}" class="h-12 w-12 object-cover rounded-full">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $dish->category }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $dish->dish_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $dish->dish_available ? 'Available' : 'Not Available' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">₱{{ number_format($dish->Price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button wire:click="edit({{ $dish->dish_id}})" class="text-white bg-amber-500 mr-5 py-2 px-4 rounded hover:text-indigo-900">Edit</button>
                            <select wire:click="updateAvailability({{ $dish->dish_id }}, $event.target.value)" 
                                class="text-sm rounded-md px-2 py-2 {{ 
                                    $dish->dish_available ? 'bg-green-100' : 'bg-red-100'
                                }}" >
                                
                                <option value="1" {{ $dish->dish_available ? 'selected' : '' }} class="bg-green-100">Available</option>
                                <option value="0" {{ !$dish->dish_available ? 'selected' : '' }} class="bg-red-100">Not Available</option>
                            </select>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                            No dishes available
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>


    <!--------------------------------------------------Dish Add/Edit Modal ------------------------------------------------------------->
    
    @if($isDishModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="store">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900">Add New Dish</h3>
                        
                        <div class="mt-4">
                            <label for="dish_name" class="block text-sm font-medium text-gray-700">Dish Name</label>
                            <input type="text" wire:model="dish_name" id="dish_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('dish_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="dish_picture" class="block text-sm font-medium text-gray-700">Picture</label>
                            <input type="file" wire:model="dish_picture" id="dish_picture" 
                                class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50"
                                accept="image/*">
                            @if ($dish_picture && !is_string($dish_picture))
                                <div class="mt-2">
                                    <img src="{{ $dish_picture->temporaryUrl() }}" class="h-20 w-20 object-cover rounded">
                                </div>
                            @elseif(is_string($dish_picture))
                                <div class="mt-2">
                                    <img src="{{ asset($dish_picture) }}" class="h-20 w-20 object-cover rounded">
                                </div>
                            @endif
                            @error('dish_picture') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="category" class="block text-sm font-medium text-gray-700">category</label>
                            <input type="text" wire:model="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('category') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="dish_available" class="block text-sm font-medium text-gray-700">Availability</label>
                            <select wire:model="dish_available" id="dish_available" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Select availability</option>
                                <option value="1">Available</option>
                                <option value="0">Not Available</option>
                            </select>
                            @error('dish_available') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="Price" class="block text-sm font-medium text-gray-700">Price</label>
                            <input type="number" wire:model="Price" id="Price" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('Price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Save
                        </button>
                        <button type="button" wire:click="closeDishModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif


</div>
