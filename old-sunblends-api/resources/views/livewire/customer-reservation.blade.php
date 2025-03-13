<div >

    <div class="bg-white flex-grow py-16 relative bg-cover bg-center bg-no-repeat min-h-screen" style="background-image: url('{{ asset('images/menu-bg.png') }}'); background-size: 100% 100%;">
        <div class="max-w-3xl mt-10 mx-auto bg-white shadow-lg rounded-lg p-8">
            
            <h1 class="text-4xl font-bold text-center mb-8">BOOKING TABLE</h1>
            <p class="text-center text-gray-600 italic mb-4">Please fill out the form below to make a reservation.</p>
            <form wire:submit.prevent="store">
                <div class="grid grid-cols-2 gap-6 mt-10">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2" for="reservation_people">
                            Person
                        </label>
                        <select
                            wire:model="reservation_people"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="reservation_people"
                        >
                            <option value="1">1 Person</option>
                            <option value="2">2 Persons</option>
                            <option value="3">3 Persons</option>
                            <option value="4">4 Persons</option>
                        </select>
                        @error('reservation_people') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-2" for="reservation_date">
                            Date
                        </label>
                        <input
                            wire:model="reservation_date"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="reservation_date"
                            type="date"
                        />
                        @error('reservation_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                </div>
                <div class="mt-4">
                    <label class="block text-gray-700 font-bold mb-2" for="reservation_time">
                        Time
                    </label>
                    <input
                        wire:model="reservation_time"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="reservation_time"
                        type="time"
                    />
                    @error('reservation_time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end mt-6">
                    <button
                        type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        BOOKING TABLE
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($successModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4 text-green-600">Success!</h2>
            <p class="text-gray-700">Your table has been successfully booked.</p>
            <button wire:click="closeModal" class="mt-4 bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded">
                Close
            </button>
        </div>
    </div>
    @endif
    




</div>
