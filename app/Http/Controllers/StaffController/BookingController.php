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
        // Fetch paginated bookings with relationships (10 per page)
        $bookingsPaginated = Booking::with([
            'customer.user',
            'vehicle.images',
            'approvedBy'
        ])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        // Format bookings data with calculated totals (only for current page)
        $bookingsData = $bookingsPaginated->getCollection()->map(function ($booking) {
            $vehicle = $booking->vehicle;
            $customer = $booking->customer;
            
            // Calculate days rented
            $daysRented = $booking->rent_start->diffInDays($booking->rent_end);
            if ($daysRented == 0) $daysRented = 1; // Minimum 1 day
            
            // Use values from database
            $subtotal = $booking->subtotal;
            $extraCharge = $booking->extra_charge ?? 0;
            $total = $booking->total;
            $downpayment = $booking->downpayment ?? 0;
            $remainingBalance = $booking->payment_status === 'fullpaid'
                ? 0
                : max(0, $total - $downpayment);
            
            // Calculate VAT for display (12% of subtotal) but don't recalculate total
            $vatDisplay = $subtotal * 0.12;
            
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
                'vat' => $vatDisplay,
                'extra_charge' => $extraCharge,
                'total' => $total,
                'downpayment' => $downpayment,
                'remaining_balance' => $remainingBalance,
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

        // Stats (count totals, not just current page)
        $allBookingsCount = Booking::count();
        $stats = [
            'ongoing' => Booking::where('status', 'ongoing')->count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'approved' => Booking::where('status', 'approved')->count(),
            'finished' => Booking::where('status', 'finished')->count(),
        ];

        return view('staff.staff_bookings', [
            'bookingsData' => $bookingsData, 
            'stats' => $stats,
            'bookings' => $bookingsPaginated
        ]);
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
        
        // Use saved booking values for consistency with the staff list/modal
        $subtotal = $booking->subtotal;
        $vat = $subtotal * 0.12;
        $extraCharge = $booking->extra_charge ?? 0;
        $total = $booking->total;
        $downpayment = $booking->downpayment ?? 0;
        $remainingBalance = $booking->payment_status === 'fullpaid'
            ? 0
            : max(0, $total - $downpayment);
        
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
            'extra_charge' => $extraCharge,
            'total' => $total,
            'downpayment' => $downpayment,
            'remaining_balance' => $remainingBalance,
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

            // Check if rental can be started (1 day before rent_start or during rental period through rent_end)
            $today = now()->startOfDay();
            $rentStartDate = $booking->rent_start->startOfDay();
            $rentEndDate = $booking->rent_end->startOfDay();
            $oneDayBefore = $rentStartDate->copy()->subDay();

            if ($today->isBefore($oneDayBefore) || $today->isAfter($rentEndDate)) {
                $formattedStartDate = $booking->rent_start->format('M d, Y');
                $formattedEndDate = $booking->rent_end->format('M d, Y');
                $formattedOneDayBefore = $oneDayBefore->format('M d, Y');
                return response()->json([
                    'success' => false,
                    'message' => "Rental can only be started from $formattedOneDayBefore (1 day before) through $formattedEndDate (rental end date)",
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

    public function sendMessage(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        if (!in_array($booking->status, ['approved', 'ongoing', 'finished'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Messages can only be sent for approved, ongoing, or finished bookings.',
            ], 422);
        }

        try {
            BookingNotification::create([
                'booking_ID' => $booking->booking_ID,
                'customer_user_id' => $booking->customer_user_id,
                'type' => 'message',
                'message' => 'Message from staff for booking #' . $booking->booking_ID,
                'staff_note' => $validated['message'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message sent to customer.',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send customer message: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error sending message: ' . $e->getMessage(),
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

            // Mark as returned and calculate extra charges FIRST
            $booking->markAsReturned();
            $booking->save();

            // Only update vehicle status if calculation succeeded
            $booking->vehicle->update([
                'status' => 'maintenance',
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
