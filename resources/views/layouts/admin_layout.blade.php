<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — VionAuto</title>

    {{--
        IMPORTANT: We load admin_global.css on every admin page.
        Each admin page then loads its own CSS via @yield('styles').
        app.css is NOT loaded here — it's only for customer/landing pages.
        This stops admin CSS from bleeding into customer pages and vice versa.
    --}}
    @vite(['resources/css/admin_css/admin_global.css', 'resources/js/app.js'])

    {{--
        Each admin blade view adds its own CSS here. Example:
        dashboard.blade.php  → @section('styles') @vite('resources/css/admin_css/admin_dashboard.css') @endsection
        users.blade.php      → @section('styles') @vite('resources/css/admin_css/admin_users.css') @endsection
        reports.blade.php    → @section('styles') @vite('resources/css/admin_css/admin_reports.css') @endsection
        customers.blade.php  → @section('styles') @vite('resources/css/admin_css/admin_customers.css') @endsection
    --}}
    @yield('styles')
</head>

{{--
    The `admin-layout` class on <body> is the scope anchor.
    Every rule in admin CSS files is prefixed with `body.admin-layout`
    so admin styles cannot affect customer or staff pages.
--}}
<body class="admin-layout">

    <div class="wrapper">
        <!-- Hamburger Menu Button for Mobile -->
        <button class="hamburger-btn" onclick="toggleSidebar()">
            <div class="hamburger-menu">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </button>

        @include('layouts.admin_sidebar')

        <main class="main-content">
            @yield('content')
        </main>
    </div>

    @yield('scripts')
    @stack('modals') 
    <script src="{{ asset('javascripts/responsive.js') }}"></script>
     
</body>
</html>