<?php
// app/controllers/DashboardController.php

require_once BASE_PATH . '/app/models/UserModel.php';

class DashboardController {

    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function index() {
        error_log("[CONTROLLER] Inside DashboardController->index()");
        
        if (!$this->userModel->isLoggedIn()) {
            error_log("[CONTROLLER] User is NOT logged in. Redirecting to /user/login");
            header('Location: /user/login');
            exit;
        }

        error_log("[CONTROLLER] User is logged in. Calling view method to render 'dashboard/index'.");
        $this->view('dashboard/index', ['title' => 'Dashboard']);
    }

    protected function view($viewName, $data = []) {
        error_log("[VIEW] Inside view method for '{$viewName}'.");
        extract($data);

        $layout_file = BASE_PATH . '/app/views/layouts/main.php';
        error_log("[VIEW] Preparing to load layout file: {$layout_file}");

        if (file_exists($layout_file)) {
            error_log("[VIEW] Layout file found. Requiring it now.");
            require $layout_file;
            error_log("[VIEW] Finished requiring layout file.");
        } else {
            error_log("[VIEW] FATAL ERROR: Layout file not found at {$layout_file}");
        }
    }
}
