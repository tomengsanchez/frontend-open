<?php
// index.php

// Include the configuration file
require_once __DIR__ . '/config.php';

session_start();

// Define the base path of the application
define('BASE_PATH', __DIR__);

// Use parse_url to safely get the path part of the URL
$request_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Get the directory where the script is running
$script_name = $_SERVER['SCRIPT_NAME'];
$base_path = dirname($script_name);

// Remove the base path from the request path to get the clean route
if ($base_path !== '/' && strpos($request_path, $base_path) === 0) {
    $route = substr($request_path, strlen($base_path));
} else {
    $route = $request_path;
}

$route = trim($route, '/');

// Default route
if (empty($route)) {
    $route = 'dashboard';
}

// Simple routing with parameters
$parts = explode('/', $route);
$controller_name = !empty($parts[0]) ? ucfirst($parts[0]) . 'Controller' : 'DashboardController';
$method_name = !empty($parts[1]) ? $parts[1] : 'index';
$params = array_slice($parts, 2); // The rest of the URL parts are parameters

$controller_file = BASE_PATH . '/app/controllers/' . $controller_name . '.php';

if (file_exists($controller_file)) {
    require_once $controller_file;
    if (class_exists($controller_name)) {
        $controller = new $controller_name();
        if (method_exists($controller, $method_name)) {
            // Call the controller method with parameters
            call_user_func_array([$controller, $method_name], $params);
        } else {
            http_response_code(404);
            error_log("Routing Error: Method '{$method_name}' Not Found in Controller '{$controller_name}'");
            echo "404 - Page Not Found";
        }
    } else {
        http_response_code(404);
        error_log("Routing Error: Controller Class '{$controller_name}' Not Found");
        echo "404 - Page Not Found";
    }
} else {
    http_response_code(404);
    error_log("Routing Error: Controller File '{$controller_file}' Not Found");
    echo "404 - Page Not Found";
}
