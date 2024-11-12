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

    public function createStudent(array $studentData = []): bool {
        if(empty($studentData)){
            $studentData = $_POST;
        }
        try {
            $result = $this->studentDao->createStudent($studentData);
            $this->resHandler->handleCreateStudent($result);
        }
        catch (Exception $e) {
            die($e->getMessage());
            // return false;
            }
    }
    // Method to get student details
    public function getStudentDetails(int $userId): array
    {
        try {
            return $this->studentDao->getStudent($userId);
        } catch (Exception $e) {
            // use response handler instead of throwing exception
            die($e->getMessage());
        }
    }

    // Method to list courses a student is enrolled in
    public function viewEnrolledCourses(int $userId): array
    {
        try {
            return $this->studentDao->getStudentCourses($userId);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function getStudentApplication($id){
        try{
            return $this->studentDao->getStudentApplicantion($id);
        }
        catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function listStudentApplications(): array{
        try{
            return $this->studentDao->listStudentApplications();
        }
        catch (Exception $e) {
            die($e->getMessage());
        }

    }

    // Method to update a student field (like personal details)
    public function updateStudentField(int $userId = -1, string $field ="", string $newValue = ""): bool
    {
        if($userId== -1 && $field==="" && $newValue==="")
        {
            $userId=$_POST['user_id'];
            $field=$_POST['field'];
            $newValue=$_POST['newValue'];
        }
        
        try {
            $result =$this->studentDao->updateStudentField($userId, $field, $newValue);
            $this->resHandler->handleUpdateStudent($result);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    // Method to delete a student
    public function deleteStudent(int $userId): bool
    {
        try {
            $result = $this->studentDao->deleteStudent($userId);
            $this->resHandler->handleDeleteStudent($result);
        } catch (Exception $e) {
           die($e->getMessage());
        }
    }

    // Method to retrieve a list of all students
    public function listAllStudents(): array
    {
        try {
            return $this->studentDao->listAllStudents();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    //View grades for a student
    public function viewGrades($userId): array
    {
        try {
            return $this->studentDao->getStudentGrades($userId);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    //Apply for a program
    public function applyForProgram(array $applicationData = []): bool
    {
        if(empty($applicantData)){
            $applicantData = $_POST;
        }
        try {
            $result = $this->studentDao->applyForProgram($applicantData);
            $this->resHandler->handleApplyForProgram($result);
        } catch (Exception $e) {
             die($e->getMessage());
        }
    }
}
?>
