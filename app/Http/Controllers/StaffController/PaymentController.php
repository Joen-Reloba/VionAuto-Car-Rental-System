<?php

namespace App\Http\Controllers\StaffController;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\BookingNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display all payments for verification
     */
    public function index()
    {
        // Fetch all payments with relationships
        $payments = Payment::with(['booking.customer.user', 'booking.vehicle'])
            ->orderBy('payment_date', 'desc')
            ->get();

        // Format payment data for view
        $paymentsData = $payments->map(function ($payment) {
            $booking = $payment->booking;
            $totalBookingAmount = $booking->total ?? 0;
            $downpayment = $booking->downpayment ?? 0;
            $amountDue = $payment->amount_paid;
            $remainingBalance = $booking->payment_status === 'fullpaid'
                ? 0
                : max(0, $totalBookingAmount - $downpayment);

            return [
                'payment_ID' => $payment->payment_ID,
                'booking_ID' => $payment->booking_ID,
                'customer_name' => $payment->booking->customer?->user?->name ?? 'N/A',
                'vehicle_name' => $payment->booking->vehicle?->brand . ' ' . $payment->booking->vehicle?->model ?? 'N/A',
                'total_booking_amount' => $totalBookingAmount,
                'amount_due' => $amountDue,
                'amount_paid' => $payment->amount_paid,
                'remaining_balance' => $remainingBalance,
                'payment_type' => $payment->payment_type,
                'status' => $payment->status,
                'receipt_image' => $payment->receipt_image ? asset('assets/images/images-receipts/' . $payment->receipt_image) : null,
                'payment_date' => $payment->payment_date?->format('M d, Y'),
                'rent_start' => $payment->booking->rent_start?->format('M d'),
                'rent_end' => $payment->booking->rent_end?->format('M d'),
                'reference_number' => $payment->reference_number,
            ];
        })->toArray();

        // Calculate stats
        $stats = [
            'pending' => $payments->where('status', 'pending')->count(),
            'verified' => $payments->where('status', 'verified')->count(),
            'rejected' => $payments->where('status', 'rejected')->count(),
        ];

        return view('staff.staff_payments', [
            'paymentsData' => $paymentsData,
            'stats' => $stats,
        ]);
    }

    /**
     * Show single payment details
     */
    public function show(Payment $payment)
    {
        $booking = $payment->booking;
        $totalBookingAmount = $booking->total ?? 0;
        $downpayment = $booking->downpayment ?? 0;
        $amountDue = $payment->amount_paid;
        $remainingBalance = $booking->payment_status === 'fullpaid'
            ? 0
            : max(0, $totalBookingAmount - $downpayment);

        $paymentData = [
            'payment_ID' => $payment->payment_ID,
            'booking_ID' => $payment->booking_ID,
            'customer_name' => $payment->booking->customer?->user?->name ?? 'N/A',
            'vehicle_name' => $payment->booking->vehicle?->brand . ' ' . $payment->booking->vehicle?->model ?? 'N/A',
            'total_booking_amount' => $totalBookingAmount,
            'amount_due' => $amountDue,
            'amount_paid' => $payment->amount_paid,
            'remaining_balance' => $remainingBalance,
            'payment_type' => $payment->payment_type,
            'status' => $payment->status,
            'receipt_image' => $payment->receipt_image ? asset('assets/images/images-receipts/' . $payment->receipt_image) : null,
            'payment_date' => $payment->payment_date?->format('M d, Y'),
            'rent_start' => $payment->booking->rent_start?->format('M d'),
            'rent_end' => $payment->booking->rent_end?->format('M d'),
            'reference_number' => $payment->reference_number,
        ];

        return response()->json($paymentData);
    }

    /**
     * Approve a payment
     */
    public function approve(Request $request)
    {
        try {
            $payment = Payment::with(['booking', 'booking.vehicle'])->findOrFail($request->payment_id);
            
            // Update payment status
            $payment->update([
                'status' => 'verified',
                'verified_by_user_id' => Auth::id(),
                'verified_at' => now()
            ]);

            $payment = $payment->fresh(['booking', 'booking.vehicle']);



            Log::info('About to update booking', [
            'payment_type' => $payment->payment_type,
                'is_final' => $payment->payment_type === 'final',
            ]);
            // Update booking payment status and vehicle status
            if ($payment->booking) {
                $booking = $payment->booking;

                Log::info('Payment approve debug', [
                'payment_type' => $payment->payment_type,
                'booking_id' => $booking->booking_ID,
                'booking_status' => $booking->status,
                'vehicle_loaded' => $booking->relationLoaded('vehicle'),
            ]);

                $amountPaid = $payment->amount_paid;
                $total = $booking->total;
                $downpayment = $booking->downpayment;

            if ($payment->payment_type === 'final') {
                $booking->update(['payment_status' => 'fullpaid', 'status' => 'finished']);
                // Keep vehicle in maintenance - staff will manually set to available after inspection
            } elseif ($amountPaid >= $downpayment) {
                $booking->update(['payment_status' => 'downpaid']);
                $booking->vehicle->update(['status' => 'booked']);
            }
            }

            // Create notification for customer
            try {
                BookingNotification::create([
                    'booking_ID' => $payment->booking_ID,
                    'customer_user_id' => $payment->booking->customer_user_id,
                    'type' => 'payment_approved',
                    'message' => 'Your payment for booking #' . $payment->booking_ID . ' has been approved!',
                    'staff_note' => $request->input('note'),
                ]);
            } catch (\Exception $notificationError) {
                // Log the notification error but don't fail the payment approval
                Log::error('Failed to create payment notification: ' . $notificationError->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment approved successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a payment
     */
    public function reject(Request $request)
    {
        try {
            $payment = Payment::with('booking')->findOrFail($request->payment_id);
            
            // Update payment status
            $payment->update([
                'status' => 'rejected',
                'verified_by_user_id' => Auth::id(),
                'verified_at' => now()
            ]);

            // Create notification for customer
            try {
                BookingNotification::create([
                    'booking_ID' => $payment->booking_ID,
                    'customer_user_id' => $payment->booking->customer_user_id,
                    'type' => 'payment_rejected',
                    'message' => 'Your payment for booking #' . $payment->booking_ID . ' has been rejected. Please re-submit with correct payment proof.',
                    'staff_note' => $request->input('note'),
                ]);
            } catch (\Exception $notificationError) {
                // Log the notification error but don't fail the payment rejection
                Log::error('Failed to create payment notification: ' . $notificationError->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment rejected successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting payment: ' . $e->getMessage()
            ], 500);
        }
    }
}
