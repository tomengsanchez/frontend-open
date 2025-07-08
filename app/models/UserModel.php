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

        return $data; // Return the original error response from API
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

    /**
     * Fetches users from the API.
     * @param array $params Query parameters for pagination, sorting, etc.
     * @return string JSON response from the API.
     */
    public function getUsers($params) {
        // The third parameter tells the helper that this request requires authentication
        $response = ApiHelper::request('/settings/users', 'GET', $params, true);
        return $response['body'];
    }
}
?>
