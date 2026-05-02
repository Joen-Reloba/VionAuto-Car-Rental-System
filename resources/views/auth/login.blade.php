<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   @vite(['resources/css/auth.css'])
    <title>Login</title>

    <style>
        .bg {
            background-image: url("{{ asset('assets/images/car-rental-bg2.jpg') }}");
        }
    </style>
</head>
<body>

 <body>
    <div class="bg">
    </div>

    <!-- Content on top -->
    <div class="container">
            <div class="login-form">

        <a href="{{ route('landing') }}" class="back-btn">
            <span class="back-btn-icon"><img src="{{ asset('assets/icons/Back.png') }}" alt="Back"></span>
            <span>Back</span>
        </a>

        <div class="center-container">
            <img src="{{ asset('assets/logo/VionAuto-logo.png') }}" alt="VionAuto Logo">
            <p>Sign in to your account</p>
        </div>

      <form action="{{ route('login') }}" method="POST">
            @csrf
            <div>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>

        <div class="login-footer">
            Don't have an account? <a href="/register">Register here</a>
        </div>

    </div>
        </div>

</body>
</html>