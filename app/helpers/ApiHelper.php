<?php
// app/helpers/ApiHelper.php

class ApiHelper {

    /**
     * Makes a request to the API.
     *
     * @param string $endpoint The API endpoint to call (e.g., '/login').
     * @param string $method The HTTP method (GET, POST, etc.).
     * @param array|null $data The data to send with the request.
     * @param bool $requiresAuth Whether the request requires an authentication token.
     * @return array The response from the API, including 'http_code' and 'body'.
     */
    public static function request($endpoint, $method = 'GET', $data = null, $requiresAuth = false) {
        $url = API_URL . $endpoint;
        $headers = ['Content-Type: application/json'];

        if ($requiresAuth) {
            if (!isset($_SESSION['jwt_token'])) {
                return ['http_code' => 401, 'body' => json_encode(['status' => 'error', 'message' => 'Unauthorized: No token found in session.'])];
            }
            $headers[] = 'Authorization: Bearer ' . $_SESSION['jwt_token'];
        }
        
        if ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        if (in_array($method, ['POST', 'PUT', 'DELETE']) && $data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $body = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error_body = json_encode(['status' => 'error', 'message' => 'API Request Error: ' . curl_error($ch)]);
            curl_close($ch);
            return ['http_code' => 500, 'body' => $error_body];
        }

        curl_close($ch);

        // MODIFICATION: We no longer automatically destroy the session here.
        // The frontend JavaScript will handle the 401 redirect.
        // This helps prevent unexpected logouts during background requests.
        
        return ['http_code' => $http_code, 'body' => $body];
    }
}
?>
