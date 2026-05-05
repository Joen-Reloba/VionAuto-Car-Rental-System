<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff — VionAuto</title>

    {{--
        staff_global.css loads on every staff page (sidebar, layout shell).
        Each staff blade view loads its own page CSS via @yield('styles').
        app.css is NOT loaded here — it's only for customer/landing pages.
    --}}
    @vite(['resources/css/staff_css/staff_global.css', 'resources/js/app.js'])

    {{--
        Each staff blade adds its own CSS here. Examples:
        staff_bookings.blade.php   → @vite('resources/css/staff_css/staff_bookings.css')
        staff_customers.blade.php  → @vite('resources/css/staff_css/staff_customers.css')
        staff_vehicles.blade.php   → @vite('resources/css/staff_css/staff_vehicles.css')
        staff_payments.blade.php   → @vite('resources/css/staff_css/staff_payment.css')
        staff_add_vehicle.blade.php    → @vite('resources/css/staff_css/staff_vehicles.css')
        staff_update_vehicle.blade.php → @vite('resources/css/staff_css/staff_vehicles.css')
    --}}
    @yield('styles')
</head>

{{--
    The `staff-layout` class on <body> is the CSS scope anchor.
    Every rule in staff CSS files is prefixed with `body.staff-layout`
    so staff styles cannot affect customer or admin pages.
--}}
<body class="staff-layout">

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
    @stack('modals')
    <script src="{{ asset('javascripts/responsive.js') }}"></script>
</body>
</html>