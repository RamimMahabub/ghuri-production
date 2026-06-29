<?php
/**
 * Run Laravel Seeders on Shared Hosting
 * DELETE THIS FILE IMMEDIATELY AFTER USE!
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('max_execution_time', 300);

$laravelCoreDir = __DIR__ . '/../laravel_core';

require $laravelCoreDir . '/vendor/autoload.php';

$app = require_once $laravelCoreDir . '/bootstrap/app.php';

try {
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    // Run the db:seed command forcefully
    $status = $kernel->call('db:seed', [
        '--force' => true,
    ]);

    $output = $kernel->output();

    echo "<pre style='background:#1a1a1a;color:#00ff00;padding:20px;font-size:14px;'>";
    echo htmlspecialchars($output);
    echo "</pre>";

    if ($status === 0) {
        echo "<h2 style='color:green;'>✅ Database Seeding completed! Your data has been inserted.</h2>";
    } else {
        echo "<h2 style='color:orange;'>⚠️ Seeding finished with warnings. See output above.</h2>";
    }

} catch (\Exception $e) {
    echo "<h2 style='color:red;'>❌ Error:</h2>";
    echo "<pre style='background:#1a1a1a;color:#ff4444;padding:20px;'>" . $e->getMessage() . "</pre>";
}

echo "<hr><p><strong style='color:red;'>⚠️ CRITICAL: DELETE this seed.php file from your File Manager NOW!</strong></p>";
?>
