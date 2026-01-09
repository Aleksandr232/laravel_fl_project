<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any of our classes later on.
|
*/

// Get the base path - handle both direct access and symbolic links
// Always use realpath to resolve symbolic links to actual paths
$publicPath = realpath(__DIR__);
if ($publicPath === false) {
    $publicPath = __DIR__;
}

// Determine base path
// If public_html is a real directory (not a symlink), we need to find the project directory
$basePath = dirname($publicPath);

// Check if we're in public_html and need to go to project directory
// This handles the case when public_html is a real folder, not a symlink
if (basename($publicPath) === 'public_html' || basename(dirname($publicPath)) === 'public_html') {
    // Try to find project directory at the same level as public_html
    $parentDir = dirname($publicPath);
    $projectPath = $parentDir . DIRECTORY_SEPARATOR . 'project';
    
    if (file_exists($projectPath) && is_dir($projectPath)) {
        $basePath = $projectPath;
    }
} else {
    // Normal case: public is inside project directory
    $basePath = dirname($publicPath);
}

// Ensure we're working with absolute paths
if (!file_exists($basePath)) {
    die('Base path not found: ' . $basePath . '<br>Current directory: ' . __DIR__ . '<br>Public path: ' . $publicPath);
}

// Determine if the application is in maintenance mode...
$maintenance = $basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'maintenance.php';
if (file_exists($maintenance)) {
    require $maintenance;
}

// Register the Composer autoloader...
$autoload = $basePath . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (!file_exists($autoload)) {
    die('Composer autoload file not found. Please run "composer install".');
}
require $autoload;

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$bootstrap = $basePath . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'app.php';
if (!file_exists($bootstrap)) {
    die('Bootstrap file not found. Please check your Laravel installation.');
}
$app = require_once $bootstrap;

$app->handleRequest(Request::capture());
