<?php
class AdminController
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createUser($username, $password, $roleId, $status)
    {
        if ($_SESSION['roleid'] === 1) {
            $stmt = $this->conn->prepare("INSERT INTO users_table (username, password, roleid, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssii", $username, $password, $roleId, $status);
            if ($stmt->execute()) {
                return array("message" => "User created successfully");
            } else {
                return array("message" => "Failed to create user");
            }
        } else {
            return array("message" => "Only ADMIN can create users");
        }
    }

    public function createRole($role, $description)
    {
        if ($_SESSION['roleid'] === 1) {
            $stmt = $this->conn->prepare("INSERT INTO roles_table (role, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $role, $description);
            if ($stmt->execute()) {
                return array("message" => "Role created successfully");
            } else {
                return array("message" => "Failed to create role");
            }
        } else {
            return array("message" => "Only ADMIN can create roles");
        }
    }

    public function viewUsers()
    {
        if ($_SESSION['roleid'] === 1) {
            $result = $this->conn->query("SELECT * FROM users_table");
            if ($result->num_rows > 0) {
                $users = $result->fetch_all(MYSQLI_ASSOC);
                return array("message" => "Users retrieved successfully", "users" => $users);
            } else {
                return array("message" => "No users found");
            }
        } else {
            return array("message" => "Only ADMIN can view users");
        }
    }

    public function changeUserRole($userId, $newRoleId)
    {
        if ($_SESSION['roleid'] === 1) {
            $stmt = $this->conn->prepare("UPDATE users_table SET roleid = ? WHERE userid = ?");
            $stmt->bind_param("ii", $newRoleId, $userId);
            if ($stmt->execute()) {
                return array("message" => "User role updated successfully");
            } else {
                return array("message" => "Failed to update user role");
            }
        } else {
            return array("message" => "Only ADMIN can change user roles");
        }
    }

    public function disableUser($userId)
    {
        if ($_SESSION['roleid'] === 1) {
            $stmt = $this->conn->prepare("UPDATE users_table SET status = 0 WHERE userid = ?");
            $stmt->bind_param("i", $userId);
            if ($stmt->execute()) {
                return array("message" => "User disabled successfully");
            } else {
                return array("message" => "Failed to disable user");
            }
        } else {
            return array("message" => "Only ADMIN can disable users");
        }
    }

    public function disableRole($roleId)
    {
        if ($_SESSION['roleid'] === 1) {
            $stmt = $this->conn->prepare("UPDATE roles_table SET status = 0 WHERE roleid = ?");
            $stmt->bind_param("i", $roleId);
            if ($stmt->execute()) {
                return array("message" => "Role disabled successfully");
            } else {
                return array("message" => "Failed to disable role");
            }
        } else {
            return array("message" => "Only ADMIN can disable roles");
        }
    }
}
?>
