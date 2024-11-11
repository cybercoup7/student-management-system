<?php
namespace SYS\DAO;

use mysqli;
use Exception;

class CourseDAO {
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

    // Create a new course record
    public function createCourse(array $courseData): bool
    {
        $sql = "INSERT INTO course (course_name, description, dept_id) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_bind_param($stmt, 'ssi', $courseData['course_name'], $courseData['description'], $courseData['dept_id']);

        if (mysqli_stmt_execute($stmt)) {
            return true;
        } else {
            throw new Exception("Failed to create course: " . mysqli_error($this->connection));
        }
    }

    // Read a course's details by course ID
    public function getCourse(int $courseId): array
    {
        $sql = "SELECT course.course_id, course.course_name, course.description, 
                       course.dept_id, dept.dept_name
                FROM course 
                JOIN dept ON course.dept_id = dept.dept_id 
                WHERE course.course_id = ?";

        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $courseId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $course = mysqli_fetch_assoc($result) ?: [];

        return $course;
    }

    public function getCourseStudents(int $courseId): array
{
    $sql = "SELECT user.user_id, user.f_name, user.l_name, student.program
            FROM student_course
            JOIN user ON student_course.user_id = user.user_id
            JOIN student ON user.user_id = student.user_id
            WHERE student_course.course_id = ?";
    $stmt = mysqli_prepare($this->connection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $courseId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $students = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $students;
}

    // Update a course's specific field
    public function updateCourseField(int $courseId, string $field, $newValue): bool
    {
        $allowedFields = ['course_name', 'description', 'dept_id'];

        if (!in_array($field, $allowedFields)) {
            throw new Exception("Invalid field: $field");
        }

        $sql = "UPDATE course SET $field = ? WHERE course_id = ?";
        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_bind_param($stmt, is_int($newValue) ? 'ii' : 'si', $newValue, $courseId);

        if (mysqli_stmt_execute($stmt)) {
            return true;
        } else {
            throw new Exception("Failed to update course field: " . mysqli_error($this->connection));
        }
    }

    // Delete a course by course ID
    public function deleteCourse(int $courseId): bool
    {
        $sql = "DELETE FROM course WHERE course_id = ?";
        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $courseId);

        if (mysqli_stmt_execute($stmt)) {
            return true;
        } else {
            throw new Exception("Failed to delete course: " . mysqli_error($this->connection));
        }
    }

    // List all courses with their details
    public function listAllCourses(): array
    {
        $sql = "SELECT course.course_id, course.course_name, course.description, 
                       course.dept_id, dept.dept_name
                FROM course 
                JOIN dept ON course.dept_id = dept.dept_id";

        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $courses = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return $courses;
    }
}
?>
