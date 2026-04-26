<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;
use App\Models\Payment;

echo "Booking 1:\n";
$b = Booking::find(1);
if ($b) {
    echo "Status: " . $b->status . "\n";
    echo "Payment Status: " . $b->payment_status . "\n";
    echo "Total: " . $b->total . "\n";
    echo "Vehicle Status: " . $b->vehicle->status . "\n";
} else {
    echo "Not found\n";
}

echo "Payments for booking 1:\n";
$payments = Payment::where('booking_ID', 1)->get();
foreach ($payments as $p) {
    echo "Payment ID: " . $p->payment_ID . ", Status: " . $p->status . ", Amount: " . $p->amount_paid . "\n";
}