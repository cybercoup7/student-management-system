<?php
namespace SYS\CONTROLLER;

use SYS\DAO\StudentDAO;
use Exception;

class StudentController {
    private StudentDAO $studentDao;

    public function __construct()
    {
        $this->studentDao = new StudentDAO();
    }

    public function createStudent(array $studentData): bool {
        try {
            return $this->studentDao->createStudent($studentData);
        }
        catch (Exception $e) {
            return false;
            }
    }
    // Method to get student details
    public function getStudentDetails(int $userId): array
    {
        try {
            return $this->studentDao->getStudent($userId);
        } catch (Exception $e) {
            throw new Exception("Error fetching student details: " . $e->getMessage());
        }
    }

    // Method to list courses a student is enrolled in
    public function viewEnrolledCourses(int $userId): array
    {
        try {
            return $this->studentDao->getStudentCourses($userId);
        } catch (Exception $e) {
            throw new Exception("Error fetching enrolled courses: " . $e->getMessage());
        }
    }

    // Method to update a student field (like personal details)
    public function updateStudentField(int $userId, string $field, $newValue): bool
    {
        try {
            return $this->studentDao->updateStudentField($userId, $field, $newValue);
        } catch (Exception $e) {
            throw new Exception("Error updating student field: " . $e->getMessage());
        }
    }

    // Method to delete a student
    public function deleteStudent(int $userId): bool
    {
        try {
            return $this->studentDao->deleteStudent($userId);
        } catch (Exception $e) {
            throw new Exception("Error deleting student: " . $e->getMessage());
        }
    }

    // Method to retrieve a list of all students
    public function listAllStudents(): array
    {
        try {
            return $this->studentDao->listAllStudents();
        } catch (Exception $e) {
            throw new Exception("Error fetching student list: " . $e->getMessage());
        }
    }

    //View grades for a student
    public function viewGrades($userId): array
    {
        try {
            return $this->studentDao->getStudentGrades($userId);
        } catch (Exception $e) {
            throw new Exception("Error fetching grades: " . $e->getMessage());
        }
    }

    //Apply for a program
    public function applyForProgram(): bool
    {
        $applicantData = $_POST;
        try {
            return $this->studentDao->applyForProgram($applicantData);
        } catch (Exception $e) {
            throw new Exception("Error applying for program: " . $e->getMessage());
        }
    }
}
?>
