<?php
// app/controllers/UserController.php

require_once BASE_PATH . '/app/models/UserModel.php';

class UserController {

    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    /**
     * Display the login page or redirect to dashboard if already logged in.
     */
    public function login() {
        if ($this->userModel->isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        }
        // This will render the login form without the main layout
        require_once BASE_PATH . '/app/views/auth/login.php';
    }

    /**
     * Handle the login form submission via AJAX.
     */
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


    /**
     * Display the list of users.
     */
    public function list() {
        if (!$this->userModel->isLoggedIn()) {
            header('Location: /user/login');
            exit;
        }
        $this->view('users/list', ['title' => 'Manage Users']);
    }

    /**
     * Handle logout.
     */
    public function logout() {
        $this->userModel->logout();
        header('Location: /user/login');
        exit;
    }

    /**
     * Proxy for the DataTables API request.
     */
    public function usersApi() {
        if (!$this->userModel->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        header('Content-Type: application/json');
        // Forward query parameters from DataTables to the model
        $response = $this->userModel->getUsers($_GET);
        // If token is invalid, API might return 401, handle it
        $data = json_decode($response, true);
        if (isset($data['http_code']) && $data['http_code'] == 401) {
             $this->userModel->logout();
        }
        echo $response; // The model returns a JSON string from the API
    }


    /**
     * Loads a view file within the main layout.
     *
     * @param string $viewName The name of the view file (e.g., 'users/list')
     * @param array $data Data to be extracted for the view
     */
    protected function view($viewName, $data = []) {
        extract($data);
        // This loads the main layout, which in turn will include the specific view
        require_once BASE_PATH . '/app/views/layouts/main.php';
    }
}
?>
