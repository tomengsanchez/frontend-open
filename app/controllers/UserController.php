<?php
// app/controllers/UserController.php

// Require the helper first, as the model depends on it.
require_once BASE_PATH . '/app/helpers/ApiHelper.php';
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
     * Display the list of users with pagination.
     */
    public function list() {
        if (!$this->userModel->isLoggedIn()) {
            header('Location: /user/login');
            exit;
        }
        
        // Fetch users from the model, passing all GET parameters
        $apiResponseJson = $this->userModel->getUsers($_GET);
        $apiResponseData = json_decode($apiResponseJson, true);

        // Prepare data for the view
        $users = $apiResponseData['data'] ?? [];
        $pagination = $apiResponseData['pagination'] ?? [];

        $this->view('users/list', [
            'title' => 'Manage Users',
            'users' => $users,
            'pagination' => $pagination
        ]);
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
     * The usersApi method is no longer needed and has been removed.
     */

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
