<div>
    <div class="mb-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Employee Management</h1>
        <button wire:click="openModal" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-md">
            Add Employee
        </button>
    </div>

    <!-- Search and Per Page -->
    <div class="mb-4 flex justify-between items-center">
        <div class="flex items-center">
            <input wire:model.debounce.300ms="search" type="text" placeholder="Search employees..." class="border rounded-md p-2 mr-2">
            <select wire:model="perPage" class="border rounded-md p-2">
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
                <option value="100">100 per page</option>
            </select>
        </div>
    </div>

    <!-- Employee Table -->
    <div class="overflow-x-auto shadow-2xl rounded-lg">
        <table class="min-w-full bg-white">
            <thead>
                <tr class="bg-zinc-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left cursor-pointer" wire:click="sortBy('employee_id')">
                        ID 
                        @if($sortField === 'employee_id')
                            <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                        @endif
                    </th>
                    <th class="py-3 px-6 text-left cursor-pointer" wire:click="sortBy('name')">
                        Name
                        @if($sortField === 'name')
                            <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                        @endif
                    </th>
                    <th class="py-3 px-6 text-left cursor-pointer" wire:click="sortBy('employee_email')">
                        Email
                        @if($sortField === 'employee_email')
                            <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                        @endif
                    </th>
                    <th class="py-3 px-6 text-left cursor-pointer" wire:click="sortBy('employee_number')">
                        Employee Number
                        @if($sortField === 'employee_number')
                            <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                        @endif
                    </th>
                    <th class="py-3 px-6 text-left">Role</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm">
                @foreach($employees as $employee)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{ $employee->employee_id }}</td>
                        <td class="py-3 px-6 text-left">
                            <div class="flex items-center">
                                @if($employee->employee_picture)
                                    <div class="mr-2">
                                        <img class="w-8 h-8 rounded-full" src="{{ Storage::url($employee->employee_picture) }}" alt="{{ $employee->name }}">
                                    </div>
                                @else
                                    <div class="mr-2">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500">{{ substr($employee->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                @endif
                                <span>{{ $employee->name }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-6 text-left">{{ $employee->employee_email }}</td>
                        <td class="py-3 px-6 text-left">{{ $employee->employee_number }}</td>
                        <td class="py-3 px-6 text-left">{{ $employee->role ? $employee->role->name : 'No Role' }}</td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center">
                                <button wire:click="viewDetails({{ $employee->employee_id }})" class="text-blue-600 hover:text-blue-900 mr-2">
                                    View
                                </button>
                                @if($this->canEdit($employee->employee_id))
                                    <button wire:click="edit({{ $employee->employee_id }})" class="text-amber-600 hover:text-amber-900 mr-2">
                                        Edit
                                    </button>
                                @endif
                                @if($this->canDelete($employee->employee_id))
                                    <button wire:click="confirmDelete({{ $employee->employee_id }})" class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $employees->links() }}
    </div>

    <!-- Create/Edit Modal -->
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50" style="display: {{ $isModalOpen ? 'flex' : 'none' }}">
        <div class="relative p-8 bg-white w-full max-w-2xl m-auto rounded-md shadow-lg">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold text-gray-800">{{ $editMode ? 'Edit Employee' : 'Add New Employee' }}</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form wire:submit.prevent="store">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" id="name" wire:model="name" class="border rounded-md p-2 w-full @error('name') border-red-500 @enderror">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="employee_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="employee_email" wire:model="employee_email" class="border rounded-md p-2 w-full @error('employee_email') border-red-500 @enderror">
                        @error('employee_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="employee_number" class="block text-sm font-medium text-gray-700 mb-1">Employee Number</label>
                        <input type="text" id="employee_number" wire:model="employee_number" class="border rounded-md p-2 w-full @error('employee_number') border-red-500 @enderror">
                        @error('employee_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select id="role_id" wire:model="role_id" class="border rounded-md p-2 w-full @error('role_id') border-red-500 @enderror">
                            <option value="">Select a role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password {{ $editMode ? '(leave blank to keep current)' : '' }}</label>
                        <input type="password" id="password" wire:model="password" class="border rounded-md p-2 w-full @error('password') border-red-500 @enderror">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" id="password_confirmation" wire:model="password_confirmation" class="border rounded-md p-2 w-full">
                    </div>

                    <div class="col-span-2">
                        <label for="temp_image" class="block text-sm font-medium text-gray-700 mb-1">Profile Picture</label>
                        <input type="file" id="temp_image" wire:model="temp_image" class="border rounded-md p-2 w-full @error('temp_image') border-red-500 @enderror">
                        @error('temp_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        
                        <div wire:loading wire:target="temp_image" class="text-sm text-gray-500 mt-1">Uploading...</div>
                        
                        @if($temp_image)
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Image Preview:</p>
                                <img src="{{ $temp_image->temporaryUrl() }}" class="mt-1 h-20 w-20 object-cover rounded-full">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="button" wire:click="closeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md mr-2">
                        Cancel
                    </button>
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-md">
                        {{ $editMode ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Details Modal -->
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50" style="display: {{ $isDetailsModalOpen ? 'flex' : 'none' }}">
        <div class="relative p-8 bg-white w-full max-w-2xl m-auto rounded-md shadow-lg">
            @if($selectedEmployee)
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold text-gray-800">Employee Details</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/3 flex justify-center mb-4 md:mb-0">
                    @if($selectedEmployee->employee_picture)
                        <img class="w-48 h-48 rounded-full object-cover" src="{{ Storage::url($selectedEmployee->employee_picture) }}" alt="{{ $selectedEmployee->name }}">
                    @else
                        <div class="w-48 h-48 rounded-full bg-gray-200 flex items-center justify-center">
                            <span class="text-5xl text-gray-500">{{ substr($selectedEmployee->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                
                <div class="md:w-2/3 md:pl-6">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Name</p>
                            <p class="text-lg">{{ $selectedEmployee->name }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="text-lg">{{ $selectedEmployee->employee_email }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Employee Number</p>
                            <p class="text-lg">{{ $selectedEmployee->employee_number }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Role</p>
                            <p class="text-lg">{{ $selectedEmployee->role ? $selectedEmployee->role->name : 'No Role' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="button" wire:click="closeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md mr-2">
                    Close
                </button>
                @if($this->canEdit($selectedEmployee->employee_id))
                    <button type="button" wire:click="edit({{ $selectedEmployee->employee_id }})" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-md">
                        Edit
                    </button>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50" style="display: {{ $isDeleteModalOpen ? 'flex' : 'none' }}">
        <div class="relative p-8 bg-white w-full max-w-md m-auto rounded-md shadow-lg">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-800">Confirm Delete</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <p class="text-md text-gray-600 mb-6">Are you sure you want to delete this employee? This action cannot be undone.</p>
            
            <div class="flex justify-end">
                <button type="button" wire:click="closeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md mr-2">
                    Cancel
                </button>
                <button type="button" wire:click="deleteEmployee" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Message -->
    @if(session()->has('success'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-500 text-white px-4 py-2 rounded-md shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif
</div>