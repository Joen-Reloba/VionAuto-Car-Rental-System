
@extends('layouts.admin_layout')

@section('styles')
   @vite(['resources/css/admin_css/admin_dashboard.css'])
@endsection

@section('content')
 
<div class="dash-header">
    <div>
        <h1 class="dash-title">Dashboard</h1>
    </div>
    <div class="dash-date" id="dash-date">{{ now()->format('l, F j, Y') }}</div>
</div>
 
{{-- Stat Cards --}}
<div class="stat-grid">
 
        {{-- Total Customers Card - clicks to Reports --}}
    <a href="{{ route('admin.users') }}" class="stat-card card-users" style="text-decoration: none;">
        <div class="stat-icon-wrap">
            <img src="{{asset('assets/icons/customers.png')}}" alt="Customers">
        </div>
        <div class="stat-body">
            <p class="stat-label">Total Customers</p>
            <h2 class="stat-value">{{ $totalCustomers ?? '—' }}</h2>
            <span class="stat-badge badge-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                {{ $newCustomersPercent ?? '0' }}% this month
            </span>
        </div>
    </a>
    
    <div class="stat-card card-bookings">
        <div class="stat-icon-wrap">
            <img src="{{asset('assets/icons/Calendar.png')}}" alt="Calendar">
        </div>
        <div class="stat-body">
            <p class="stat-label">Today's Bookings</p>
            <h2 class="stat-value">{{ $todayBookings ?? '—' }}</h2>
            <span class="stat-badge badge-blue">
                Updated just now
            </span>
        </div>
    </div>
 
        {{-- Revenue Card - clicks to Reports --}}
    <a href="{{ route('admin.reports') }}" class="stat-card card-revenue" style="text-decoration: none;">
        <div class="stat-icon-wrap">
        <img src="{{asset('assets/icons/dollar.png')}}" alt="">
        </div>
        <div class="stat-body">
            <p class="stat-label">Revenue This Month</p>
            <h2 class="stat-value">₱{{ number_format($monthRevenue ?? 0, 2) }}</h2>
            <span class="stat-badge badge-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                {{ $revenuePercent ?? '0' }}% from last month
            </span>
        </div>
    </a>
 
</div>
 
{{-- Chart + Recent Bookings --}}
<div class="bottom-grid">
 
    <div class="chart-card">
        <div class="chart-card-header">
            <div>
                <h3 class="chart-title">Monthly Revenue</h3>
                <p class="chart-sub">{{ now()->year }} overview</p>
            </div>
        </div>
        <div class="chart-area">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
 
    <div class="recent-card">
        <div class="chart-card-header">
            <h3 class="chart-title">Recent Bookings</h3>
        </div>
        <div class="recent-list">
            @forelse($recentBookings ?? [] as $booking)
            <div class="recent-row">
                <div class="recent-avatar">{{ strtoupper(substr($booking->customer_name ?? 'U', 0, 1)) }}</div>
                <div class="recent-info">
                    <p class="recent-name">{{ $booking->customer_name ?? 'Unknown' }}</p>
                    <p class="recent-car">{{ $booking->car_name ?? '—' }}</p>
                </div>
                <span class="recent-amount">₱{{ number_format($booking->amount ?? 0, 0) }}</span>
            </div>
            @empty
            <p class="empty-state">No recent bookings.</p>
            @endforelse
        </div>
    </div>
 
</div>
 
@endsection
 
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Date display
    const dateElement = document.getElementById('dash-date');
    if (dateElement) {
        dateElement.textContent = new Date().toLocaleDateString('en-US', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    }
 
    // Monthly Revenue Chart
   const monthlyData = {!! json_encode($monthlyRevenue ?? [0,0,0,0,0,0,0,0,0,0,0,0]) !!};
 
    const ctx = document.getElementById('revenueChart')?.getContext('2d');
    
    if (ctx) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.3)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0.01)');
     
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                datasets: [{
                    label: 'Revenue',
                    data: monthlyData,
                    borderColor: '#6366f1',
                    borderWidth: 2.5,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.45,
                    pointBackgroundColor: '#6366f1',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e1b4b',
                        titleColor: '#a5b4fc',
                        bodyColor: '#fff',
                        padding: 12,
                        callbacks: {
                            label: ctx => ' ₱' + ctx.parsed.y.toLocaleString()
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { font: { family: 'DM Sans', size: 12 }, color: '#94a3b8' }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            font: { family: 'DM Sans', size: 12 },
                            color: '#94a3b8',
                            callback: v => '₱' + v.toLocaleString()
                        }
                    }
                }
            }
        });
    }
</script>
@endsection
