<?php
namespace SYS\DAO;
use Exception;

class DAO
{
    private $dbConnection;
    private $dbHost = 'localhost';
    private $dbUser = 'sims_user';
    private $dbPassword = '1234567890987654321234567890';
    private $dbName = 'sms_db';

    public function __construct()
    {
        $this->dbConnection = mysqli_connect($this->dbHost, $this->dbUser, $this->dbPassword, $this->dbName);

        if (!$this->dbConnection) {
            throw new Exception('Database connection failed: ' . mysqli_connect_error());
        }

        mysqli_set_charset($this->dbConnection, 'utf8');
    }

    /**
     * insert user
     */
    public function insertUser($user)
{
    // Check if required fields are present
    $requiredFields = ['user_id', 'password'];
    $user = (array) $user;  // Convert $user to an array

    $missingFields = array_diff_key(array_flip($requiredFields), $user);

    if (!empty($missingFields)) {
        throw new Exception('Missing required user fields: ' . implode(', ', array_keys($missingFields)));
    }


    // Prepare the query
    $query = "INSERT INTO user (user_id, password_hash, f_name, l_name) VALUES (?, ?, ?, ?)";

    // Prepare the statement
    $stmt = mysqli_prepare($this->dbConnection, $query);

    // Check if statement preparation failed
    if (!$stmt) {
        throw new Exception('Database query preparation failed: ' . mysqli_error($this->dbConnection));
    }

    // Bind parameters
    $user_id = $user['user_id'];
    $password = password_hash($user['password'], PASSWORD_BCRYPT); // Hash the password
    $f_name = $user['f_name'];
    $l_name = $user['l_name'] ?? null; // Use null if name is not provided

    mysqli_stmt_bind_param($stmt, 'isss', $user_id, $password, $f_name, $l_name);

    // Execute the statement
    $result = mysqli_stmt_execute($stmt);

    // Close the statement
    mysqli_stmt_close($stmt);

    // Check if query execution failed
    if (!$result) {
        throw new Exception('Database query failed: ' . mysqli_error($this->dbConnection));
    }

    return true;
}

    /**
     * Fetch a user by ID.
     */
    public function fetchUser(int $userId)
    {
        $query = "SELECT * FROM user WHERE user_id = '$userId'";
        $result = mysqli_query($this->dbConnection, $query);

        if (!$result) {
            die("Database query failed: ".$query);
            throw new Exception('Database query failed: ' . mysqli_error($this->dbConnection));
        }

        $userData = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        if (!$userData) {
            return null;
        }

        return $userData;
    }

    /**
     * Set a user's token.
     */
    public function setUserToken($userId, string $token): bool{
        $query = "UPDATE user SET access_token = '$token' WHERE user_id = '$userId'";
        $result = mysqli_query($this->dbConnection, $query);

        return $result !== false;
    }

    /**
     * Delete a user's token.
     */
    public function deleteUserToken(string $token): bool
    {
        $query = "UPDATE user SET access_token = NULL WHERE access_token = '$token'";
        $result = mysqli_query($this->dbConnection, $query);

        return $result !== false;
    }

    /**
     * Get a user by token.
     */
    public function getUserByToken(string $token)
    {
        $query = "SELECT * FROM user WHERE access_token = '$token'";
        $result = mysqli_query($this->dbConnection, $query);

        if (!$result) {
            throw new Exception('Database query failed: ' . mysqli_error($this->dbConnection));
        }

        $userData = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        if (!$userData) {
            return null;
        }

        return $userData;
    }

    public function __destruct()
    {
        mysqli_close($this->dbConnection);
    }
}

?>