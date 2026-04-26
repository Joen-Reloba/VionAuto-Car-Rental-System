<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $reportType = $request->get('report_type', 'rentals');
        $dateFrom = $request->get('date_from', now()->startOfWeek()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        
        // Calculate stats
        $stats = [
            'total_revenue' => Payment::sum('amount_paid'),
            'total_rentals' => Booking::count(),
            'total_customers' => Customer::count(),
            'total_transactions' => Payment::count(),
            'revenue_growth' => $this->calculateGrowth('revenue'),
            'rentals_growth' => $this->calculateGrowth('rentals'),
            'customers_growth' => $this->calculateGrowth('customers'),
            'transactions_growth' => $this->calculateGrowth('transactions'),
        ];
        
        // Get records based on report type
        $records = $this->getRecords($reportType, $dateFrom, $dateTo);
        
        return view('admin.admin_reports', compact('stats', 'records'));
    }
    
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $reportType = $request->get('report_type', 'rentals');
        $dateFrom = $request->get('date_from', now()->startOfWeek()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        
        if ($format === 'pdf') {
            return $this->exportPDF($reportType, $dateFrom, $dateTo);
        } else {
            return $this->exportCSV($reportType, $dateFrom, $dateTo);
        }
    }
    
    private function exportCSV($reportType, $dateFrom, $dateTo)
    {
        if ($reportType === 'revenue') {
            $records = Payment::with(['booking.customer', 'booking.vehicle'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->get();
            
            $filename = "revenue-report-" . now()->format('Y-m-d-His') . ".csv";
            $headers = ['Transaction ID', 'Customer', 'Vehicle', 'Date Paid', 'Amount', 'Payment Method'];
            $rows = $records->map(function ($transaction) {
                return [
                    '#' . str_pad($transaction->payment_ID, 6, '0', STR_PAD_LEFT),
                    $transaction->booking?->customer?->name ?? '—',
                    $transaction->booking?->vehicle?->name ?? '—',
                    \Carbon\Carbon::parse($transaction->payment_date)->format('M d, Y'),
                    number_format($transaction->amount_paid, 2),
                    $transaction->payment_type === 'downpayment' ? 'Down Payment' : 'Final Payment'
                ];
            });
        } elseif ($reportType === 'users') {
            $records = User::where('role', 'customer')
                ->withCount('bookings')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->get();
            
            $filename = "users-report-" . now()->format('Y-m-d-His') . ".csv";
            $headers = ['Name', 'Email', 'Role', 'Date Registered', 'Total Rentals'];
            $rows = $records->map(function ($user) {
                return [
                    $user->name,
                    $user->email,
                    ucfirst($user->role),
                    \Carbon\Carbon::parse($user->created_at)->format('M d, Y'),
                    $user->bookings_count
                ];
            });
        } else {
            $records = Booking::with(['customer', 'vehicle'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->get();
            
            $filename = "rentals-report-" . now()->format('Y-m-d-His') . ".csv";
            $headers = ['ID', 'Customer Name', 'Vehicle', 'Rental Date', 'Return Date', 'Amount', 'Status'];
            $rows = $records->map(function ($rental) {
                return [
                    $rental->booking_ID,
                    $rental->customer?->name ?? '—',
                    $rental->vehicle?->name ?? '—',
                    \Carbon\Carbon::parse($rental->rent_start)->format('M d, Y'),
                    \Carbon\Carbon::parse($rental->rent_end)->format('M d, Y'),
                    number_format($rental->total, 2),
                    ucfirst($rental->status)
                ];
            });
        }
        
        return $this->generateCSV($filename, $headers, $rows);
    }
    
    private function generateCSV($filename, $headers, $rows)
    {
        $callback = function () use ($headers, $rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
        
        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    private function exportPDF($reportType, $dateFrom, $dateTo)
    {
        // For now, return a simple message. In production, use TCPDF, DOMPDF or similar
        return response()->json([
            'message' => 'PDF export is not yet implemented. Please use CSV export instead.',
            'status' => 'info'
        ]);
    }
    
    private function getRecords($reportType, $dateFrom, $dateTo)
    {
        if ($reportType === 'revenue') {
            return $this->getRevenueRecords($dateFrom, $dateTo);
        } elseif ($reportType === 'users') {
            return $this->getUsersRecords($dateFrom, $dateTo);
        } else {
            return $this->getRentalRecords($dateFrom, $dateTo);
        }
    }
    
    private function getRentalRecords($dateFrom, $dateTo)
    {
        return Booking::with(['customer.user', 'vehicle'])
            ->where('status', 'finished')  // ← add this
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->paginate(10);
    }

    private function getRevenueRecords($dateFrom, $dateTo)
    {
        return Payment::with(['booking.customer.user', 'booking.vehicle'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->paginate(10);
    }
    
    private function getUsersRecords($dateFrom, $dateTo)
{
    return User::where('role', 'customer')
        ->with(['bookings' => function ($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('bookings.created_at', [$dateFrom, $dateTo]);  // ← qualified
        }])
        ->whereBetween('users.created_at', [$dateFrom, $dateTo])  // ← qualified
        ->withCount('bookings as rentals_count')
        ->paginate(10);
}
    private function calculateGrowth($type)
    {
        $today = Carbon::today();
        $lastWeek = $today->copy()->subWeek();
        $twoWeeksAgo = $today->copy()->subWeeks(2);
        
        if ($type === 'revenue') {
            $currentWeek = Payment::whereBetween('created_at', [$lastWeek, $today])->sum('amount_paid');
            $previousWeek = Payment::whereBetween('created_at', [$twoWeeksAgo, $lastWeek])->sum('amount_paid');
        } elseif ($type === 'rentals') {
            $currentWeek = Booking::whereBetween('created_at', [$lastWeek, $today])->count();
            $previousWeek = Booking::whereBetween('created_at', [$twoWeeksAgo, $lastWeek])->count();
        } elseif ($type === 'customers') {
            $currentWeek = Customer::whereBetween('created_at', [$lastWeek, $today])->count();
            $previousWeek = Customer::whereBetween('created_at', [$twoWeeksAgo, $lastWeek])->count();
        } elseif ($type === 'transactions') {
            $currentWeek = Payment::whereBetween('created_at', [$lastWeek, $today])->count();
            $previousWeek = Payment::whereBetween('created_at', [$twoWeeksAgo, $lastWeek])->count();
        }
        
        if ($previousWeek == 0) {
            return $currentWeek > 0 ? 100 : 0;
        }
        
        return round((($currentWeek - $previousWeek) / $previousWeek) * 100, 2);
    }
}


