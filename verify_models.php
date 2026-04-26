<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

echo "=== TINKER MODEL LOADING TEST ===\n";
echo "Testing models as they would be referenced in tinker:\n\n";

$tests = [
    'App\Models\Staff',
    'App\Models\User',
    'App\Models\Booking',
    'App\Models\Payment'
];

foreach ($tests as $model) {
    try {
        $classConstant = $model . '::class';
        eval('$result = ' . $classConstant . ';');
        echo ">> $classConstant\n";
        echo "=> \"$result\"\n\n";
    } catch (Throwable $e) {
        echo ">> $classConstant\n";
        echo "ERROR: " . $e->getMessage() . "\n\n";
    }
}
