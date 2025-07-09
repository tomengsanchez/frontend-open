<?php
// config.php
date_default_timezone_set('Asia/Manila');
// --- URL Configuration ---
define('BASE_URL', 'http://fe.openoffice.local');
define('API_URL', 'http://api.openoffice.local');

// --- Database Configuration (Example) ---
// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'openoffice');

// --- Error Logging Configuration ---
// Disable displaying errors to the user
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
// Enable logging errors to a file
ini_set('log_errors', 1);
// Set the path for the error log file
ini_set('error_log', __DIR__ . '/error.log');
// Log all types of errors
error_reporting(E_ALL);
