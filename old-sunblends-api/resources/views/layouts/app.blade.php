<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Project Management System</title>

    
    <link rel="icon" type="image/png" href="{{ asset('images/aap-logo.png') }}">

    <!-- Styles -->
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">

    @livewireStyles

    <!-- Scripts -->
    @vite('resources/js/app.js')
</head>
<body class="font-sans antialiased">
    <div id="app">
        @yield('content')
    </div>

    @livewireScripts
    @stack('scripts')
    
    
</body>
</html>