<?php

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.1'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . 'app/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Config\Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

//For Logs

// for logs - traffic logging
register_shutdown_function(function() {
    $uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
    $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
    $controller = 'unknown';
    
    // Try to extract controller from URI
    $parts = explode('/', trim($uri, '/'));
    if (!empty($parts[0])) {
        $controller = $parts[0];
    }
    $istTime = new DateTime('now', new DateTimeZone('Asia/Kolkata'));

    
    $logData = [
    'time' => $istTime->format('Y-m-d H:i:s'),
        'method' => $method,
        'uri' => $uri,
        'controller' => $controller,
        'execution' => round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 4) . 's',
        'memory' => round(memory_get_peak_usage() / 1024 / 1024, 2) . 'MB'
    ];
    
    $logFile = FCPATH . 'writable/logs/traffic/' . date('Y-m-d') . '.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    
    @file_put_contents($logFile, json_encode($logData) . PHP_EOL, FILE_APPEND);
});


exit(CodeIgniter\Boot::bootWeb($paths));
