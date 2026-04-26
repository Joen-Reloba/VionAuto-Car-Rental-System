<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

$models = [
    'App\Models\Staff',
    'App\Models\User',
    'App\Models\Booking',
    'App\Models\Payment'
];

echo "Testing model loading:\n";
foreach ($models as $modelClass) {
    try {
        $reflection = new ReflectionClass($modelClass);
        echo "$modelClass: OK\n";
    } catch (Throwable $e) {
        echo "$modelClass: ERROR - " . $e->getMessage() . "\n";
    }
}
