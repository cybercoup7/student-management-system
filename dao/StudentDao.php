<?php
namespace SYS\DAO;

use Exception;

class StudentDAO {
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

    // Create a new student record
    public function createStudent(array $studentData): bool
    {
        $role= 'student';
        mysqli_begin_transaction($this->connection);
        try {
            $userSql = "INSERT INTO user (user_id, f_name, l_name, role) VALUES (?, ?, ?, ?)";
            $userStmt = mysqli_prepare($this->connection, $userSql);
            mysqli_stmt_bind_param($userStmt, 'isss', $studentData['user_id'], $studentData['f_name'], $studentData['l_name'], $role);
            mysqli_stmt_execute($userStmt);

            $studentSql = "INSERT INTO student (user_id, program_d) VALUES (?, ?)";
            $studentStmt = mysqli_prepare($this->connection, $studentSql);
            mysqli_stmt_bind_param($studentStmt, 'is', $studentData['user_id'], $studentData['program_id']);
            mysqli_stmt_execute($studentStmt);

            mysqli_commit($this->connection);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->connection);
            throw new Exception("Failed to create student: " . $e->getMessage() . "\n" . mysqli_error($this->connection));
        }
    }

    // Read a student's details by user ID
    public function getStudent(int $userId): array
    {
        $sql = "SELECT user.user_id, user.f_name, user.l_name, user.role, student.program_id 
                FROM user 
                JOIN student ON user.user_id = student.user_id 
                WHERE user.user_id = ?";
        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $student = mysqli_fetch_assoc($result) ?: [];

        return $student;
    }

    public function getStudentCourses(int $userId): array
{
    $sql = "SELECT course.course_id, course.course_name, course.description, dept.dept_name
            FROM student_course
            JOIN course ON student_course.course_id = course.course_id
            JOIN dept ON course.dept_id = dept.dept_id
            WHERE student_course.user_id = ?";

    $stmt = mysqli_prepare($this->connection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $courses = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $courses;
}

    // Update a student's details
    public function updateStudentField(int $userId, string $field, $newValue): bool
{
    mysqli_begin_transaction($this->connection);

    try {
        $allowedUserFields = ['f_name', 'l_name', 'role'];
        $allowedStudentFields = ['program_id', 'g12_grade', 'next_of_kin'];
        
        // Determine which table to update based on the field
        if (in_array($field, $allowedUserFields)) {
            $query = "UPDATE user SET $field = ? WHERE user_id = ?";
        } elseif (in_array($field, $allowedStudentFields)) {
            $query = "UPDATE student SET $field = ? WHERE user_id = ?";
        } else {
            throw new Exception("Invalid field: $field");
        }

        // Prepare and execute the query
        $stmt = mysqli_prepare($this->connection, $query);
        mysqli_stmt_bind_param($stmt, 'si', $newValue, $userId);
        mysqli_stmt_execute($stmt);
        
        mysqli_commit($this->connection);
        return true;

    } catch (Exception $e) {
        mysqli_rollback($this->connection);
        throw new Exception("Failed to update student field: " . $e->getMessage() . "\n" . mysqli_error($this->connection));
    }
}


    // Delete a student record by user ID
    public function deleteStudent(int $userId): bool
    {
        mysqli_begin_transaction($this->connection);
        try {
            // Delete from `student` table first due to foreign key constraint
            $studentSql = "DELETE FROM student WHERE user_id = ?";
            $studentStmt = mysqli_prepare($this->connection, $studentSql);
            mysqli_stmt_bind_param($studentStmt, 'i', $userId);
            mysqli_stmt_execute($studentStmt);

            // Delete from `user` table
            $userSql = "DELETE FROM user WHERE user_id = ?";
            $userStmt = mysqli_prepare($this->connection, $userSql);
            mysqli_stmt_bind_param($userStmt, 'i', $userId);
            mysqli_stmt_execute($userStmt);

            mysqli_commit($this->connection);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->connection);
            throw new Exception("Failed to delete student: " . $e->getMessage() . "\n" . mysqli_error($this->connection));
        }
    }

    // List all students with their details
    public function listAllStudents(): array
    {
        $sql = "SELECT user.user_id, user.f_name, user.l_name, user.role, student.program_id
                FROM user
                JOIN student ON user.user_id = student.user_id";
        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $students = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return $students;
    }

    public function getStudentGrades(int $studentId): array
    {
        $sql = "SELECT CONCAT(user.f_name, ' ', user.l_name) AS student_name, course.course_name, grade.grade 
                FROM grade 
                JOIN user ON grade.student_id = user.user_id 
                JOIN course ON grade.course_id = course.course_id 
                WHERE grade.student_id = ?";
        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $studentId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $grades = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return $grades;
    }
    public function getAllStudentGrades(): array
    {
        $sql = "SELECT CONCAT(user.f_name, ' ', user.l_name) AS student_name, course.course_name, grade.grade 
                FROM grade 
                JOIN user ON grade.student_id = user.user_id 
                JOIN course ON grade.course_id = course.course_id 
                WHERE grade.student_id IN (
                                            SELECT student_course.student_id 
                                            FROM student_course;);";
        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $studentId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $grades = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return $grades;
    }

    // New Method: Apply for a program
    public function applyForProgram(array $studentData): bool
    {
        $sql = "INSERT INTO applications (nrc, program_id, g12_grade, next_of_kin) VALUES (?,?,?,?)";
        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_bind_param($stmt, 'siss', $studentData['nrc'], $studentData['program_id'], 
                                            $studentData['g12_grade'], $studentData['next_of_kin']);
        
        if (mysqli_stmt_execute($stmt)) {
            return true;
        } else {
            throw new Exception("Failed to apply for program: " . mysqli_error($this->connection));
        }
    }
}
?>
