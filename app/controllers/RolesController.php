<?php
// app/controllers/RolesController.php

require_once BASE_PATH . '/app/helpers/ApiHelper.php';
require_once BASE_PATH . '/app/models/RoleModel.php';
require_once BASE_PATH . '/app/models/PermissionModel.php';
require_once BASE_PATH . '/app/models/UserModel.php';

class RolesController {

    private $roleModel;
    private $permissionModel;
    private $userModel;

    public function __construct() {
        $this->roleModel = new RoleModel();
        $this->permissionModel = new PermissionModel();
        $this->userModel = new UserModel();
    }

    public function index() {
        if (!$this->userModel->isLoggedIn()) {
            header('Location: /user/login');
            exit;
        }

        $apiResponseJson = $this->roleModel->getRoles($_GET);
        $apiResponseData = json_decode($apiResponseJson, true);

        $roles = $apiResponseData['data'] ?? [];
        $pagination = $apiResponseData['pagination'] ?? [];

        $this->view('roles/index', [
            'title' => 'Manage Roles',
            'roles' => $roles,
            'pagination' => $pagination
        ]);
    }

    public function create() {
        if (!$this->userModel->isLoggedIn()) {
            header('Location: /user/login');
            exit;
        }

        $permissionsData = $this->permissionModel->getAllPermissions();
        $permissions = $permissionsData['data'] ?? [];
        
        $groupedPermissions = [];
        foreach ($permissions as $permission) {
            $name = $permission['permission_name'] ?? 'unknown';
            $parts = explode(':', $name, 2);
            $group = ucfirst($parts[0]);
            $groupedPermissions[$group][] = $permission;
        }

        $this->view('roles/create', [
            'title' => 'Create New Role',
            'groupedPermissions' => $groupedPermissions
        ]);
    }

    public function store() {
        if (!$this->userModel->isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(403);
            exit("Forbidden");
        }

        $roleName = $_POST['role_name'] ?? '';
        // FIX: Changed 'role_description' to 'description' to match the form input name.
        $description = $_POST['description'] ?? ''; 
        $permissionIds = $_POST['permissions'] ?? []; 

        if (empty($roleName)) {
            $_SESSION['error_message'] = 'Role name is required.';
            header('Location: /roles/create');
            exit;
        }

        $response = $this->roleModel->createRole($roleName, $description, $permissionIds);

        if (isset($response['status']) && $response['status'] === 'success') {
            $_SESSION['success_message'] = 'Role created successfully!';
            header('Location: /roles');
        } else {
            $_SESSION['error_message'] = $response['message'] ?? 'An unknown error occurred.';
            header('Location: /roles/create');
        }
        exit;
    }

    public function edit($id) {
        if (!$this->userModel->isLoggedIn()) {
            header('Location: /user/login');
            exit;
        }

        $roleData = $this->roleModel->getRoleById($id);
        if (!$roleData || !isset($roleData['data'])) {
            $_SESSION['error_message'] = 'Role not found.';
            header('Location: /roles');
            exit;
        }
        $role = $roleData['data'];
        $assignedPermissionIds = array_column($role['permissions'], 'id');

        $permissionsData = $this->permissionModel->getAllPermissions();
        $permissions = $permissionsData['data'] ?? [];
        
        $groupedPermissions = [];
        foreach ($permissions as $permission) {
            $name = $permission['permission_name'] ?? 'unknown';
            $parts = explode(':', $name, 2);
            $group = ucfirst($parts[0]);
            $groupedPermissions[$group][] = $permission;
        }

        $this->view('roles/edit', [
            'title' => 'Edit Role',
            'role' => $role,
            'groupedPermissions' => $groupedPermissions,
            'assignedPermissionIds' => $assignedPermissionIds
        ]);
    }

    public function update() {
        if (!$this->userModel->isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(403);
            exit("Forbidden");
        }

        $id = $_POST['id'] ?? 0;
        $roleName = $_POST['role_name'] ?? '';
        // FIX: Changed 'role_description' to 'description' to match the form input name.
        $description = $_POST['description'] ?? '';
        $permissionIds = $_POST['permissions'] ?? [];

        if (empty($id) || empty($roleName)) {
            $_SESSION['error_message'] = 'Role ID and Name are required.';
            header('Location: /roles/edit/' . $id);
            exit;
        }

        $response = $this->roleModel->updateRole($id, $roleName, $description, $permissionIds);

        if (isset($response['status']) && $response['status'] === 'success') {
            $_SESSION['success_message'] = 'Role updated successfully!';
            header('Location: /roles');
        } else {
            $_SESSION['error_message'] = $response['message'] ?? 'An unknown error occurred during update.';
            header('Location: /roles/edit/' . $id);
        }
        exit;
    }

    public function delete() {
       if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
          http_response_code(405);
          echo json_encode(['status' => 'error', 'message' => 'Delete requires POST.']);
          exit;
      }
      if (!$this->userModel->isLoggedIn()) {
          http_response_code(403);
          echo json_encode(['status' => 'error', 'message' => 'Forbidden: Not logged in.']);
          exit;
      }

      header('Content-Type: application/json');
      $data = json_decode(file_get_contents('php://input'), true);
      $id = $data['id'] ?? 0;

      if (empty($id)) {
          http_response_code(400);
          echo json_encode(['status' => 'error', 'message' => 'Role ID is required for deletion.']);
          exit;
      }

      $response = $this->roleModel->deleteRole($id);
      echo json_encode($response);
  }

    protected function view($viewName, $data = []) {
        extract($data);
        require BASE_PATH . '/app/views/layouts/main.php';
    }
}
