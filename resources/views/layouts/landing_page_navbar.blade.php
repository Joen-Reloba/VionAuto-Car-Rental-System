<!-- Navigation Bar -->
<nav class="navbar">
    <div class="navbar-container">
        <!-- Logo Placeholder -->
        <div class="logo">
            <div class="logo-placeholder"><img src="{{asset('assets/logo/vertical-logo.png')}}" alt="VionAuto Logo"></div>
        </div>

        <!-- Mobile Menu Button -->
        <button class="mobile-menu-btn" onclick="toggleMobileNavMenu()">
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </button>

        <!-- Navigation Links -->
        <div class="nav-links">
            <a href="#home" class="nav-link"><img src="{{asset('assets/icons/Home.png')}}" alt="">Home</a>
            <a href="#cars" class="nav-link"><img src="{{asset('assets/icons/cars.png')}}" alt="">Cars</a>
            <a href="#about" class="nav-link"><img src="{{asset('assets/icons/about-us.png')}}" alt="">About us</a>
        </div>

        <!-- Login Button or Profile Section -->
        @if(Auth::check())
            <!-- Notifications Icon -->
            <div class="notifications-icon-wrapper">
                <button class="notifications-icon" onclick="toggleNotificationsDropdown()" title="View Booking Notifications">
                    <!-- PLACEHOLDER: Replace this div with your icon -->
                    <!-- You can use Font Awesome, SVG, or image here -->
                    <div class="notification-icon-placeholder">
                        <img src="{{asset('assets/icons/Notification.png')}}" alt="notifications">
                    </div>
                    <!-- Badge for notification count -->
                    <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                </button>
                <!-- Notifications Dropdown Placeholder -->
                <div class="notifications-dropdown" id="notificationsDropdown">
                    <div class="dropdown-header">Booking Notifications</div>
                    <div class="notifications-list" id="notificationsList">
                        <p class="empty-notification">No new notifications</p>
                    </div>
                </div>
            </div>

            <div class="profile-section">
                <button class="profile-trigger" onclick="toggleProfileDropdown()">
                    <div class="profile-circle">
                        <img src="{{asset('assets/icons/Customer.png')}}" alt="Profile">
                    </div>
                    <span class="username">{{ Auth::user()->username }}</span>
                </button>
                <div class="profile-dropdown" id="profileDropdown">
                    <a href="{{ route('customer.profile') }}" class="dropdown-item">
                        <img src="{{ asset('assets/icons/Customer.png') }}" alt="My Profile">
                        My Profile
                    </a>
                    <a href="{{ route('customer.bookings') }}" class="dropdown-item">
                        <img src="{{asset('assets/icons/booking.png')}}" alt="My Bookings">
                        My Bookings
                    </a>
                    <a href="{{ route('customer.payments') }}" class="dropdown-item">
                        <img src="{{asset('assets/icons/payment.png')}}" alt="My Payments">
                        My Payments
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="dropdown-item logout-item">
                        @csrf
                        <button type="submit" class="logout-link">
                            <img src="{{asset('assets/icons/logout.png')}}" alt="Logout">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="login-btn">Login</a>
        @endif
    </div>
</nav>

<script src="{{ asset('javascripts/customer_js/notification.js') }}"></script>
<script src="{{ asset('javascripts/responsive.js') }}"></script>
