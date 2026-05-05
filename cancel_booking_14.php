<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$booking = \App\Models\Booking::find(14);

if (!$booking) {
    echo "Booking 14 not found.\n";
    exit(1);
}

echo "Before Update:\n";
echo "  Booking ID: {$booking->booking_ID}\n";
echo "  Status: {$booking->status}\n";
echo "  Rent Start: {$booking->rent_start}\n";
echo "\nUpdating to 'cancelled'...\n";

$booking->update(['status' => 'cancelled']);

echo "After Update:\n";
echo "  Status: {$booking->status}\n";
echo "\n✓ Booking 14 has been successfully cancelled!\n";
