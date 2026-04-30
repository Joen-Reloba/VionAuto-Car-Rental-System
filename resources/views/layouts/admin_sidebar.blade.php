<aside class="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('assets/logo/VionAuto-sidebar-logo.png') }}" alt="Admin Logo">
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('admin.dashboard') }}"
               class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <img src="{{ asset('assets/icons/dashboard.png') }}" alt="dashboard">
                Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('admin.users') }}"
               class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <img src="{{ asset('assets/icons/manage-user.png') }}" alt="manage users">
                Manage Users
            </a>
        </li>

        <li>
            <a href="{{ route('admin.reports') }}"
               class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                <img src="{{ asset('assets/icons/reports.png') }}" alt="reports">
                Reports
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-icon">
                <img src="{{ asset('assets/icons/admin_prof_icon.png') }}" alt="profile">
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