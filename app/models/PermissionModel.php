<?php
// app/models/PermissionModel.php

class PermissionModel {

    /**
     * Fetches a paginated list of permissions from the API.
     */
    public function getPermissions($params) {
        $response = ApiHelper::request('/settings/permissions', 'GET', $params, true);
        return $response['body'];
    }
    
    /**
     * Fetches the complete list of all permissions from the API.
     */
    public function getAllPermissions() {
        // We request a very large number per page to get all permissions.
        // A better backend might have a dedicated /all endpoint.
        $params = ['per_page' => 1000]; 
        $response = ApiHelper::request('/settings/permissions', 'GET', $params, true);
        return json_decode($response['body'], true);
    }

    /**
     * Fetches a single permission by its ID from the API.
     */
    public function getPermissionById($id) {
        $response = ApiHelper::request("/settings/permissions/{$id}", 'GET', null, true);
        return json_decode($response['body'], true);
    }

    /**
     * Creates a new permission via the API.
     */
    public function createPermission($name, $description) {
        $payload = [
            'permission_name' => $name,
            'description' => $description
        ];
        $response = ApiHelper::request('/settings/permissions', 'POST', $payload, true);
        return json_decode($response['body'], true);
    }

    /**
     * Updates an existing permission via the API.
     */
    public function updatePermission($id, $name, $description) {
        $payload = [
            'permission_name' => $name,
            'description' => $description
        ];
        $response = ApiHelper::request("/settings/permissions/{$id}", 'PUT', $payload, true);
        return json_decode($response['body'], true);
    }

    /**
     * Deletes a permission via the API.
     */
    public function deletePermission($id) {
        $response = ApiHelper::request("/settings/permissions/{$id}", 'DELETE', null, true);
        return json_decode($response['body'], true);
    }
}
