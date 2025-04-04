<div>
    <div class="mb-4 flex flex-col md:flex-row justify-between items-center">
        <h1 class="text-2xl font-bold mb-4 md:mb-0">Activity Logs</h1>
        
        <!-- Success/Error Messages -->
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
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search logs..." 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >
            </div>
            
            <div>
                <label for="logType" class="block text-sm font-medium text-gray-700 mb-1">Log Type</label>
                <select 
                    wire:model.live="logType" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option value="">All Log Types</option>
                    @foreach($logTypes as $type)
                        <option value="{{ $type }}">{{ str_replace('Log', '', $type) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="causer" class="block text-sm font-medium text-gray-700 mb-1">Actor Type</label>
                <select 
                    wire:model.live="causer" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option value="">All Actors</option>
                    @foreach($causerTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
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
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input 
                    type="date" 
                    wire:model.live="startDate" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >
            </div>
            
            <div>
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

    <!-- Logs Table -->
    <div class="overflow-x-auto shadow-xl rounded-lg">
        <div class="overflow-hidden border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($activityLogs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $log->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ str_contains($log->description, 'created') ? 'bg-green-100 text-green-800' : 
                                      (str_contains($log->description, 'deleted') ? 'bg-red-100 text-red-800' : 
                                      (str_contains($log->description, 'updated') ? 'bg-blue-100 text-blue-800' : 
                                      (str_contains($log->description, 'logged in') ? 'bg-purple-100 text-purple-800' : 
                                      (str_contains($log->description, 'logged out') ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')))) }}">
                                    {{ $this->getEventName($log->description) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                @if($log->causer)
                                    @if($log->causer_type === 'App\Models\Employee')
                                        <span class="flex items-center">
                                            <span class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 mr-2">
                                                <i class="fa fa-user"></i>
                                            </span>
                                            {{ $log->causer->name ?? 'Unknown Employee' }}
                                        </span>
                                    @elseif($log->causer_type === 'App\Models\Customer')
                                        <span class="flex items-center">
                                            <span class="h-8 w-8 rounded-full bg-blue-200 flex items-center justify-center text-blue-600 mr-2">
                                                <i class="fa fa-user-circle"></i>
                                            </span>
                                            {{ $log->causer->customer_name ?? 'Unknown Customer' }}
                                        </span>
                                    @else
                                        <span class="flex items-center">
                                            <span class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 mr-2">
                                                <i class="fa fa-user-circle"></i>
                                            </span>
                                            {{ class_basename($log->causer_type) }} #{{ $log->causer_id }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-gray-500">System</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 max-w-xs truncate">
                                {{ $this->getDetailedDescription($log) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-2 py-1 text-xs rounded-md bg-gray-100">
                                    {{ str_replace(' Log', '', $log->log_name) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                <button 
                                    wire:click="viewLogDetails({{ $log->id }})" 
                                    class="text-amber-500 hover:text-amber-700 mr-2"
                                >
                                    <i class="fa fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p>No activity logs found</p>
                                    @if($search || $logType || $causer || $dateRange != 'today')
                                        <button 
                                            wire:click="$set('search', ''); $set('logType', ''); $set('causer', ''); $set('dateRange', 'today')" 
                                            class="mt-2 px-3 py-1 text-sm text-blue-600 hover:text-blue-800"
                                        >
                                            Clear filters
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $activityLogs->links() }}
    </div>

    <!-- Log Detail Modal -->
    @if($isDetailModalOpen && $selectedLog)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="relative bg-white rounded-lg w-full max-w-4xl">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-xl font-semibold">Activity Log Details</h3>
                    <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal content -->
                <div class="p-6">
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Log Information -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-bold mb-3 text-lg">Log Information</h4>
                            <div class="space-y-2">
                                <p><span class="text-gray-600">Date & Time:</span> {{ $selectedLog->created_at->format('F d, Y h:i:s A') }}</p>
                                <p><span class="text-gray-600">Log ID:</span> {{ $selectedLog->id }}</p>
                                <p><span class="text-gray-600">Type:</span> {{ $selectedLog->log_name }}</p>
                                <p><span class="text-gray-600">IP Address:</span> {{ $selectedLog->properties['ip_address'] ?? 'N/A' }}</p>
                                <p><span class="text-gray-600">Description:</span> {{ $this->getDetailedDescription($selectedLog) }}</p>
                            </div>
                        </div>

                        <!-- Causer Information -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-bold mb-3 text-lg">User Information</h4>
                            <div class="space-y-2">
                                @if($selectedLog->causer)
                                    <p><span class="text-gray-600">User Type:</span> {{ class_basename($selectedLog->causer_type) }}</p>
                                    <p>
                                        <span class="text-gray-600">User:</span> 
                                        @if($selectedLog->causer_type === 'App\Models\Employee')
                                            {{ $selectedLog->causer->name ?? 'N/A' }}
                                        @elseif($selectedLog->causer_type === 'App\Models\Customer')
                                            {{ $selectedLog->causer->customer_name ?? 'N/A' }}
                                        @else
                                            {{ class_basename($selectedLog->causer_type) }} #{{ $selectedLog->causer_id }}
                                        @endif
                                    </p>
                                    <p><span class="text-gray-600">Email:</span> 
                                        @if($selectedLog->causer_type === 'App\Models\Employee')
                                            {{ $selectedLog->causer->employee_email ?? 'N/A' }}
                                        @elseif($selectedLog->causer_type === 'App\Models\Customer')
                                            {{ $selectedLog->causer->customer_email ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                @else
                                    <p class="text-gray-500">No user information available</p>
                                @endif
                            </div>

                            @if($selectedLog->subject)
                            <h4 class="font-bold mt-4 mb-3 text-lg">Target Information</h4>
                            <div class="space-y-2">
                                <p><span class="text-gray-600">Target Type:</span> {{ class_basename($selectedLog->subject_type) }}</p>
                                <p><span class="text-gray-600">Target ID:</span> {{ $selectedLog->subject_id }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Properties Section -->
                    @php
                        $formattedProperties = $this->formatProperties($selectedLog->properties);
                    @endphp

                    @if(count($formattedProperties) > 0)
                    <div class="border-t pt-4">
                        <h4 class="font-bold mb-4 text-lg">Changed Properties</h4>
                        
                        @foreach($formattedProperties as $section => $properties)
                            <div class="mb-4">
                                <h5 class="font-medium text-gray-900 mb-2">{{ $section }}</h5>
                                <div class="bg-gray-50 rounded-lg p-4 overflow-x-auto">
                                    @if(is_array($properties))
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead>
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                @foreach($properties as $key => $value)
                                                    <tr>
                                                        <td class="px-4 py-2 text-sm text-gray-900 font-medium">{{ $key }}</td>
                                                        <td class="px-4 py-2 text-sm text-gray-600">
                                                            @if(is_array($value))
                                                                <pre class="whitespace-pre-wrap bg-gray-100 p-2 rounded text-xs">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                            @elseif(is_null($value))
                                                                <span class="text-gray-400 italic">null</span>
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p class="text-gray-600">{{ $properties }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
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