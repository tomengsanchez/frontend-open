<?php
// app/models/RoleModel.php

class RoleModel {

    public function getRoles($params) {
        $response = ApiHelper::request('/settings/roles', 'GET', $params, true);
        return $response['body'];
    }
    
    /**
     * Fetches the complete list of all roles for dropdowns.
     */
    public function getAllRoles() {
        $params = ['per_page' => 1000]; // Assume a large number to get all roles
        $response = ApiHelper::request('/settings/roles', 'GET', $params, true);
        return json_decode($response['body'], true);
    }

    public function getRoleById($id) {
        $response = ApiHelper::request("/settings/roles/{$id}", 'GET', null, true);
        return json_decode($response['body'], true);
    }

    public function createRole($roleName, $description, $permissionIds) {
        $payload = [
            'role_name' => $roleName,
            'role_description' => $description,
            'permissions' => array_map('intval', $permissionIds)
        ];
        $response = ApiHelper::request('/settings/roles', 'POST', $payload, true);
        return json_decode($response['body'], true);
    }

    public function updateRole($id, $roleName, $description, $permissionIds) {
        $payload = [
            'role_name' => $roleName,
            'role_description' => $description,
            'permissions' => array_map('intval', $permissionIds)
        ];
        $response = ApiHelper::request("/settings/roles/{$id}", 'PUT', $payload, true);
        return json_decode($response['body'], true);
    }

    public function deleteRole($id) {
        $response = ApiHelper::request("/settings/roles/{$id}", 'DELETE', null, true);
        return json_decode($response['body'], true);
    }
}
