<aside class="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('assets/logo/VionAuto-sidebar-logo.png') }}" alt="Staff Logo">
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('staff.vehicles') }}"
               class="{{ request()->routeIs('staff.vehicles') ? 'active' : '' }}">
                <img src="{{ asset('assets/icons/cars.png') }}" alt="dashboard">
                Manage Vehicles
            </a>
        </li>
        <li>
            <a href="{{ route('staff.bookings') }}"
               class="{{ request()->routeIs('staff.bookings') ? 'active' : '' }}">
                <img src="{{ asset('assets/icons/booking.png') }}" alt="manage users">
                Booking
            </a>
        </li>
        <li>
            <a href="{{ route('staff.payments') }}"
               class="{{ request()->routeIs('staff.payments') ? 'active' : '' }}">
                <img src="{{ asset('assets/icons/payment.png') }}" alt="customers">
                Payment
            </a>
        </li>
        <li>
            <a href="{{ route('staff.customers') }}"
               class="{{ request()->routeIs('staff.customers') ? 'active' : '' }}">
                <img src="{{ asset('assets/icons/customers.png') }}" alt="customers">
                Customers
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-icon">
                <img src="{{ asset('assets/icons/staff_prof_icon.png') }}" alt="profile">
            </div>
            <div class="sidebar-user-info">
               {{-- once auth is working uncomment these and remove the placeholder --}}
                <span class="sidebar-user-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
               <span class="sidebar-user-role">{{ ucfirst(auth()->user()->role) }}</span>
                {{-- <span class="sidebar-user-name">Joe Doe</span>
                <span class="sidebar-user-role">Admin</span> --}}
            </div>
        </div>

        <div class="sidebar-divider"></div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">
                <img src="{{ asset('assets/icons/logout.png') }}" alt="logout">
                Log out
            </button>
        </form>
    </div>
</aside>