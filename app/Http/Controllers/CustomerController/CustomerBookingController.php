<?php

namespace App\Http\Controllers\CustomerController;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CustomerBookingController extends Controller
{
    public function index(Request $request)
    {
        // Get all bookings for the logged-in customer
        $bookings = Booking::with([
            'vehicle',
            'vehicle.images'
        ])
        ->where('customer_user_id', Auth::id())
        ->orderBy('created_at', 'desc')
        ->get();

        // Get filter status from query parameter (from notification redirect)
        $filterStatus = $request->query('status', 'all');
        $highlightBookingId = $request->query('booking', null);

        return view('customer.customer_bookings', compact('bookings', 'filterStatus', 'highlightBookingId'));
    }

    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'vehicle_ID' => 'required|exists:vehicles,vehicle_ID',
            'trip_start' => 'required|date',
            'trip_end' => 'required|date|after:trip_start',
            'customer_name' => 'required|string|max:255',
            'total' => 'required|numeric|min:0',
            'downpayment' => 'required|numeric|min:0',
        ]);

        try {
            // Get vehicle to verify it exists and is available
            $vehicle = Vehicle::where('vehicle_ID', $validated['vehicle_ID'])
                ->where('status', 'available')
                ->firstOrFail();

            // Get customer user ID (from auth if logged in)
            $customerUserId = Auth::id();

            // Create booking
            $booking = Booking::create([
                'vehicle_ID' => $validated['vehicle_ID'],
                'customer_user_id' => $customerUserId,
                'approved_by_user_id' => null,
                'rent_start' => $validated['trip_start'],
                'rent_end' => $validated['trip_end'],
                'downpayment' => $validated['downpayment'],
                'subtotal' => $validated['total'],
                'tax' => 0, // Tax can be calculated later if needed
                'total' => $validated['total'],
                'status' => 'pending', // Default status
                'payment_status' => 'unpaid', // Default payment status
                'returned_at' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully',
                'booking_id' => $booking->booking_ID,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating booking: ' . $e->getMessage(),
            ], 500);
        }
    }
}
