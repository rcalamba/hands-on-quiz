<?php
class Authentication
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function login($username, $password)
{
    if (empty($username) || empty($password)) {
        return array("message" => "Username and password are required");
    }

    $stmt = $this->conn->prepare("SELECT * FROM users_table WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['userid'] = $user['userid'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['roleid'] = $user['roleid'];

        // Update is_logged_in for the user
        $updateStmt = $this->conn->prepare("UPDATE users_table SET is_logged_in = 1 WHERE userid = ?");
        $updateStmt->bind_param("i", $user['userid']);
        $updateStmt->execute();

        // Display a message based on the role
        $roleMessage = "";
        switch ($_SESSION['roleid']) {
            case 1:
                $roleMessage = "You are an ADMIN.";
                break;
            case 2:
                $roleMessage = "You are a FACULTY member.";
                break;
            case 3:
                $roleMessage = "You are a STUDENT.";
                break;
            default:
                $roleMessage = "Your role is undefined.";
                break;
        }

        return array("message" => "Login successful", "user" => $user, "role_message" => $roleMessage);
    } else {
        return array("message" => "Invalid username or password");
    }
}

    public function logout()
    {
        if (isset($_SESSION['userid'])) {
            // Update is_logged_in to 0 for the logged-out user
            $updateStmt = $this->conn->prepare("UPDATE users_table SET is_logged_in = 0 WHERE userid = ?");
            $updateStmt->bind_param("i", $_SESSION['userid']);
            $updateStmt->execute();

            // Unset all session variables
            session_unset();

            // Destroy the session
            session_destroy();

            return array("message" => "Logout successful");
        } else {
            return array("message" => "Already logged out");
        }
    }

    public function register($username, $password)
    {
        $stmt = $this->conn->prepare("INSERT INTO users_table (username, password, roleid) VALUES (?, ?, 3)");
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            return array("message" => "User registered successfully");
        } else {
            return array("message" => "Failed to register user");
        }
    }
}
?>