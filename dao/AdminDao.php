<?php
namespace SYS\DAO;

use Exception;

class AdminDAO {
    private $connection;
    private string $dbHost = 'localhost';
    private string $dbName = 'sms_db';
    private string $dbUser = 'root';
    private string $dbPassword = '';

    public function __construct()
    {
        $this->connection = mysqli_connect($this->dbHost, $this->dbUser, $this->dbPassword, $this->dbName);
        if (!$this->connection) {
            throw new Exception("Connection failed: " . mysqli_connect_error());
        }
    }

    // Create a new admin record
    public function createAdmin(array $adminData): bool
    {
        $role = 'admin';
        mysqli_begin_transaction($this->connection);
        try {
            $userSql = "INSERT INTO user (user_id, f_name, l_name, `role`) VALUES (?, ?, ?, ?)";
            $userStmt = mysqli_prepare($this->connection, $userSql);
            mysqli_stmt_bind_param($userStmt, 'isss', $adminData['user_id'], $adminData['f_name'], $adminData['l_name'], $role);
            mysqli_stmt_execute($userStmt);

            mysqli_commit($this->connection);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->connection);
            throw new Exception("Failed to create admin: " . $e->getMessage() . "\n" . mysqli_error($this->connection));
        }
    }

    // Get an admin's details by user ID
    public function getAdmin(int $userId): array
    {
        $role = 'admin';
        $sql = "SELECT user.user_id, user.f_name, user.l_name, user.role  
                FROM user 
                WHERE user.user_id = ? AND user.role = ?";

        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_bind_param($stmt, 'is', $userId, $role);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $admin = mysqli_fetch_assoc($result) ?: [];

        return $admin;
    }

    // Update an admin's field
    public function updateAdminField(int $userId, string $field, $newValue): bool
    {
        mysqli_begin_transaction($this->connection);
        try {
            $allowedUserFields = ['f_name', 'l_name', 'role'];
            
            if (in_array($field, $allowedUserFields)) {
                $query = "UPDATE user SET $field = ? WHERE user_id = ?";
            }
            else {
                throw new Exception("Invalid field: $field");
            }

            $stmt = mysqli_prepare($this->connection, $query);
            mysqli_stmt_bind_param($stmt, is_int($newValue) ? 'ii' : 'si', $newValue, $userId);
            mysqli_stmt_execute($stmt);

            mysqli_commit($this->connection);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->connection);
            throw new Exception("Failed to update admin field: " . $e->getMessage() . "\n" . mysqli_error($this->connection));
        }
    }

    // Delete an admin record by user ID
    public function deleteAdmin(int $userId): bool
    {
        mysqli_begin_transaction($this->connection);
        try {
            $userSql = "DELETE FROM user WHERE user_id = ?";
            $userStmt = mysqli_prepare($this->connection, $userSql);
            mysqli_stmt_bind_param($userStmt, 'i', $userId);
            mysqli_stmt_execute($userStmt);

            mysqli_commit($this->connection);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->connection);
            throw new Exception("Failed to delete admin: " . $e->getMessage() . "\n" . mysqli_error($this->connection));
        }
    }

    // List all admins with their details
    public function listAllAdmins(): array
    {
        $sql = "SELECT user.user_id, user.f_name, user.l_name, user.role
                FROM user 
                WHERE user.role = 'admin'";
                
        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $admins = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $admins;
    }
}
?>
