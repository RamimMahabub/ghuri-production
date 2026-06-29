<?php
/**
 * Zip Extractor for Shared Hosting
 * 
 * 1. Upload this file to public_html alongside travel_agent_optimized.zip
 * 2. Visit: http://yourdomain.com/extract.php
 */

ini_set('max_execution_time', 300); // Allow 5 minutes to extract
ini_set('memory_limit', '512M');

$zipFile = __DIR__ . '/travel_agent_optimized.zip';
$extractTo = __DIR__;

echo "<h1>Extracting ZIP...</h1>";

if (!file_exists($zipFile)) {
    die("❌ Could not find travel_agent_optimized.zip. Did you upload it?");
}

$zip = new ZipArchive;
if ($zip->open($zipFile) === TRUE) {
    $zip->extractTo($extractTo);
    $zip->close();
    echo "<h2 style='color:green;'>✅ Extraction 100% Complete!</h2>";
    echo "<p>Please delete this extract.php file.</p>";
} else {
    echo "<h2 style='color:red;'>❌ Failed to extract the zip file.</h2>";
}
?>
