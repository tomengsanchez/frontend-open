<?php 
require_once BASE_PATH . '/app/helpers/ApiHelper.php';
class TestController {

    public function index() {
        // This is a placeholder for the index method.
        // You can add logic here to handle requests to the test controller.
        session_start();

        $response = ApiHelper::request("/routes");

        print_r($response);

        
    }

    public function testMethod() {
        // This is a placeholder for a test method.
        // You can add logic here to test specific functionality.
        echo "This is a test method in the TestController.";
    }
}