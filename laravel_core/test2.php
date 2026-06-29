<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/hotels/272429?check_in=2026-06-18&check_out=2026-06-20', 'GET');
$app->instance('request', $request);

echo "Routing request...\n";
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Length: " . strlen($response->getContent()) . "\n";
