<?php 


class RolesController {
    public function index() {
        $this->view('roles/index', ['title' => 'Manage Roles']);
    }

    protected function view($viewName, $data = []) {
        extract($data);
        require BASE_PATH . '/app/views/layouts/main.php';
    }
}