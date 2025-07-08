<?php 

require_once BASE_PATH . '/app/helpers/ApiHelper.php';

class PermissionsController {
    public function index() {
        $this->view('permissions/index', ['title' => 'Manage Permissions']);
    }

    protected function view($viewName, $data = []) {
        extract($data);
        require BASE_PATH . '/app/views/layouts/main.php';
    }
}