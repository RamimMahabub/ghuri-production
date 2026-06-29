<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Airport;

// Test DAC search
echo "=== Testing q=DAC ===\n";
$query = 'DAC';
$exactQuery = strtoupper($query);
$likeQuery = '%' . $query . '%';
$prefixQuery = $query . '%';

$airports = Airport::where('iata_code', $exactQuery)
    ->orWhere('iata_code', 'like', $exactQuery . '%')
    ->orWhere('city', 'like', $prefixQuery)
    ->orWhere('name', 'like', $likeQuery)
    ->orderByRaw("
        CASE 
            WHEN iata_code = ? THEN 1 
            WHEN city LIKE ? THEN 2
            WHEN iata_code LIKE ? THEN 3
            ELSE 4 
        END
    ", [$exactQuery, $prefixQuery, $exactQuery . '%'])
    ->limit(10)
    ->get(['iata_code', 'name', 'city', 'country']);

foreach ($airports as $a) {
    echo "  [{$a->iata_code}] {$a->city} - {$a->name} ({$a->country})\n";
}

// Test hungary search
echo "\n=== Testing q=hungary ===\n";
$query2 = 'hungary';
$exactQuery2 = strtoupper($query2);
$likeQuery2 = '%' . $query2 . '%';
$prefixQuery2 = $query2 . '%';

$airports2 = Airport::where('iata_code', $exactQuery2)
    ->orWhere('iata_code', 'like', $exactQuery2 . '%')
    ->orWhere('city', 'like', $prefixQuery2)
    ->orWhere('name', 'like', $likeQuery2)
    ->orderByRaw("
        CASE 
            WHEN iata_code = ? THEN 1 
            WHEN city LIKE ? THEN 2
            WHEN iata_code LIKE ? THEN 3
            ELSE 4 
        END
    ", [$exactQuery2, $prefixQuery2, $exactQuery2 . '%'])
    ->limit(10)
    ->get(['iata_code', 'name', 'city', 'country']);

if ($airports2->isEmpty()) {
    echo "  No results!\n";
} else {
    foreach ($airports2 as $a) {
        echo "  [{$a->iata_code}] {$a->city} - {$a->name} ({$a->country})\n";
    }
}

// Check what country code Hungary uses
echo "\n=== Hungarian airports in DB ===\n";
$hu = Airport::where('country', 'HU')->orWhere('country', 'Hungary')->limit(10)->get(['iata_code', 'name', 'city', 'country']);
foreach ($hu as $a) {
    echo "  [{$a->iata_code}] {$a->city} - {$a->name} ({$a->country})\n";
}
