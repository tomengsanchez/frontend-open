<?php
// app/controllers/PermissionsController.php

require_once BASE_PATH . '/app/helpers/AuthHelper.php';
require_once BASE_PATH . '/app/helpers/ApiHelper.php';
require_once BASE_PATH . '/app/models/PermissionModel.php';
require_once BASE_PATH . '/app/models/UserModel.php';


class PermissionsController {

    private $permissionModel;
    private $userModel;

    public function __construct() {
        $this->permissionModel = new PermissionModel();
        $this->userModel = new UserModel();
    }

    private function checkAccess($permission) {
        if (!AuthHelper::can($permission)) {
            http_response_code(403);
            // You can create a dedicated "access_denied" view later
            exit("<h1>403 Forbidden</h1><p>You do not have permission to access this page.</p>");
        }
    }

    public function index() {
        // FIX: Changed to lowercase to match API standard
        $this->checkAccess('permissions:index');
        
        if (!$this->userModel->isLoggedIn()) {
            header('Location: /user/login');
            exit;
        }

        $apiResponseJson = $this->permissionModel->getPermissions($_GET);
        $apiResponseData = json_decode($apiResponseJson, true);

        $permissions = $apiResponseData['data'] ?? [];
        $pagination = $apiResponseData['pagination'] ?? [];

        $this->view('permissions/index', [
            'title' => 'Manage Permissions',
            'permissions' => $permissions,
            'pagination' => $pagination
        ]);
    }

    public function create() {
        // FIX: Changed to lowercase
        $this->checkAccess('permissions:create');
        
        if (!$this->userModel->isLoggedIn()) {
            header('Location: /user/login');
            exit;
        }
        $this->view('permissions/create', ['title' => 'Create New Permission']);
    }

    public function store() {
        // FIX: Changed to lowercase
        $this->checkAccess('permissions:create');
        
        if (!$this->userModel->isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(403);
            exit("Forbidden");
        }

        $name = $_POST['permission_name'] ?? '';
        $description = $_POST['description'] ?? '';

        if (empty($name)) {
            $_SESSION['error_message'] = 'Permission name is required.';
            header('Location: /permissions/create');
            exit;
        }

        $response = $this->permissionModel->createPermission($name, $description);

        if (isset($response['status']) && $response['status'] === 'success') {
            $_SESSION['success_message'] = 'Permission created successfully!';
            header('Location: /permissions');
        } else {
            $_SESSION['error_message'] = $response['message'] ?? 'An unknown error occurred.';
            header('Location: /permissions/create');
        }
        exit;
    }

    public function edit($id) {
        // FIX: Changed to lowercase
        $this->checkAccess('permissions:edit');
        
        if (!$this->userModel->isLoggedIn()) {
            header('Location: /user/login');
            exit;
        }

        $permissionResponse = $this->permissionModel->getPermissionById($id);

        if (!$permissionResponse || (isset($permissionResponse['status']) && $permissionResponse['status'] === 'error') || !isset($permissionResponse['data'])) {
            $_SESSION['error_message'] = 'Permission not found or API error.';
            header('Location: /permissions');
            exit;
        }

        $this->view('permissions/edit', [
            'title' => 'Edit Permission',
            'permission' => $permissionResponse['data']
        ]);
    }

    public function update() {
        // FIX: Changed to lowercase
        $this->checkAccess('permissions:edit');
        
        if (!$this->userModel->isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(403);
            exit("Forbidden");
        }

        $id = $_POST['id'] ?? 0;
        $name = $_POST['permission_name'] ?? '';
        $description = $_POST['description'] ?? '';

        if (empty($id) || empty($name)) {
            $_SESSION['error_message'] = 'ID and Permission name are required.';
            header('Location: /permissions/edit/' . $id);
            exit;
        }

        $response = $this->permissionModel->updatePermission($id, $name, $description);

        if (isset($response['status']) && $response['status'] === 'success') {
            $_SESSION['success_message'] = 'Permission updated successfully!';
            header('Location: /permissions');
        } else {
            $_SESSION['error_message'] = $response['message'] ?? 'An unknown error occurred during update.';
            header('Location: /permissions/edit/' . $id);
        }
        exit;
    }

    public function delete() {
        // FIX: Changed to lowercase
        $this->checkAccess('permissions:delete');
        
        if (!$this->userModel->isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
           http_response_code(403);
           echo json_encode(['status' => 'error', 'message' => 'Forbidden: Not logged in.']);
           exit;
       }

       header('Content-Type: application/json');
       $data = json_decode(file_get_contents('php://input'), true);
       $id = $data['id'] ?? 0;

       if (empty($id)) {
           http_response_code(400);
           echo json_encode(['status' => 'error', 'message' => 'Permission ID is required for deletion.']);
           exit;
       }

       $response = $this->permissionModel->deletePermission($id);
       echo json_encode($response);
    }


    protected function view($viewName, $data = []) {
        extract($data);
        require BASE_PATH . '/app/views/layouts/main.php';
    }
}
