<?php
// index.php

// Include the configuration file
require_once __DIR__ . '/config.php';

// --- Enhanced Debug Logging ---
error_log("--- New Request: " . $_SERVER['REQUEST_URI'] . " ---");

session_start();

// Define the base path of the application
define('BASE_PATH', __DIR__);

// Get the requested URI
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];
$base_path = dirname($script_name);

// Remove the base path from the request URI to get the clean route
if (strpos($request_uri, $base_path) === 0) {
    $route = substr($request_uri, strlen($base_path));
} else {
    $route = $request_uri;
}

// Remove query string from the route
$route = strtok($route, '?');
$route = trim($route, '/');
error_log("[ROUTING] Clean route: '{$route}'");

// Default route if the URL is empty (e.g., http://fe.openoffice.local/)
if (empty($route)) {
    $route = 'dashboard';
    error_log("[ROUTING] Route empty, defaulting to 'dashboard'.");
}

// Simple routing
$parts = explode('/', $route);
// Default to DashboardController if none is provided
$controller_name = !empty($parts[0]) ? ucfirst($parts[0]) . 'Controller' : 'DashboardController';
// Default to the 'index' method if none is provided
$method_name = !empty($parts[1]) ? $parts[1] : 'index';
error_log("[ROUTING] Controller: '{$controller_name}', Method: '{$method_name}'");

$controller_file = BASE_PATH . '/app/controllers/' . $controller_name . '.php';

if (file_exists($controller_file)) {
    error_log("[ROUTING] Controller file found: {$controller_file}");
    require_once $controller_file;

    if (class_exists($controller_name)) {
        error_log("[ROUTING] Controller class exists: {$controller_name}");
        $controller = new $controller_name();

        if (method_exists($controller, $method_name)) {
            error_log("[ROUTING] Method exists. Calling {$controller_name}->{$method_name}()");
            // Call the controller method
            $controller->$method_name();
            error_log("[ROUTING] Finished executing method: {$controller_name}->{$method_name}()");
        } else {
            http_response_code(404);
            error_log("[ERROR] Routing Error: Method '{$method_name}' Not Found in Controller '{$controller_name}'");
            echo "404 - Page Not Found";
        }
    } else {
        http_response_code(404);
        error_log("[ERROR] Routing Error: Controller Class '{$controller_name}' Not Found in file '{$controller_file}'. Check for parse errors in the file.");
        echo "404 - Page Not Found";
    }
} else {
    http_response_code(404);
    error_log("[ERROR] Routing Error: Controller File '{$controller_file}' Not Found");
    echo "404 - Page Not Found";
}
