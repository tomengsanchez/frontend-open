<?php
// app/controllers/PermissionsController.php

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

    public function index() {
         if (!$this->userModel->isLoggedIn()) {
            header('Location: /user/login');
            exit;
        }
        $this->view('permissions/index', ['title' => 'Manage Permissions']);
    }

    public function permissionsApi() {
        if (!$this->userModel->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        header('Content-Type: application/json');
        $response = $this->permissionModel->getPermissions($_GET);
        echo $response;
    }

    public function create() {
        if (!$this->userModel->isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Forbidden']);
            exit;
        }

        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $name = $data['permission_name'] ?? '';
        $description = $data['description'] ?? '';

        if (empty($name)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Permission name is required.']);
            exit;
        }

        $response = $this->permissionModel->createPermission($name, $description);
        echo json_encode($response);
    }

    public function update() {
        if (!$this->userModel->isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') { // Should be POST for simplicity with AJAX
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Forbidden']);
            exit;
        }

        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? 0;
        $name = $data['permission_name'] ?? '';
        $description = $data['description'] ?? '';

        if (empty($id) || empty($name)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID and Permission name are required.']);
            exit;
        }

        $response = $this->permissionModel->updatePermission($id, $name, $description);
        echo json_encode($response);
    }

    public function delete() {
         if (!$this->userModel->isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Forbidden']);
            exit;
        }

        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? 0;

        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Permission ID is required.']);
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
