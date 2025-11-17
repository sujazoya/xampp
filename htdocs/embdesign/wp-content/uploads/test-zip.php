<?php
// Load WordPress environment
require_once(dirname(__DIR__, 2) . '/wp-load.php');

// Ensure uploads directory exists
$upload_dir = wp_upload_dir();
$uag_dir = $upload_dir['basedir'] . '/uag-apps/';

if (!file_exists($uag_dir)) {
    wp_mkdir_p($uag_dir);
}

// Test ZIP creation
$zip = new ZipArchive();
$filename = $uag_dir . 'test-' . time() . '.zip';

echo "<h1>ZIP Creation Test</h1>";
echo "<p>Attempting to create: $filename</p>";

if ($zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    $zip->addFromString('test.txt', 'This is a test file');
    $zip->addFromString('readme.txt', 'This ZIP was generated for testing purposes');
    
    if ($zip->close()) {
        echo "<div style='color:green;'><p>✓ Successfully created ZIP file</p>";
        echo "<p>File path: <code>$filename</code></p>";
        echo "<p>File size: " . filesize($filename) . " bytes</p>";
        echo "<p><a href='" . $upload_dir['baseurl'] . "/uag-apps/" . basename($filename) . "'>Download ZIP</a></p></div>";
        
        // List directory contents
        echo "<h3>Directory Contents:</h3>";
        echo "<pre>";
        print_r(scandir($uag_dir));
        echo "</pre>";
    } else {
        echo "<div style='color:red;'>✓ ZIP created but couldn't be closed properly</div>";
    }
} else {
    echo "<div style='color:red;'>✗ Failed to create ZIP file</div>";
    echo "<h3>Error Details:</h3>";
    echo "<pre>";
    print_r(error_get_last());
    echo "</pre>";
    
    // Additional diagnostics
    echo "<h3>System Checks:</h3>";
    echo "<ul>";
    echo "<li>ZIP extension: " . (extension_loaded('zip') ? 'Enabled' : 'Disabled') . "</li>";
    echo "<li>Directory writable: " . (is_writable($uag_dir) ? 'Yes' : 'No') . "</li>";
    echo "<li>Free disk space: " . round(disk_free_space('/') / (1024*1024)) . "MB</li>";
    echo "</ul>";
}