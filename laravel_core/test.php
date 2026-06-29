<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$property = App\Models\Property::find(272429);
if (!$property) {
    echo "Property not found\n";
    exit;
}

echo "Testing AvailabilityService:\n";
foreach ($property->activeRoomTypes as $rt) {
    echo "Room: {$rt->id}\n";
    $av = app(App\Services\AvailabilityService::class)->checkAvailability($rt->id, Carbon\Carbon::parse('2026-06-18'), Carbon\Carbon::parse('2026-06-20'));
    echo "Av: {$av}\n";
    $pr = app(App\Services\PricingService::class)->calculateStayPrice($rt->id, Carbon\Carbon::parse('2026-06-18'), Carbon\Carbon::parse('2026-06-20'));
    echo "Pr: {$pr['total']}\n";
}
echo "Done.\n";
