<?php
namespace SYS\CONTROLLER;
use SYS\DAO\StudentDAO;
use Exception;
use SYS\RES\StudentResHandler;

class StudentController {
    private StudentDAO $studentDao;
    private StudentResHandler $resHandler;

    public function __construct()
    {
        $this->studentDao = new StudentDAO();
        $this->resHandler = new StudentResHandler();
        
    }

    public function createStudent(array $studentData): bool {
        try {
            $result = $this->studentDao->createStudent($studentData);
            $this->resHandler->handleCreateStudent($result);
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
            // use response handler instead of throwing exception
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

    public function getStudentApplication($id){
        try{
            return $this->studentDao->getStudentApplicantion($id);
        }
        catch (Exception $e) {
            throw new Exception("Error fetching student applicant details: " . $e->getMessage());
        }
    }

    public function listStudentApplications(): array{
        try{
            return $this->studentDao->listStudentApplications();
        }
        catch (Exception $e) {
            throw new Exception("Error fetching student applications: " . $e->getMessage());
        }

    }

    // Method to update a student field (like personal details)
    public function updateStudentField(): bool
    {
        $userId=$_POST['user_id'];
        $field=$_POST['field'];
        $newValue=$_POST['newValue'];
        try {
            $result =$this->studentDao->updateStudentField($userId, $field, $newValue);
            $this->resHandler->handleUpdateStudent($result);
        } catch (Exception $e) {
            throw new Exception("Error updating student field: " . $e->getMessage());
        }
    }

    // Method to delete a student
    public function deleteStudent(int $userId): bool
    {
        try {
            $result = $this->studentDao->deleteStudent($userId);
            $this->resHandler->handleDeleteStudent($result);
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
            $result = $this->studentDao->applyForProgram($applicantData);
            $this->resHandler->handleApplyForProgram($result);
        } catch (Exception $e) {
            throw new Exception("Error applying for program: " . $e->getMessage());
        }
    }
}
?>
