<?php

/**
 * Vercel PHP Serverless Entry Point
 * 
 * This file forwards requests from Vercel Serverless Functions to Laravel's
 * public/index.php entry point.
 */

// Define a constant so bootstrap/app.php knows we are running on Vercel.
// Using a constant (not getenv) is 100% reliable — no dependency on env injection.
define('VERCEL_SERVERLESS', true);

// Ensure all required writable directories exist in /tmp
$storagePath = '/tmp/storage';
$dirs = [
    $storagePath,
    $storagePath . '/framework',
    $storagePath . '/framework/cache',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/testing',
    $storagePath . '/framework/views',
    $storagePath . '/logs',
    $storagePath . '/app',
    $storagePath . '/app/public',
    $storagePath . '/bootstrap',
    $storagePath . '/bootstrap/cache',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Map Laravel's cache files to the readable/writable /tmp directory
$bootstrapCachePath = $storagePath . '/bootstrap/cache';
$_ENV['APP_SERVICES_CACHE'] = $_SERVER['APP_SERVICES_CACHE'] = $bootstrapCachePath . '/services.php';
$_ENV['APP_PACKAGES_CACHE'] = $_SERVER['APP_PACKAGES_CACHE'] = $bootstrapCachePath . '/packages.php';
$_ENV['APP_CONFIG_CACHE'] = $_SERVER['APP_CONFIG_CACHE'] = $bootstrapCachePath . '/config.php';
$_ENV['APP_ROUTES_CACHE'] = $_SERVER['APP_ROUTES_CACHE'] = $bootstrapCachePath . '/routes.php';
$_ENV['APP_EVENTS_CACHE'] = $_SERVER['APP_EVENTS_CACHE'] = $bootstrapCachePath . '/events.php';
putenv('APP_SERVICES_CACHE=' . $_ENV['APP_SERVICES_CACHE']);
putenv('APP_PACKAGES_CACHE=' . $_ENV['APP_PACKAGES_CACHE']);
putenv('APP_CONFIG_CACHE=' . $_ENV['APP_CONFIG_CACHE']);
putenv('APP_ROUTES_CACHE=' . $_ENV['APP_ROUTES_CACHE']);
putenv('APP_EVENTS_CACHE=' . $_ENV['APP_EVENTS_CACHE']);

// Forward to typical Laravel index.php
require __DIR__ . '/../public/index.php';
