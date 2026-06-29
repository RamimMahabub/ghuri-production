<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test hotel show page
$request = Illuminate\Http\Request::create('/hotels/272429?check_in=2026-06-20&check_out=2026-06-22&guests=2', 'GET');
$response = $kernel->handle($request);
echo "Show page status: " . $response->getStatusCode() . "\n";
if ($response->getStatusCode() !== 200) {
    echo substr($response->getContent(), 0, 2000) . "\n";
}

// Test booking step 1
$request2 = Illuminate\Http\Request::create('/hotels/272429/rooms/134260/book?check_in=2026-06-20&check_out=2026-06-22&adults=2', 'GET');
$response2 = $kernel->handle($request2);
echo "Booking step1 status: " . $response2->getStatusCode() . "\n";
if ($response2->getStatusCode() !== 200) {
    echo substr($response2->getContent(), 0, 2000) . "\n";
}
