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

echo "Testing services:\n";
foreach ($property->activeRoomTypes as $rt) {
    echo "Room: {$rt->id}\n";

    $t1 = microtime(true);
    $av = app(App\Services\AvailabilityService::class)->checkAvailability($rt->id, Carbon\Carbon::parse('2026-06-18'), Carbon\Carbon::parse('2026-06-20'));
    $t2 = microtime(true);
    echo "Availability time: " . ($t2 - $t1) . "s\n";

    $t3 = microtime(true);
    $pr = app(App\Services\PricingService::class)->calculateStayPrice($rt->id, Carbon\Carbon::parse('2026-06-18'), Carbon\Carbon::parse('2026-06-20'));
    $t4 = microtime(true);
    echo "Pricing time: " . ($t4 - $t3) . "s\n";
}

$t5 = microtime(true);
$reviewStats = [
    'average' => $property->average_rating,
    'count' => $property->review_count,
    'cleanliness' => App\Models\Review::where('property_id', $property->id)->published()->avg('cleanliness_score'),
    'location' => App\Models\Review::where('property_id', $property->id)->published()->avg('location_score'),
    'service' => App\Models\Review::where('property_id', $property->id)->published()->avg('service_score'),
    'value' => App\Models\Review::where('property_id', $property->id)->published()->avg('value_score'),
    'facilities' => App\Models\Review::where('property_id', $property->id)->published()->avg('facilities_score'),
];
$t6 = microtime(true);
echo "Review Stats time: " . ($t6 - $t5) . "s\n";

echo "Done.\n";
