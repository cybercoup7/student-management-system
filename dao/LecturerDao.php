<?php
namespace SYS\DAO;

use Exception;

class LecturerDAO {
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

    // Create a new lecturer record
    public function createLecturer(array $lecturerData): bool
    {
        $role = 'lecturer';
        mysqli_begin_transaction($this->connection);
        try {
            // Insert into `user` table
            $userSql = "INSERT INTO user (user_id, f_name, l_name, role) VALUES (?, ?, ?, ?)";
            $userStmt = mysqli_prepare($this->connection, $userSql);
            mysqli_stmt_bind_param($userStmt, 'isss', $lecturerData['user_id'], $lecturerData['f_name'], $lecturerData['l_name'], $role);
            mysqli_stmt_execute($userStmt);

            // Insert into `lecturer` table
            $lecturerSql = "INSERT INTO lecturer (user_id, qualification, dept_id) VALUES (?, ?, ?)";
            $lecturerStmt = mysqli_prepare($this->connection, $lecturerSql);
            mysqli_stmt_bind_param($lecturerStmt, 'isi', $lecturerData['user_id'], $lecturerData['qualification'], $lecturerData['dept_id']);
            mysqli_stmt_execute($lecturerStmt);

            mysqli_commit($this->connection);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->connection);
            throw new Exception("Failed to create lecturer: " . $e->getMessage() . "\n" . mysqli_error($this->connection));
        }
    }

    // Read a lecturer's details by user ID
    public function getLecturer(int $userId): array
    {
        $sql = "SELECT user.user_id, user.f_name, user.l_name, user.role, 
                        lecturer.qualification, lecturer.dept_id
                FROM user 
                JOIN lecturer ON user.user_id = lecturer.user_id 
                WHERE user.user_id = ? and user.role = 'lecturer'";

        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $lecturer = mysqli_fetch_assoc($result) ?: [];

        return $lecturer;
    }

    // Update a lecturer's specific field
    public function updateLecturerField(int $userId, string $field, $newValue): bool
    {
        mysqli_begin_transaction($this->connection);

        try {
            $allowedUserFields = ['f_name', 'l_name', 'role'];
            $allowedLecturerFields = ['dept_id', 'qualification'];
            
            // Determine which table to update based on the field
            if (in_array($field, $allowedUserFields)) {
                $query = "UPDATE user SET $field = ? WHERE user_id = ?";
            } elseif (in_array($field, $allowedLecturerFields)) {
                $query = "UPDATE lecturer SET $field = ? WHERE user_id = ?";
            } else {
                throw new Exception("Invalid field: $field");
            }

            // Prepare and execute the query
            $stmt = mysqli_prepare($this->connection, $query);
            mysqli_stmt_bind_param($stmt, is_int($newValue) ? 'ii' : 'si', $newValue, $userId);
            mysqli_stmt_execute($stmt);

            mysqli_commit($this->connection);
            return true;

        } catch (Exception $e) {
            mysqli_rollback($this->connection);
            throw new Exception("Failed to update lecturer field: " . $e->getMessage() . "\n" . mysqli_error($this->connection));
        }
    }

    // Delete a lecturer record by user ID
    public function deleteLecturer(int $userId): bool
    {
        mysqli_begin_transaction($this->connection);
        try {
            // Delete from `lecturer` table first due to foreign key constraint
            $lecturerSql = "DELETE FROM lecturer WHERE user_id = ?";
            $lecturerStmt = mysqli_prepare($this->connection, $lecturerSql);
            mysqli_stmt_bind_param($lecturerStmt, 'i', $userId);
            mysqli_stmt_execute($lecturerStmt);

            // Delete from `user` table
            $userSql = "DELETE FROM user WHERE user_id = ?";
            $userStmt = mysqli_prepare($this->connection, $userSql);
            mysqli_stmt_bind_param($userStmt, 'i', $userId);
            mysqli_stmt_execute($userStmt);

            mysqli_commit($this->connection);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->connection);
            throw new Exception("Failed to delete lecturer: " . $e->getMessage() . "\n" . mysqli_error($this->connection));
        }
    }

    // List all lecturers with their details
    public function listAllLecturers(): array
    {
        $sql = "SELECT user.user_id, user.f_name, user.l_name, user.role, 
                       lecturer.qualification, lecturer.dept_id
                FROM user 
                JOIN lecturer ON user.user_id = lecturer.user_id 
                WHERE user.role = 'lecturer'";

        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $lecturers = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return $lecturers;
    }

     // Enroll a student in a course
     public function enrollStudentInCourse(int $studentId, int $courseId): bool
     {
         $sql = "INSERT INTO student_course (student_id, course_id) VALUES (?, ?)";
         $stmt = mysqli_prepare($this->connection, $sql);
         mysqli_stmt_bind_param($stmt, 'ii', $studentId, $courseId);
 
         return mysqli_stmt_execute($stmt);
     }
 
     // Post a grade for a student in a course
     public function postGrade(int $studentId, int $courseId, string $grade): bool
     {
         $sql = "INSERT INTO grades (student_id, course_id, grade) VALUES (?, ?, ?) 
                 ON DUPLICATE KEY UPDATE grade = ?";
         $stmt = mysqli_prepare($this->connection, $sql);
         mysqli_stmt_bind_param($stmt, 'iiss', $studentId, $courseId, $grade, $grade);
 
         return mysqli_stmt_execute($stmt);
     }
 
     // Get a list of students in a course
     public function getStudentsInCourse(int $courseId): array
     {
         $sql = "SELECT DISTINCT student_course.student_id, CONCAT(user.f_name, ' ', user.l_name) AS student_name, grades.grade
                 FROM student_course
                 JOIN user ON student_course.student_id = user.user_id
                 JOIN grades ON student_course.student_id = grades.student_id
                 WHERE student_course.course_id = ?";
         $stmt = mysqli_prepare($this->connection, $sql);
         mysqli_stmt_bind_param($stmt, 'i', $courseId);
         mysqli_stmt_execute($stmt);
 
         $result = mysqli_stmt_get_result($stmt);
         return mysqli_fetch_all($result, MYSQLI_ASSOC);
     }
}
?>
