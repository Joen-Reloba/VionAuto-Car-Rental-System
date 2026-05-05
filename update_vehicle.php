<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Search for vehicle where brand + model contains the search term
$vehicle = \App\Models\Vehicle::where('brand', 'Ford')
    ->where('model', 'like', '%Raptor F 150%')
    ->first();

if (!$vehicle) {
    // Try alternative search
    $vehicle = \App\Models\Vehicle::where('brand', 'like', '%Ford%')
        ->where('model', 'like', '%Raptor%')
        ->first();
}

if (!$vehicle) {
    echo "Vehicle 'Ford Raptor F 150' not found.\n";
    echo "Let me list all available vehicles:\n";
    $allVehicles = \App\Models\Vehicle::all();
    foreach ($allVehicles as $v) {
        echo "  - {$v->brand} {$v->model} (Status: {$v->status})\n";
    }
    exit(1);
}

echo "Updating Vehicle:\n";
echo "  Vehicle ID: {$vehicle->vehicle_ID}\n";
echo "  Name: {$vehicle->name}\n";
echo "  Current Status: {$vehicle->status}\n";

$vehicle->update(['status' => 'available']);

echo "\nAfter Update:\n";
echo "  Status: {$vehicle->status}\n";
echo "\n✓ {$vehicle->name} has been updated to available!\n";
