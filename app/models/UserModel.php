<?php
// app/models/UserModel.php

class UserModel {

    public function isLoggedIn() {
        return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
    }

    public function login($username, $password) {
        $payload = ['username' => $username, 'password' => $password];
        $response = ApiHelper::request('/login', 'POST', $payload);
        $data = json_decode($response['body'], true);

        if ($response['http_code'] === 200 && $data['status'] === 'success') {
            $_SESSION['jwt_token'] = $data['data']['token'];
            $_SESSION['user_logged_in'] = true;
            return ['status' => 'success'];
        }
        return $data;
    }
    
    public function createUser($userData) {
        $response = ApiHelper::request('/settings/users', 'POST', $userData, true);
        return json_decode($response['body'], true);
    }

    /**
     * Fetches a single user by their ID from the API.
     * @param int $id The ID of the user.
     * @return array The decoded JSON response from the API.
     */
    public function getUserById($id) {
        $response = ApiHelper::request("/settings/users/{$id}", 'GET', null, true);
        return json_decode($response['body'], true);
    }

    /**
     * Updates an existing user via the API.
     * @param int $id The ID of the user to update.
     * @param array $userData The user data from the form.
     * @return array The decoded JSON response from the API.
     */
    public function updateUser($id, $userData) {
        // Assuming the API uses a PUT request for updates
        $response = ApiHelper::request("/settings/users/{$id}", 'PUT', $userData, true);
        return json_decode($response['body'], true);
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

    public function getUsers($params) {
        $response = ApiHelper::request('/settings/users', 'GET', $params, true);
        return $response['body'];
    }

    // The make_curl_request method is no longer needed here as it's in ApiHelper
}
