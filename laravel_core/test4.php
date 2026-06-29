<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

Illuminate\Support\Facades\DB::enableQueryLog();
$start = microtime(true);

$request = Illuminate\Http\Request::create('/hotels/272429?check_in=2026-06-18&check_out=2026-06-20', 'GET');
$response = $kernel->handle($request);

$time = microtime(true) - $start;
echo "Time: {$time}s\n";
echo "Queries: " . count(Illuminate\Support\Facades\DB::getQueryLog()) . "\n";
