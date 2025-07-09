<?php
// app/controllers/UserController.php

require_once BASE_PATH . '/app/helpers/ApiHelper.php';
require_once BASE_PATH . '/app/models/UserModel.php';
require_once BASE_PATH . '/app/models/RoleModel.php'; // Required to get roles for the dropdown

class UserController {

    private $userModel;
    private $roleModel; // Added

    public function __construct() {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel(); // Added
    }

    public function list() {
        if (!$this->userModel->isLoggedIn()) {
            header('Location: /user/login');
            exit;
        }
        
        $apiResponseJson = $this->userModel->getUsers($_GET);
        $apiResponseData = json_decode($apiResponseJson, true);

        $users = $apiResponseData['data'] ?? [];
        $pagination = $apiResponseData['pagination'] ?? [];

        $this->view('users/list', [
            'title' => 'Manage Users',
            'users' => $users,
            'pagination' => $pagination
        ]);
    }

    public function create() {
        if (!$this->userModel->isLoggedIn()) {
            header('Location: /user/login');
            exit;
        }

        $rolesData = $this->roleModel->getAllRoles();
        $roles = $rolesData['data'] ?? [];

        $this->view('users/create', [
            'title' => 'Create New User',
            'roles' => $roles
        ]);
    }

    public function store() {
        if (!$this->userModel->isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(403);
            exit("Forbidden");
        }

        $userData = [
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'password_confirmation' => $_POST['password_confirmation'] ?? '',
            'role_id' => $_POST['role_id'] ?? null
        ];

        if (empty($userData['username']) || empty($userData['email']) || empty($userData['password'])) {
            $_SESSION['error_message'] = 'Username, Email, and Password are required.';
            header('Location: /user/create');
            exit;
        }
        if ($userData['password'] !== $userData['password_confirmation']) {
            $_SESSION['error_message'] = 'Passwords do not match.';
            header('Location: /user/create');
            exit;
        }

        $response = $this->userModel->createUser($userData);

        if (isset($response['status']) && $response['status'] === 'success') {
            $_SESSION['success_message'] = 'User created successfully!';
            header('Location: /user/list');
        } else {
            $_SESSION['error_message'] = $response['message'] ?? 'An unknown error occurred.';
            header('Location: /user/create');
        }
        exit;
    }

    /**
     * Show the form for editing an existing user.
     * @param int $id The ID of the user to edit.
     */
    public function edit($id) {
        if (!$this->userModel->isLoggedIn()) {
            header('Location: /user/login');
            exit;
        }

        // Fetch the specific user's details
        $userData = $this->userModel->getUserById($id);
        if (!$userData || !isset($userData['data'])) {
            $_SESSION['error_message'] = 'User not found.';
            header('Location: /user/list');
            exit;
        }
        $user = $userData['data'];

        // Fetch all available roles for the dropdown
        $rolesData = $this->roleModel->getAllRoles();
        $roles = $rolesData['data'] ?? [];

        $this->view('users/edit', [
            'title' => 'Edit User',
            'user' => $user,
            'roles' => $roles
        ]);
    }

    /**
     * Update an existing user in the database.
     */
    public function update() {
        if (!$this->userModel->isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(403);
            exit("Forbidden");
        }

        $id = $_POST['id'] ?? 0;
        $userData = [
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'role_id' => $_POST['role_id'] ?? null
        ];

        // Conditionally add password to the update payload if it's provided
        if (!empty($_POST['password'])) {
            if ($_POST['password'] !== $_POST['password_confirmation']) {
                $_SESSION['error_message'] = 'Passwords do not match.';
                header('Location: /user/edit/' . $id);
                exit;
            }
            $userData['password'] = $_POST['password'];
            $userData['password_confirmation'] = $_POST['password_confirmation'];
        }

        if (empty($id) || empty($userData['username']) || empty($userData['email'])) {
            $_SESSION['error_message'] = 'User ID, Username, and Email are required.';
            header('Location: /user/edit/' . $id);
            exit;
        }

        $response = $this->userModel->updateUser($id, $userData);

        if (isset($response['status']) && $response['status'] === 'success') {
            $_SESSION['success_message'] = 'User updated successfully!';
            header('Location: /user/list');
        } else {
            $_SESSION['error_message'] = $response['message'] ?? 'An unknown error occurred during update.';
            header('Location: /user/edit/' . $id);
        }
        exit;
    }


    public function login() {
        if ($this->userModel->isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        }
        require_once BASE_PATH . '/app/views/auth/login.php';
    }

    public function handleLogin() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            $response = $this->userModel->login($username, $password);
            echo json_encode($response);
        } else {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
        }
    }

    public function logout() {
        $this->userModel->logout();
        header('Location: /user/login');
        exit;
    }

    protected function view($viewName, $data = []) {
        extract($data);
        require_once BASE_PATH . '/app/views/layouts/main.php';
    }
}
