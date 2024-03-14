<?php
session_start();
require_once 'Database.php';
require_once 'Authentication.php';
require_once 'AdminController.php';

// Initialize Database
$database = new Database();
$db = $database->conn;

// Create Authentication and AdminController instances
$auth = new Authentication($db);
$adminController = new AdminController($db);

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['endpoint'])) {
        $endpoint = $_GET['endpoint'];

        switch ($endpoint) {
            case 'login':
                // Handle login endpoint
                if (isset($_POST['username']) && isset($_POST['password'])) {
                    $username = $_POST['username'];
                    $password = $_POST['password'];
                    $result = $auth->login($username, $password);
                    echo json_encode($result);
                } else {
                    echo json_encode(array("message" => "Username and password are required"));
                }
                break;
            case 'create_user':
                // Handle create_user endpoint
                if ($_SESSION['roleid'] === 1) {
                    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['roleid'])) {
                        $username = $_POST['username'];
                        $password = $_POST['password'];
                        $roleId = $_POST['roleid'];
                        $status = 1; // Active by default
                        $result = $adminController->createUser($username, $password, $roleId, $status);
                        echo json_encode($result);
                    } else {
                        echo json_encode(array("message" => "Username, password, and roleid are required"));
                    }
                } else {
                    echo json_encode(array("message" => "Only ADMIN can create users"));
                }
                break;
            case 'logout':
                // Handle logout endpoint
                $result = $auth->logout();
                echo json_encode($result);
                break;
            // Add more cases for other endpoints as needed
            default:
                echo json_encode(array("message" => "Invalid endpoint"));
                break;
        }
    } else {
        echo json_encode(array("message" => "Endpoint parameter is missing"));
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['endpoint'])) {
        $endpoint = $_GET['endpoint'];

        switch ($endpoint) {
            case 'view_users':
                // Handle view_users endpoint
                if ($_SESSION['roleid'] === 1) {
                    $result = $adminController->viewUsers();
                    echo json_encode($result);
                } else {
                    echo json_encode(array("message" => "Only ADMIN can view users"));
                }
                break;
            // Add more cases for other GET endpoints as needed
            default:
                echo json_encode(array("message" => "Invalid endpoint"));
                break;
        }
    } else {
        echo json_encode(array("message" => "Endpoint parameter is missing"));
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Handle PUT requests
    parse_str(file_get_contents("php://input"), $_PUT);

    if (isset($_GET['endpoint'])) {
        $endpoint = $_GET['endpoint'];

        switch ($endpoint) {
            case 'change_user_role':
                // Handle change_user_role endpoint
                if ($_SESSION['roleid'] === 1) {
                    if (isset($_PUT['userId']) && isset($_PUT['newRoleId'])) {
                        $userId = $_PUT['userId'];
                        $newRoleId = $_PUT['newRoleId'];
                        $result = $adminController->changeUserRole($userId, $newRoleId);
                        echo json_encode($result);
                    } else {
                        echo json_encode(array("message" => "userId and newRoleId are required"));
                    }
                } else {
                    echo json_encode(array("message" => "Only ADMIN can change user roles"));
                }
                break;
            // Add more cases for other PUT endpoints as needed
            default:
                echo json_encode(array("message" => "Invalid endpoint"));
                break;
        }
    } else {
        echo json_encode(array("message" => "Endpoint parameter is missing"));
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Handle DELETE requests
    parse_str(file_get_contents("php://input"), $_DELETE);

    if (isset($_GET['endpoint'])) {
        $endpoint = $_GET['endpoint'];

        switch ($endpoint) {
            case 'disable_user':
                // Handle disable_user endpoint
                if ($_SESSION['roleid'] === 1) {
                    if (isset($_DELETE['userId'])) {
                        $userId = $_DELETE['userId'];
                        $result = $adminController->disableUser($userId);
                        echo json_encode($result);
                    } else {
                        echo json_encode(array("message" => "userId is required"));
                    }
                } else {
                    echo json_encode(array("message" => "Only ADMIN can disable users"));
                }
                break;
            // Add more cases for other DELETE endpoints as needed
            default:
                echo json_encode(array("message" => "Invalid endpoint"));
                break;
        }
    } else {
        echo json_encode(array("message" => "Endpoint parameter is missing"));
    }
} else {
    echo json_encode(array("message" => "Method Not Allowed"));
}
?>
