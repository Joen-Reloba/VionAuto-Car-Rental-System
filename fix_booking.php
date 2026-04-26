<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;
use App\Models\Payment;

$booking = Booking::find(1);
$payment = Payment::where('booking_ID', 1)->first();

if ($booking && $payment && $payment->status == 'verified') {
    $amountPaid = $payment->amount_paid;
    $total = $booking->total;
    $downpayment = $booking->downpayment;

    if ($amountPaid >= $total) {
        $booking->update(['payment_status' => 'fullpaid']);
        $booking->vehicle->update(['status' => 'rented']);
        echo "Updated to fullpaid and rented\n";
    } elseif ($amountPaid >= $downpayment) {
        $booking->update(['payment_status' => 'downpaid']);
        $booking->vehicle->update(['status' => 'booked']);
        echo "Updated to downpaid and booked\n";
    }
} else {
    echo "Conditions not met\n";
}