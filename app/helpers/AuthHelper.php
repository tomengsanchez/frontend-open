<?php
// app/helpers/AuthHelper.php

class AuthHelper {
    /**
     * Checks if the user has permission for a specific action/route.
     *
     * @param string $permission The permission string to check (e.g., 'permissions:index').
     * @return bool True if the user has the permission, false otherwise.
     */
    public static function can($permission) {
        // Ensure the user is logged in and the routes are in the session.
        if (!isset($_SESSION['user_logged_in']) || !isset($_SESSION['user_routes'])) {
            return false;
        }

        // Find the route with the matching permission_required string.
        foreach ($_SESSION['user_routes'] as $route) {
            if (isset($route['permission_required']) && $route['permission_required'] === $permission) {
                // Return the value of the 'has_permission' key.
                return isset($route['has_permission']) && $route['has_permission'] === true;
            }
        }

        // If the permission string is not found, deny permission.
        return false;
    }
}
