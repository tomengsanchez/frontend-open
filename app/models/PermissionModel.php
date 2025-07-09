<?php
// app/models/PermissionModel.php

class PermissionModel {

    /**
     * Fetches the list of available routes and the user's permission for each.
     * @return array The decoded JSON response from the API.
     */
    public function getRoutes() {
        $response = ApiHelper::request('/routes', 'GET', null, true);
        return json_decode($response['body'], true);
    }

    // ... other methods remain the same ...

    public function getPermissions($params) {
        $response = ApiHelper::request('/settings/permissions', 'GET', $params, true);
        return $response['body'];
    }
    
    public function getAllPermissions() {
        $params = ['per_page' => 1000]; 
        $response = ApiHelper::request('/settings/permissions', 'GET', $params, true);
        return json_decode($response['body'], true);
    }

    public function getPermissionById($id) {
        $response = ApiHelper::request("/settings/permissions/{$id}", 'GET', null, true);
        return json_decode($response['body'], true);
    }

    public function createPermission($name, $description) {
        $payload = [
            'permission_name' => $name,
            'description' => $description
        ];
        $response = ApiHelper::request('/settings/permissions', 'POST', $payload, true);
        return json_decode($response['body'], true);
    }

    public function updatePermission($id, $name, $description) {
        $payload = [
            'permission_name' => $name,
            'description' => $description
        ];
        $response = ApiHelper::request("/settings/permissions/{$id}", 'PUT', $payload, true);
        return json_decode($response['body'], true);
    }

    public function deletePermission($id) {
        $response = ApiHelper::request("/settings/permissions/{$id}", 'DELETE', null, true);
        return json_decode($response['body'], true);
    }
}
