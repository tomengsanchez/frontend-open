<?php
// Start a PHP session to store the JWT token securely.
session_start();

// The base URL of your backend API.
// Make sure this is correct for your environment.
$api_base_url = 'http://api.openoffice.local'; // IMPORTANT: Change this to your actual API URL

// Get the requested endpoint from the frontend.
$endpoint = $_GET['endpoint'] ?? '';
// Get the request method.
$method = $_SERVER['REQUEST_METHOD'];

// A simple router for our proxy.
switch ($endpoint) {
    case '/login':
        // Forward login requests to the API and store the token.
        handle_login($api_base_url, $method);
        break;
    case '/logout':
        // Destroy the session to log the user out.
        handle_logout();
        break;
    default:
        // Forward all other requests to the API, adding the auth token.
        forward_request($api_base_url, $endpoint, $method);
        break;
}


function list_users($api_base_url, $method) {
    // This function is not used in the current implementation.
    // It can be implemented if needed to list users from the API.
    if ($method !== 'GET') {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['status' => 'error', 'message' => 'Only GET method is allowed for listing users.']);
        exit;
    }
    $response = forward_request($api_base_url, '/settings/users', $method);
    // echo $response['body'];
}
/**
 * Handles the login request, stores the JWT in the session.
 */
function handle_login($api_base_url, $method) {
    if ($method !== 'POST') {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['status' => 'error', 'message' => 'Only POST method is allowed for login.']);
        exit;
    }

    $response = make_curl_request($api_base_url . '/login', 'POST', file_get_contents('php://input'));
    $data = json_decode($response['body'], true);

    // Add this check:
    if (is_array($data) && isset($data['status']) && $data['status'] === 'success' && isset($data['data']['token'])) {
        $_SESSION['jwt_token'] = $data['data']['token'];
        $_SESSION['user_logged_in'] = true;
    }

    // Return the API's response to the frontend.
    http_response_code($response['http_code']);
    header('Content-Type: application/json');
    echo $response['body'];
}

/**
 * Destroys the user session to handle logout.
 */
function handle_logout() {
    session_unset();
    session_destroy();
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Logged out successfully.']);
    exit;
}

/**
 * Forwards a request to the backend API, adding the JWT from the session.
 */
function forward_request($api_base_url, $endpoint, $method) {
    // Check if the user is logged in by looking for the token in the session.
    if (!isset($_SESSION['jwt_token'])) {
        http_response_code(401); // Unauthorized
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please log in.']);
        exit;
    }

    // Prepare the authorization header.
    $headers = [
        'Authorization: Bearer ' . $_SESSION['jwt_token'],
        'Content-Type: application/json'
    ];

    // Get data for POST/PUT/DELETE requests.
    $data = null;
    if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
        $data = file_get_contents('php://input');
    }

    // Build the full API URL, including any query parameters from the original request.
    $query_string = $_SERVER['QUERY_STRING'];
    // Remove the 'endpoint' param from the query string before forwarding.
    parse_str($query_string, $query_params);
    unset($query_params['endpoint']);
    $final_query_string = http_build_query($query_params);
    $full_api_url = $api_base_url . $endpoint . ($final_query_string ? '?' . $final_query_string : '');


    $response = make_curl_request($full_api_url, $method, $data, $headers);

    // Return the API's response to the frontend.
    http_response_code($response['http_code']);
    header('Content-Type: application/json');
    echo $response['body'];
}


/**
 * A generic cURL wrapper to make requests to the backend API.
 */
function make_curl_request($url, $method, $data = null, $headers = ['Content-Type: application/json']) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    // Set a timeout to prevent long-running requests
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);


    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    $body = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        // If cURL errors, return a 500.
        // We can't set http_response_code here as headers might already be sent.
        // Instead, we craft an error response body.
        $error_body = json_encode(['status' => 'error', 'message' => 'Proxy cURL Error: ' . curl_error($ch)]);
        curl_close($ch);
        return ['http_code' => 500, 'body' => $error_body];
    }

    curl_close($ch);

    return ['http_code' => $http_code, 'body' => $body];
}
?>
