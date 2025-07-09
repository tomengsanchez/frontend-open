<?php
// app/controllers/RolesController.php

require_once BASE_PATH . '/app/helpers/ApiHelper.php';
require_once BASE_PATH . '/app/models/RoleModel.php';
require_once BASE_PATH . '/app/models/UserModel.php';

class RolesController {

    private $roleModel;
    private $userModel;

    public function __construct() {
        $this->roleModel = new RoleModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display the roles list page.
     */
    public function index() {
        if (!$this->userModel->isLoggedIn()) {
            header('Location: /user/login');
            exit;
        }

        // Fetch roles from the model, passing all GET parameters from the URL
        $apiResponseJson = $this->roleModel->getRoles($_GET);
        $apiResponseData = json_decode($apiResponseJson, true);

        // Prepare data for the view
        $roles = $apiResponseData['data'] ?? [];
        $pagination = $apiResponseData['pagination'] ?? [];

        // Pass the data to the view
        $this->view('roles/index', [
            'title' => 'Manage Roles',
            'roles' => $roles,
            'pagination' => $pagination
        ]);
    }

    // Placeholder for future CRUD actions
    // public function create() {}
    // public function store() {}
    // public function edit($id) {}
    // public function update() {}
    // public function delete() {}

    protected function view($viewName, $data = []) {
        extract($data);
        require BASE_PATH . '/app/views/layouts/main.php';
    }
}
