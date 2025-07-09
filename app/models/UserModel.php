<?php
// app/models/UserModel.php

class UserModel {

    public function isLoggedIn() {
        return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
    }

    public function login($username, $password) {
        $payload = json_encode(['username' => $username, 'password' => $password]);
        $response = $this->make_curl_request(API_URL . '/login', 'POST', $payload);

        $data = json_decode($response['body'], true);

        if (is_array($data) && $data['status'] === 'success' && isset($data['data']['token'])) {
            $_SESSION['jwt_token'] = $data['data']['token'];
            $_SESSION['user_logged_in'] = true;
            return ['status' => 'success'];
        }

        return $data; // Return the original error response from API
    }
    
    /**
     * Creates a new user via the API.
     * @param array $userData The user data from the form.
     * @return array The decoded JSON response from the API.
     */
    public function createUser($userData) {
        // Assuming the API endpoint is /settings/users
        $response = ApiHelper::request('/settings/users', 'POST', $userData, true);
        return json_decode($response['body'], true);
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

    public function getUsers($params) {
        if (!isset($_SESSION['jwt_token'])) {
            return json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $headers = [
            'Authorization: Bearer ' . $_SESSION['jwt_token'],
            'Content-Type: application/json'
        ];
        
        $query_string = http_build_query($params);
        $full_api_url = API_URL . '/settings/users?' . $query_string;

        $response = $this->make_curl_request($full_api_url, 'GET', null, $headers);
        
        if ($response['http_code'] == 401) {
            $this->logout();
        }

        return $response['body'];
    }


    private function make_curl_request($url, $method, $data = null, $headers = ['Content-Type: application/json']) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $body = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error_body = json_encode(['status' => 'error', 'message' => 'Proxy cURL Error: ' . curl_error($ch)]);
            curl_close($ch);
            return ['http_code' => 500, 'body' => $error_body];
        }

        curl_close($ch);
        return ['http_code' => $http_code, 'body' => $body];
    }
}
?>
