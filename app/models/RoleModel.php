<?php
// app/models/RoleModel.php

class RoleModel {

    /**
     * Fetches all roles from the API.
     * @return string JSON response from the API.
     */
    public function getRoles() {
        // This request requires authentication
        $response = ApiHelper::request('/settings/roles', 'GET', null, true);
        return $response['body'];
    }

    /**
     * Creates a new role.
     * @param string $roleName The name of the new role.
     * @return array The decoded JSON response from the API.
     */
    public function createRole($roleName) {
        $payload = ['role_name' => $roleName];
        // This request requires authentication
        $response = ApiHelper::request('/settings/roles', 'POST', $payload, true);
        return json_decode($response['body'], true);
    }
}
?>
