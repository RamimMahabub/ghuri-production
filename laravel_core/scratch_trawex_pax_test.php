<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(\App\Services\TrawexFlightService::class);
echo "Testing 1 Passenger:\n";
$f1 = $service->search('DAC', 'DXB', '2026-06-05', 1, 'OneWay');
echo "1 Pax Lowest Price: " . ($f1[0]['price'] ?? 'N/A') . "\n";

echo "Testing 2 Passengers:\n";
$f2 = $service->search('DAC', 'DXB', '2026-06-05', 2, 'OneWay');
echo "2 Pax Lowest Price: " . ($f2[0]['price'] ?? 'N/A') . "\n";
