<div>
<div class="p-4">
    <div class="mb-6">
        <h1 class="text-2xl font-bold mb-4">Sales Dashboard</h1>
        
        <!-- Date Range Selector -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="dateRange" class="block text-sm font-medium text-gray-700">Date Range</label>
                    <select id="dateRange" wire:model.live="dateRange" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="this_week">This Week</option>
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                
                @if($dateRange === 'custom')
                <div class="flex-1">
                    <label for="startDate" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" id="startDate" wire:model.live="startDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                
                <div class="flex-1">
                    <label for="endDate" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" id="endDate" wire:model.live="endDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                @endif
            </div>
        </div>
        
        <!-- Sales Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Sales Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Sales</dt>
                            <dd class="text-3xl font-semibold text-gray-900">₱{{ number_format($totalSales, 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            
            <!-- Total Orders Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                            <dd class="text-3xl font-semibold text-gray-900">{{ number_format($totalOrders) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            
            <!-- Average Order Value -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Average Order Value</dt>
                            <dd class="text-3xl font-semibold text-gray-900">₱{{ number_format($averageOrderValue, 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sales Trend Chart -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Sales Trend</h2>
            <div class="h-80">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        
        <!-- Top Dishes -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Top 7 Most Ordered Dishes</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Bar Chart for Top Dishes -->
                <div class="h-80">
                    <canvas id="topDishesChart"></canvas>
                </div>
                
                <!-- Top Dishes List -->
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dish</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($topDishes as $dish)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($dish['image_url'])
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $dish['image_url'] }}" alt="{{ $dish['dish_name'] }}">
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $dish['dish_name'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($dish['total_ordered']) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ₱{{ number_format($dish['total_revenue'], 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No dish orders found in selected date range.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Global chart instances
        let salesChartInstance = null;
        let topDishesChartInstance = null;
        
        // Initialize charts when Livewire updates
        document.addEventListener('livewire:initialized', function () {
            setTimeout(initCharts, 100);
        });
        
        document.addEventListener('livewire:update', function () {
            setTimeout(initCharts, 100);
        });
        
        // Add this to refresh charts when date range changes
        window.addEventListener('chartsUpdated', function() {
            setTimeout(initCharts, 100);
        });
        
        function initCharts() {
            initSalesChart();
            initTopDishesChart();
        }
        
        function initSalesChart() {
            const salesChartElement = document.getElementById('salesChart');
            if (!salesChartElement) return;
            
            const ctx = salesChartElement.getContext('2d');
            
            // Clear any previous chart instance
            if (salesChartInstance) {
                salesChartInstance.destroy();
                salesChartInstance = null;
            }
            
            // Get data from Livewire component
            const salesData = @this.dailySales;
            
            // Create chart with at least default data
            const labels = salesData && salesData.labels ? salesData.labels : ['No Data'];
            const chartData = salesData && salesData.data ? salesData.data : [0];
            
            salesChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Sales Amount',
                        data: chartData,
                        fill: false,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += '₱' + context.parsed.y.toLocaleString();
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        function initTopDishesChart() {
            const topDishesChartElement = document.getElementById('topDishesChart');
            if (!topDishesChartElement) return;
            
            const ctx = topDishesChartElement.getContext('2d');
            
            // Clear any previous chart instance
            if (topDishesChartInstance) {
                topDishesChartInstance.destroy();
                topDishesChartInstance = null;
            }
            
            // Process top dishes data
            const topDishes = @this.topDishes || [];
            
            // If no data, display empty chart with message
            if (!topDishes || topDishes.length === 0) {
                topDishesChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['No data available'],
                        datasets: [{
                            label: 'Orders',
                            data: [0],
                            backgroundColor: 'rgba(200, 200, 200, 0.2)',
                            borderColor: 'rgba(200, 200, 200, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'No dishes data available for selected date range'
                            }
                        }
                    }
                });
                return;
            }
            
            // Create arrays for chart data
            const dishNames = topDishes.map(dish => dish.dish_name);
            const orderCounts = topDishes.map(dish => dish.total_ordered);
            
            // Generate colors
            const backgroundColors = [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)',
                'rgba(199, 199, 199, 0.2)'
            ];
            
            const borderColors = [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(199, 199, 199, 1)'
            ];
            
            topDishesChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dishNames,
                    datasets: [{
                        label: 'Orders',
                        data: orderCounts,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
        
        // Make sure charts are rendered properly after page loads
        window.addEventListener('load', function() {
            setTimeout(initCharts, 300);
        });
        
        // Add event listener for canvas visibility change to redraw charts
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                setTimeout(initCharts, 300);
            }
        });
        
        // Redraw charts on window resize for better responsiveness
        window.addEventListener('resize', function() {
            clearTimeout(window.resizeChartTimer);
            window.resizeChartTimer = setTimeout(initCharts, 200);
        });
    </script>
    @endpush
    
</div>
