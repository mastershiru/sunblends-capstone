<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Sunblends Management System</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">

    @livewireStyles
    <style>
        .sidebar-closed {
            transform: translateX(-16rem); /* Change from -100% to actual width */
            width: 16rem;
        }

        .sidebar-open {
            transform: translateX(0);
            width: 16rem;
        }

        /* Add this to ensure the sidebar starts in the correct position */
        #sidebar {
            transition: transform 0.3s ease-in-out;
            width: 16rem; /* Set initial width */
        }

        .content-expanded {
            margin-left: 16rem;
        }

        .content-full {
            margin-left: 0;
        }

        /* Toggle button styles */
        .sidebar-toggle {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 40;
            background-color: #1f2937;
            color: white;
            padding: 0.5rem;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .sidebar-toggle.open {
            left: 17rem;
        }

        /* Responsive styles */
        @media (max-width: 1024px) {

            

            .content-expanded {
                margin-left: 0;
            }
            
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 25;
                display: none;
            }

            .sidebar-overlay.active {
                display: block;
            }
        }
    </style>
</head>

<body class="bg-zinc-300">
    <div id="dashboard" class="h-screen">

        <!-- Toggle Button -->
        <button id="sidebarToggle" class="sidebar-toggle open">
                <svg class="w-6 h-6 toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
        </button>

        <div id="sidebarOverlay" class="sidebar-overlay"></div>
        
        <!-- Sidebar -->
        <div id="sidebar" class="fixed top-0 left-0 h-full bg-zinc-200 text-black transition-all duration-300 ease-in-out z-30 overflow-y-auto">
            <div class="flex flex-col h-full">
                <!-- Profile Section -->
                <div class="p-4 bg-zinc-200">
                    <div class="flex items-center space-x-3">
                        <a href="{{ url('/home') }}">         
                            <img src="{{ asset('images/logo.png') }}"  alt="logo">    
                        </a>                                         
                        <div>
                            <p class="font-semibold text-sm "> </p>
                            <p class="text-xs"></p>
                            
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-grow p-4">
                    <h2 class="text-xl font-bold mb-4 text-center">Dashboard</h2>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ url('/dashboard')}}" class="flex items-center p-2 rounded-lg hover:bg-amber-500 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                <span>Home</span>
                            </a>
                        </li>
                        
                        <li><a href="{{ url('/reservation/Dashboard')}}" class="flex items-center p-2 rounded-lg hover:bg-amber-500 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Reservation</span>
                        </a></li>
                        
                        <li><a href="{{ url('/menu')}}" class="flex items-center p-2 rounded-lg hover:bg-amber-500 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            <span>Menu</span>
                        </a></li>
                        
                        <li><a href="{{ url('/Dine-in') }}" class="flex items-center p-2 rounded-lg hover:bg-amber-500 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                            <span>Dine-in Orders</span>
                        </a></li>
                        
                        <li><a href="{{ url('/Orders-Queue') }}" class="flex items-center p-2 rounded-lg hover:bg-amber-500 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <span>Orders Queue</span>
                        </a></li>

                        <li><a href="{{ url('/CMS') }}" class="flex items-center p-2 rounded-lg hover:bg-amber-500 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span>Transactions</span>
                            </a>
                        </li>

                        <li><a href="{{ url('/ActivityLogs') }}" class="flex items-center p-2 rounded-lg hover:bg-amber-500 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <span>Inventory</span>
                        </a></li>
                        
                        <li><a href="{{ url('/Settings') }}" class="flex items-center p-2 rounded-lg hover:bg-amber-500 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Reports</span>
                        </a></li>
                        
                        <li><a href="{{ url('/Settings') }}" class="flex items-center p-2 rounded-lg hover:bg-amber-500 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            <span>Audit Logs</span>
                        </a></li>

                        <li><a href="http://127.0.0.1:3000" class="flex items-center p-2 rounded-lg hover:bg-amber-500 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Website</span>
                        </a></li>
                        

                    </ul>
                </nav>
                

                <!-- Logout -->
                <div class="p-4">
                    <form action="{{ url('/logout') }}" method="GET" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center p-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div id="main-content" class="flex-grow shrink basis-1 p-6 transition-all duration-300 content-expanded">
        <div class="flex justify-end "><h1 class="mt-5"><br></h1></div>
            @yield('content')
        </div>
    </div>

    <script>
        document.addEventListener('click', function() {     
                Livewire.dispatch('closeDropdowns');
        });
    </script>

    <script>
       document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const toggleButton = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleIcon = toggleButton.querySelector('.toggle-icon');
        
        function toggleSidebar() {
            const isSidebarOpen = !sidebar.classList.contains('sidebar-closed');
            
            if (isSidebarOpen) {
                // Close sidebar
                sidebar.classList.add('sidebar-closed');
                sidebar.classList.remove('sidebar-open');
                mainContent.classList.remove('content-expanded');
                mainContent.classList.add('content-full');
                toggleButton.classList.remove('open');
                overlay.classList.remove('active');
                // Change icon to point right
                toggleIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>`;
            } else {
                // Open sidebar
                sidebar.classList.remove('sidebar-closed');
                sidebar.classList.add('sidebar-open');
                if (window.innerWidth > 1024) {
                    mainContent.classList.add('content-expanded');
                    mainContent.classList.remove('content-full');
                }
                toggleButton.classList.add('open');
                overlay.classList.add('active');
                // Change icon to point left
                toggleIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>`;
            }
        }

        toggleButton.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);

        // Handle responsive behavior
        function handleResize() {
            if (window.innerWidth <= 1024) {
                // Automatically close sidebar on mobile/tablet
                sidebar.classList.add('sidebar-closed');
                sidebar.classList.remove('sidebar-open');
                mainContent.classList.remove('content-expanded');
                mainContent.classList.add('content-full');
                toggleButton.classList.remove('open');
                overlay.classList.remove('active');
                toggleIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>`;
            } else if (!sidebar.classList.contains('sidebar-closed')) {
                // Reset to desktop view if sidebar was open
                sidebar.classList.remove('sidebar-closed');
                sidebar.classList.add('sidebar-open');
                mainContent.classList.add('content-expanded');
                mainContent.classList.remove('content-full');
                toggleButton.classList.add('open');
                toggleIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>`;
            }
        }

        // Add debounce to prevent too many resize events
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(handleResize, 250);
        });

        // Initial check
        handleResize();
    });
    </script>

    @livewireScripts
    @vite('resources/js/app.js')


    

    
</body>
</html>



</script>