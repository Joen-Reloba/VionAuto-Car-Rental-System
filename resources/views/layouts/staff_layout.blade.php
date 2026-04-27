<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff</title>
    <link rel="stylesheet" href="{{ asset('css/staff_css/staff_global.css') }}">
    @yield('styles')
</head>
<body>

    <div class="wrapper"> 
        <!-- Hamburger Menu Button for Mobile -->
        <button class="hamburger-btn" onclick="toggleSidebar()">
            <div class="hamburger-menu">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </button>

        @include('layouts.staff_sidebar')

        <main class="main-content">
            @yield('content')
        </main>
    </div>

    @yield('scripts')

    <script src="{{ asset('javascripts/responsive.js') }}"></script>
</body>
</html>