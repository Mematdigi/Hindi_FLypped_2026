<?php

/**
 * CRON RUNNER for PublishScheduledPosts
 * 
 * This file bootstraps CodeIgniter and calls the PublishScheduledPosts::index() function
 * 
 * USAGE:
 * /usr/bin/php -q /home/nzzds7ovcsb3/public_html/flypped.com/admin/cron_runner.php
 */



// Set the path to this file
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Load CodeIgniter paths
require FCPATH . 'app/Config/Paths.php';
$paths = new Config\Paths();

// Bootstrap CodeIgniter
require $paths->systemDirectory . '/Boot.php';

// Boot CLI environment
$app = CodeIgniter\Boot::bootCommand($paths);

// Manually instantiate and call the controller
$controller = new \App\Controllers\PublishScheduledPosts();
$controller->index();

// Exit successfully
exit(0);