<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Total customers
        $totalCustomers = Customer::count();

        // New customers this month
        $newCustomersThisMonth = Customer::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $newCustomersPercent = $totalCustomers > 0 ? round(($newCustomersThisMonth / $totalCustomers) * 100, 1) : 0;

        // Today's bookings
        $todayBookings = Booking::whereDate('created_at', Carbon::today())
            ->count();

        // Revenue this month
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        $monthRevenue = Payment::where('status', 'verified')
            ->whereBetween('payment_date', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount_paid');

        // Revenue last month
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $lastMonthRevenue = Payment::where('status', 'verified')
            ->whereBetween('payment_date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount_paid');

        // Revenue percentage change
        $revenuePercent = 0;
        if ($lastMonthRevenue > 0) {
            $revenuePercent = round((($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1);
        } elseif ($monthRevenue > 0) {
            $revenuePercent = 100;
        }

        // Recent bookings (last 5)
        $recentBookings = Booking::with(['customer.user', 'vehicle'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($booking) {
                return (object)[
                    'id' => $booking->booking_ID,
                    'customer_name' => $booking->customer->user->first_name . ' ' . $booking->customer->user->last_name,
                    'car_name' => $booking->vehicle->brand . ' ' . $booking->vehicle->model,
                    'amount' => $booking->total,
                ];
            });

        // Monthly revenue for chart (all 12 months)
        $monthlyRevenue = [];
        for ($month = 1; $month <= 12; $month++) {
            $start = Carbon::createFromDate(Carbon::now()->year, $month, 1)->startOfMonth();
            $end = Carbon::createFromDate(Carbon::now()->year, $month, 1)->endOfMonth();
            
            $revenue = Payment::where('status', 'verified')
                ->whereBetween('payment_date', [$start, $end])
                ->sum('amount_paid');
            
            $monthlyRevenue[] = $revenue;
        }

        return view('admin.admin_dashboard', [
            'totalCustomers' => $totalCustomers,
            'newCustomersPercent' => $newCustomersPercent,
            'todayBookings' => $todayBookings,
            'monthRevenue' => $monthRevenue,
            'revenuePercent' => $revenuePercent,
            'recentBookings' => $recentBookings,
            'monthlyRevenue' => $monthlyRevenue,
        ]);
    }
}
