<div>
    <div class="mb-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Reservations Dashboard</h1>
    </div>

    <div class="overflow-x-auto shadow-2xl rounded-lg">
        <div class="overflow-hidden border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reservation Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($reservations as $reservation)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $reservation->customer->customer_name ?? 'Guest'}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('F d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('g:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ucfirst($reservation->reservation_type) }}
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-2 py-1 rounded-full inline-block
                                    {{ $reservation->reservation_status == 'approved' ? 'bg-blue-200' : 
                                        ($reservation->reservation_status == 'cancelled' ? 'bg-red-200' : 'bg-yellow-200') }}">
                                    {{ ucfirst($reservation->reservation_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button wire:click="ReservationViewDetails({{$reservation->reservation_id}})" class="bg-amber-500 text-white px-3 py-2 font-medium rounded-md hover:bg-amber-700">View Detail</button>
                                <select wire:change="updateStatus({{ $reservation->reservation_id }}, $event.target.value)" class="ml-2 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                                {{ $reservation->reservation_status == 'approved' ? 'bg-blue-200' : ($reservation->reservation_status == 'cancelled' ? 'bg-red-200' : 'bg-yellow-200') }}">
                                    <option value="pending" {{ $reservation->reservation_status == 'pending' ? 'selected' : '' }} class="bg-yellow-200">Pending</option>
                                    <option value="approved" {{ $reservation->reservation_status == 'approved' ? 'selected' : '' }} class="bg-blue-200">Approved</option>
                                    <option value="cancelled" {{ $reservation->reservation_status == 'cancelled' ? 'selected' : '' }} class="bg-red-200">Cancelled</option>
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div> 
    </div>

    @if($ReservationDetailModal)
     <!-- Detail Modal -->

     
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="relative bg-white rounded-lg w-full max-w-4xl">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-xl font-semibold">Reservation Details</h3>
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
                                <p><b>Name:</b> {{ $reservationDetail->customer->customer_name ?? 'Guest' }}</p>
                                <p><b>Email:</b> {{ $reservationDetail->customer->customer_email ?? 'null'}}</p>
                                <p><b>Phone:</b> {{ $reservationDetail->customer->customer_number ?? 'null' }}</p>
                            </div>
                            <div>
                                <h4 class="font-bold mb-2">Reservation Information</h4>
                                <p><b>Reservation ID:</b> #{{ $reservationDetail->reservation_id }}</p>
                                <p><b>Status:</b> {{ ucfirst($reservationDetail->reservation_status) }}</p>
                                
                                
                                <p><b>Reservation Date:</b> {{ \Carbon\Carbon::parse($reservationDetail->reservation_date)->format('F d, Y') }}</p>
                                <p><b>Reservation Time:</b> {{ \Carbon\Carbon::parse($reservationDetail->reservation_time)->format('g:i A') }}</p>

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
