<?php
// app/models/RoleModel.php

class RoleModel {

    /**
     * Fetches a paginated list of roles from the API.
     */
    public function getRoles($params) {
        $response = ApiHelper::request('/settings/roles', 'GET', $params, true);
        return $response['body'];
    }

    /**
     * Fetches a single role by its ID.
     * @param int $id The ID of the role.
     * @return array The decoded JSON response.
     */
    public function getRoleById($id) {
        $response = ApiHelper::request("/settings/roles/{$id}", 'GET', null, true);
        return json_decode($response['body'], true);
    }

    /**
     * Creates a new role with associated permissions.
     */
    public function createRole($roleName, $description, $permissionIds) {
        $payload = [
            'role_name' => $roleName,
            'role_description' => $description,
            'permissions' => array_map('intval', $permissionIds)
        ];
        $response = ApiHelper::request('/settings/roles', 'POST', $payload, true);
        return json_decode($response['body'], true);
    }

    /**
     * Updates an existing role.
     */
    public function updateRole($id, $roleName, $description, $permissionIds) {
        $payload = [
            'role_name' => $roleName,
            'role_description' => $description,
            'permissions' => array_map('intval', $permissionIds)
        ];
        $response = ApiHelper::request("/settings/roles/{$id}", 'PUT', $payload, true);
        return json_decode($response['body'], true);
    }

    /**
     * Deletes a role via the API.
     * @param int $id The ID of the role to delete.
     * @return array The decoded JSON response from the API.
     */
    public function deleteRole($id) {
        $response = ApiHelper::request("/settings/roles/{$id}", 'DELETE', null, true);
        return json_decode($response['body'], true);
    }
}
