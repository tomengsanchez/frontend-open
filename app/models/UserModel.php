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

        if ($response['http_code'] === 200 && isset($data['status']) && $data['status'] === 'success') {
            $_SESSION['jwt_token'] = $data['data']['token'];
            $_SESSION['user_logged_in'] = true;
            
            $routesResponse = ApiHelper::request('/routes', 'GET', null, true);
            $routesData = json_decode($routesResponse['body'], true);

            if (isset($routesData['data'])) {
                $_SESSION['user_routes'] = $routesData['data'];
            } else {
                $_SESSION['user_routes'] = []; 
            }

            return ['status' => 'success'];
        }

        return $data;
    }
    
    public function createUser($userData) {
        $response = ApiHelper::request('/settings/users', 'POST', $userData, true);
        return json_decode($response['body'], true);
    }

    public function getUserById($id) {
        $response = ApiHelper::request("/settings/users/{$id}", 'GET', null, true);
        return json_decode($response['body'], true);
    }

    public function updateUser($id, $userData) {
        $response = ApiHelper::request("/settings/users/{$id}", 'PUT', $userData, true);
        return json_decode($response['body'], true);
    }

    /**
     * Deletes a user via the API.
     * @param int $id The ID of the user to delete.
     * @return array The decoded JSON response from the API.
     */
    public function deleteUser($id) {
        $response = ApiHelper::request("/settings/users/{$id}", 'DELETE', null, true);
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
}
