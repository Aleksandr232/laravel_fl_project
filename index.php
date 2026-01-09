<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * This file is used as a fallback when DocumentRoot points to the project root
 * instead of the public directory. It bootstraps the Laravel application.
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
*/

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
*/

if (!file_exists($autoload = __DIR__.'/vendor/autoload.php')) {
    http_response_code(500);
    die('Composer autoload file not found. Please run "composer install" on the server.');
}

require $autoload;

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
*/

$appPath = __DIR__.'/bootstrap/app.php';

if (!file_exists($appPath)) {
    http_response_code(500);
    die('Laravel bootstrap file not found.');
}

/** @var Application $app */
$app = require_once $appPath;

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
*/

$app->handleRequest(Request::capture());