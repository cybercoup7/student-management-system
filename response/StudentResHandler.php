<?php
namespace SYS\RES;

use SYS\Constants;


class StudentResHandler {
    public $base_path;
    public function __construct(){
        $this->base_path = Constants::BASE_PATH;
    }

    public function handleCreateStudent($result) {
        if ($result) {
            
            header("Location:/$this->base_path/index.php?script=studentHome&status=created");
        } else {
            header("Location: /index.php?script=studentHome&status=create_failed");
        }
        exit;
    }

    public function handleGetStudentDetails($result) {
        if (!empty($result)) {
            require __DIR__ . "/../client/students/studentDetails.php";
        } else {
            echo "Error fetching student details.";
        }
    }

    public function handleViewEnrolledCourses($result) {
        if (!empty($result)) {
            require __DIR__ . "/../client/students/enrolledCourses.php";
        } else {
            echo "Error fetching enrolled courses.";
        }
    }

    public function handleUpdateStudent($result) {
        if ($result) {
            header("Location: /$this->base_path/index.php?script=studentHome&status=updated");
        } else {
            header("Location: /$this->base_path/index.php?script=studentHome&status=update_failed");
        }
        exit;
    }

    public function handleDeleteStudent($result) {
        if ($result) {
            header("Location: /$this->base_path/index.php?script=studentHome&status=deleted");
        } else {
            header("Location: /$this->base_path/index.php?script=studentHome&status=delete_failed");
        }
        exit;
    }

    public function handleListAllStudents($result) {
        if (!empty($result)) {
            require __DIR__ . "/../client/students/studentHome.php";
        } else {
            echo "Error fetching student list.";
        }
    }

    public function handleViewGrades($result) {
        if (!empty($result)) {
            require __DIR__ . "/../client/students/grades.php";
        } else {
            echo "Error fetching grades.";
        }
    }

    public function handleApplyForProgram($result) {
        if ($result) {
            header("Location: /$this->base_path/index.php?script=application&status=applied");
        } else {
            header("Location: /$this->base_path/index.php?script=application&status=apply_failed");
        }
        exit;
    }
}
