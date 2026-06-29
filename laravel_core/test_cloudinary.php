<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $file = \Illuminate\Http\UploadedFile::fake()->create('test.txt', 10);
    echo "Uploading...\n";
    $path = $file->store('properties/1', 'cloudinary');
    echo "Success! Path: " . $path . "\n";
} catch (\Exception $e) {
    echo "Error: " . get_class($e) . " - " . $e->getMessage() . "\n";
}
