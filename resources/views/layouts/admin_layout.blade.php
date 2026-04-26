<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin_css/admin_global.css') }}">
    @yield('styles')
   
</head>
<body>

    <div class="wrapper"> 
        @include('layouts.admin_sidebar')

        <main class="main-content">
            @yield('content')
        </main>
    </div>

      @yield('scripts')
</body>
</html>