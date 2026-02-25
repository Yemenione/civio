<?php
/**
 * Laravel Deployment Setup Script for Hostinger
 * Upload this file to your root directory (public_html) and visit it in your browser.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Laravel Server Setup Tool</h1>";
echo "Current Redirect URI: <b style='color:blue;'>" . (file_exists('.env') ? preg_grep('/GOOGLE_REDIRECT_URI/', file('.env'))[0] ?? 'Not found' : 'No .env') . "</b><br>";

// 1. Create missing directories
$dirs = [
    'storage/app/public',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache'
];

echo "<h2>1. Creating Directories</h2><ul>";
foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0775, true)) {
            echo "<li style='color:green;'>[CREATED] $dir</li>";
        } else {
            echo "<li style='color:red;'>[FAILED] $dir (Check permissions)</li>";
        }
    } else {
        echo "<li>[EXISTS] $dir</li>";
    }
}
echo "</ul>";

// 2. Set Permissions
echo "<h2>2. Setting Permissions</h2><ul>";
foreach ($dirs as $dir) {
    if (chmod($dir, 0775)) {
        echo "<li style='color:green;'>[775 SET] $dir</li>";
    } else {
        echo "<li style='color:orange;'>[WARNING] Could not set 775 on $dir</li>";
    }
}
echo "</ul>";

// 3. Environment Check
echo "<h2>3. Environment Check</h2><ul>";
echo "<li>PHP Version: " . PHP_VERSION . " (Required: ^8.2)</li>";
if (version_compare(PHP_VERSION, '8.2', '<')) {
    echo "<li style='color:red;'>[ERROR] PHP Version is too low! Please change it in Hostinger panel.</li>";
} else {
    echo "<li style='color:green;'>[OK] PHP Version is compatible.</li>";
}
echo "</ul>";

// 4. Run Artisan (Optional)
echo "<h2>4. Run Commands</h2>";
if (isset($_GET['run'])) {
    $command = $_GET['run'];
    echo "Running: php artisan $command<br>";
    try {
        // We attempt to run via shell if allowed, or include artisan
        if (file_exists('artisan')) {
            $output = shell_exec("php artisan $command 2>&1");
            echo "<pre>$output</pre>";
        } else {
            echo "<b style='color:red;'>artisan file not found!</b>";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "<p>Quick Commands:</p>";
    echo "<ul>";
    echo "<li><a href='?run=migrate --force'>Run Migrations</a></li>";
    echo "<li><a href='?run=config:cache'>Cache Configuration (Clear & Rebuild)</a></li>";
    echo "<li><a href='?run=config:clear'>Clear ONLY Config Cache</a></li>";
    echo "<li><a href='?run=view:clear'>Clear Views Cache</a></li>";
    echo "<li><a href='?run=route:clear'>Clear Route Cache</a></li>";
    echo "<li><a href='?run=storage:link'>Link Storage</a></li>";
    echo "</ul>";
}

echo "<hr><p>Delete this file (setup.php) after you are done for security!</p>";
