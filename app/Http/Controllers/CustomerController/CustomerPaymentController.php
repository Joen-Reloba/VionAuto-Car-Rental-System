<?php

namespace App\Http\Controllers\CustomerController;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CustomerPaymentController extends Controller
{
    public function index()
    {
        // Get all payments for the logged-in customer through their bookings
        $payments = Payment::whereIn('booking_ID', function ($query) {
            $query->select('booking_ID')
                ->from('bookings')
                ->where('customer_user_id', Auth::id());
        })
        ->with(['booking', 'booking.vehicle', 'verifiedBy'])
        ->orderBy('payment_date', 'desc')
        ->get();

        return view('customer.customer_payments', compact('payments'));
    }

    public function show($paymentId)
    {
        $payment = Payment::with(['booking', 'booking.vehicle', 'verifiedBy'])
            ->findOrFail($paymentId);

        // Verify payment belongs to logged-in customer
        if ($payment->booking->customer_user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('customer.customer_payment_detail', compact('payment'));
    }

    public function checkPendingPayment($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        // Verify booking belongs to logged-in customer
        if ($booking->customer_user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check for pending payments
        $pendingPayment = Payment::where('booking_ID', $bookingId)
            ->where('status', 'pending')
            ->first();

        if ($pendingPayment) {
            return response()->json([
                'success' => true,
                'hasPendingPayment' => true,
                'message' => 'Payment is pending verification. Please wait for staff approval or contact support if rejected.',
                'paymentType' => $pendingPayment->payment_type
            ]);
        }

        return response()->json([
            'success' => true,
            'hasPendingPayment' => false,
            'message' => 'No pending payments'
        ]);
    }

    public function submitPayment(Request $request)
{
    $request->validate([
        'booking_id'   => 'required|exists:bookings,booking_ID',
        'receipt'      => 'required|image|max:5120',
        'payment_type' => 'nullable|in:downpayment,final',
    ]);

    try {
        $booking = Booking::findOrFail($request->booking_id);

        if ($booking->customer_user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $paymentType = $request->input('payment_type', 'downpayment');

        // For downpayment: block if a non-rejected downpayment already exists
        // For fullpayment: block if a non-rejected fullpayment already exists
        $existingPayment = Payment::where('booking_ID', $booking->booking_ID)
            ->where('payment_type', $paymentType)
            ->where('status', '!=', 'rejected')
            ->first();

        if ($existingPayment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment already submitted for this booking'
            ], 400);
        }

        // For fullpayment, also verify the vehicle has been returned
        if ($paymentType === 'fullpayment') {
            if (!$booking->returned_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle must be returned before submitting full payment'
                ], 400);
            }
            $amountDue = $booking->total - $booking->downpayment;
        } else {
            $amountDue = $booking->downpayment;
        }

        // Save receipt
        $receiptFile = $request->file('receipt');
        $fileName = time() . '_' . $receiptFile->hashName();
        $receiptFile->move(public_path('assets/images/images-receipts'), $fileName);

        // Create new payment record (don't updateOrCreate — we want separate records)
        $payment = Payment::create([
            'booking_ID'    => $booking->booking_ID,
            'payment_type'  => $paymentType,
            'receipt_image' => $fileName,
            'amount_paid'   => $amountDue,
            'status'        => 'pending',
            'payment_date'  => now(),
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'Payment submitted successfully! Waiting for staff approval.',
            'payment_id' => $payment->payment_ID
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error submitting payment: ' . $e->getMessage()
        ], 500);
    }
}
}
