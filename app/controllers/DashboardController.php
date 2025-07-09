<?php
// app/controllers/DashboardController.php

require_once BASE_PATH . '/app/helpers/AuthHelper.php';
require_once BASE_PATH . '/app/models/UserModel.php';

class DashboardController {

    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    private function checkAccess($permission) {
        if (!AuthHelper::can($permission)) {
            http_response_code(403);
            exit("<h1>403 Forbidden</h1><p>You do not have permission to access this page.</p>");
        }
    }

    public function index() {
        // The checkAccess call is now done after the session is populated by main.php
        if (!$this->userModel->isLoggedIn()) {
            header('Location: /user/login');
            exit;
        }
        
        $this->checkAccess('dashboard:index');

        $this->view('dashboard/index', ['title' => 'Dashboard']);
    }

    protected function view($viewName, $data = []) {
        extract($data);
        require BASE_PATH . '/app/views/layouts/main.php';
    }
}
