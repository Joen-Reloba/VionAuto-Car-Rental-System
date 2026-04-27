<?php

namespace App\Http\Controllers\StaffController;

use App\Models\Booking;
use App\Models\BookingNotification;
use App\Models\Staff;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        // Fetch all bookings with relationships
        $bookings = Booking::with([
            'customer.user',
            'vehicle.images',
            'approvedBy'
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        // Format bookings data with calculated totals
        $bookingsData = $bookings->map(function ($booking) {
            $vehicle = $booking->vehicle;
            $customer = $booking->customer;
            
            // Calculate days rented
            $daysRented = $booking->rent_start->diffInDays($booking->rent_end);
            if ($daysRented == 0) $daysRented = 1; // Minimum 1 day
            
            // Calculate total: (daily_rate * days) + VAT
            $subtotal = $vehicle->daily_rate * $daysRented;
            $vat = $subtotal * 0.12; // 12% VAT
            $total = $subtotal + $vat;
            
            // Get vehicle image
            $vehicleImage = $vehicle->images->where('is_primary', true)->first() ?? $vehicle->images->first();
            $imagePath = null;
            if ($vehicleImage && $vehicleImage->img_path) {
                $imagePath = asset('assets/images/images-vehicles/' . $vehicleImage->img_path);
            }
            
            return [
                'booking_ID' => $booking->booking_ID,
                'customer_name' => $customer->user->first_name . ' ' . $customer->user->last_name,
                'customer_phone' => $customer->user->phone_number,
                'customer_email' => $customer->user->email,
                'vehicle_name' => $vehicle->brand . ' ' . $vehicle->model,
                'vehicle_plate' => $vehicle->plate_no,
                'vehicle_image' => $imagePath,
                'daily_rate' => $vehicle->daily_rate,
                'rent_start' => $booking->rent_start->format('m/d/Y'),
                'rent_end' => $booking->rent_end->format('m/d/Y'),
                'days_rented' => $daysRented,
                'subtotal' => $subtotal,
                'vat' => $vat,
                'total' => $total,
                'vehicle_color' => $vehicle->color ?? 'N/A',
                'vehicle_category' => $vehicle->category ?? 'N/A',
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'note' => $booking->note,
                'returned_at' => $booking->returned_at 
                ? $booking->returned_at->format('m/d/Y') 
                : null,
            ];
        })->toArray();

        // Stats
        $stats = [
            'ongoing' => Booking::where('status', 'ongoing')->count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'approved' => Booking::where('status', 'approved')->count(),
            'finished' => Booking::where('status', 'finished')->count(),
        ];

        return view('staff.staff_bookings', compact('bookingsData', 'stats'));
    }

    public function show($id)
    {
        $booking = Booking::with([
            'customer.user',
            'vehicle.images',
            'approvedBy'
        ])->findOrFail($id);

        $vehicle = $booking->vehicle;
        $customer = $booking->customer;
        
        // Calculate days rented
        $daysRented = $booking->rent_start->diffInDays($booking->rent_end);
        if ($daysRented == 0) $daysRented = 1;
        
        // Calculate total
        $subtotal = $vehicle->daily_rate * $daysRented;
        $vat = $subtotal * 0.12;
        $total = $subtotal + $vat;
        
        // Get vehicle image
        $vehicleImage = $vehicle->images->where('is_primary', true)->first() ?? $vehicle->images->first();
        $imagePath = null;
        if ($vehicleImage && $vehicleImage->img_path) {
            $imagePath = asset('assets/images/images-vehicles/' . $vehicleImage->img_path);
        }
        
        $bookingData = [
            'booking_ID' => $booking->booking_ID,
            'customer_name' => $customer->user->first_name . ' ' . $customer->user->middle_name . ' ' . $customer->user->last_name,
            'customer_phone' => $customer->user->phone_number,
            'customer_email' => $customer->user->email,
            'vehicle_name' => $vehicle->brand . ' ' . $vehicle->model,
            'vehicle_plate' => $vehicle->plate_no,
            'vehicle_color' => $vehicle->color,
            'vehicle_category' => $vehicle->category,
            'vehicle_image' => $imagePath,
            'daily_rate' => $vehicle->daily_rate,
            'rent_start' => $booking->rent_start->format('m/d/Y'),
            'rent_end' => $booking->rent_end->format('m/d/Y'),
            'days_rented' => $daysRented,
            'subtotal' => $subtotal,
            'vat' => $vat,
            'total' => $total,
            'status' => $booking->status,
            'payment_status' => $booking->payment_status,
        ];

        return response()->json($bookingData);
    }

    public function approve(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            // Validate the request
            $request->validate([
                'note' => 'nullable|string',
            ]);

            $updateData = [
                'status' => 'approved',
                'note' => $request->input('note'),
            ];

            // Only set approved_by_user_id if the authenticated user is a staff member
            $user = Auth::user();
            if ($user && Staff::where('user_ID', $user->user_ID)->exists()) {
                $updateData['approved_by_user_id'] = $user->user_ID;
            }

            $booking->update($updateData);

            // Update vehicle status to booked
            $booking->vehicle->update([
                'status' => 'booked',
            ]);

            // Create notification for customer
            try {
                BookingNotification::create([
                    'booking_ID' => $booking->booking_ID,
                    'customer_user_id' => $booking->customer_user_id,
                    'type' => 'approved',
                    'message' => 'Your booking has been approved! Booking #' . $booking->booking_ID,
                    'staff_note' => $request->input('note'),
                ]);
            } catch (\Exception $notificationError) {
                // Log the notification error but don't fail the booking approval
                Log::error('Failed to create booking notification: ' . $notificationError->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Booking approved successfully',
                'booking' => $booking,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving booking: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            // Validate the request
            $request->validate([
                'note' => 'nullable|string',
            ]);

            $updateData = [
                'status' => 'rejected',
                'note' => $request->input('note'),
            ];

            // Only set approved_by_user_id if the authenticated user is a staff member
            $user = Auth::user();
            if ($user && Staff::where('user_ID', $user->user_ID)->exists()) {
                $updateData['approved_by_user_id'] = $user->user_ID;
            }

            $booking->update($updateData);

            // Create notification for customer
            try {
                BookingNotification::create([
                    'booking_ID' => $booking->booking_ID,
                    'customer_user_id' => $booking->customer_user_id,
                    'type' => 'rejected',
                    'message' => 'Your booking has been rejected. Booking #' . $booking->booking_ID,
                    'staff_note' => $request->input('note'),
                ]);
            } catch (\Exception $notificationError) {
                // Log the notification error but don't fail the booking rejection
                Log::error('Failed to create booking notification: ' . $notificationError->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Booking rejected successfully',
                'booking' => $booking,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting booking: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function startRental(Request $request, $id)
    {
        try {
            $booking = Booking::with('vehicle')->findOrFail($id);
            
            // Check if booking is approved and payment is downpaid
            if ($booking->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking must be approved to start rental',
                ], 400);
            }

            if ($booking->payment_status !== 'downpaid' && $booking->payment_status !== 'fullpaid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Downpayment must be verified before starting rental',
                ], 400);
            }

            // Check if rental can be started (1 day before or on the day of rent start)
            $today = now()->startOfDay();
            $rentStartDate = $booking->rent_start->startOfDay();
            $oneDayBefore = $rentStartDate->copy()->subDay();

            if ($today->isBefore($oneDayBefore) || $today->isAfter($rentStartDate)) {
                $formattedStartDate = $booking->rent_start->format('M d, Y');
                return response()->json([
                    'success' => false,
                    'message' => "Rental can only be started 1 day before ($oneDayBefore->format('M d, Y')) or on the day of rent start ($formattedStartDate)",
                ], 400);
            }

            // Update booking status to ongoing
            $booking->update([
                'status' => 'ongoing',
            ]);

            // Update vehicle status to rented
            $booking->vehicle->update([
                'status' => 'rented',
            ]);

            // Create notification for customer
            try {
                BookingNotification::create([
                    'booking_ID' => $booking->booking_ID,
                    'customer_user_id' => $booking->customer_user_id,
                    'type' => 'rental_started',
                    'message' => 'Your rental has started! Booking #' . $booking->booking_ID . '. Enjoy your ride!',
                ]);
            } catch (\Exception $notificationError) {
                // Log the notification error but don't fail the rental start
                Log::error('Failed to create rental notification: ' . $notificationError->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Rental started successfully',
                'booking' => $booking,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error starting rental: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function returnVehicle(Request $request, $id)
    {
        try {
            $booking = Booking::with('vehicle')->findOrFail($id);
            
            // Check if booking is ongoing
            if ($booking->status !== 'ongoing') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only ongoing bookings can be returned',
                ], 400);
            }

            // Update vehicle status to maintenance
            $booking->vehicle->update([
                'status' => 'maintenance',
            ]);

            // Update booking returned_at timestamp
            $booking->update([
                'returned_at' => now(),
            ]);

            // Create notification for customer
            try {
                BookingNotification::create([
                    'booking_ID' => $booking->booking_ID,
                    'customer_user_id' => $booking->customer_user_id,
                    'type' => 'vehicle_returned',
                    'message' => 'Your vehicle for booking #' . $booking->booking_ID . ' has been returned. Please complete the remaining payment.',
                ]);
            } catch (\Exception $notificationError) {
                // Log the notification error but don't fail the vehicle return
                Log::error('Failed to create return notification: ' . $notificationError->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Vehicle returned successfully',
                'booking' => $booking,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error returning vehicle: ' . $e->getMessage(),
            ], 500);
        }
    }
}
