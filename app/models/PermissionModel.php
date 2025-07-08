<?php
// app/models/PermissionModel.php

class PermissionModel {

    /**
     * Fetches a paginated list of permissions from the API.
     * @param array $params Query parameters for pagination, sorting, etc.
     * @return string JSON response from the API.
     */
    public function getPermissions($params) {
        $response = ApiHelper::request('/settings/permissions', 'GET', $params, true);
        return $response['body'];
    }

    /**
     * Creates a new permission via the API.
     * @param string $name The name of the permission.
     * @param string $description The description of the permission.
     * @return array The decoded JSON response from the API.
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
     * @param int $id The ID of the permission to update.
     * @param string $name The new name of the permission.
     * @param string $description The new description of the permission.
     * @return array The decoded JSON response from the API.
     */
    public function updatePermission($id, $name, $description) {
        $payload = [
            'permission_name' => $name,
            'description' => $description
        ];
        // The ID is part of the URL for a PUT request
        $response = ApiHelper::request("/settings/permissions/{$id}", 'PUT', $payload, true);
        return json_decode($response['body'], true);
    }

    /**
     * Deletes a permission via the API.
     * @param int $id The ID of the permission to delete.
     * @return array The decoded JSON response from the API.
     */
    public function deletePermission($id) {
        // No payload is needed for a DELETE request
        $response = ApiHelper::request("/settings/permissions/{$id}", 'DELETE', null, true);
        return json_decode($response['body'], true);
    }
}
?>
