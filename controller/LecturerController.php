<?php
namespace SYS\CONTROLLER;
use SYS\DAO\LecturerDAO;
use Exception;

class LecturerController extends StudentController {
    private LecturerDAO $lecturerDao;

    public function __construct()
    {
        $this->lecturerDao = new LecturerDAO();
    }

    // Create a new lecturer
    public function createLecturer(array $lecturerData): bool
    {
        return $this->lecturerDao->createLecturer($lecturerData);
    }

    // Get details of a specific lecturer
    public function getLecturer(int $userId): array
    {
        return $this->lecturerDao->getLecturer($userId);
    }

    // Update a lecturer's field
    public function updateLecturerField(int $userId, string $field, $newValue): bool
    {
        return $this->lecturerDao->updateLecturerField($userId, $field, $newValue);
    }

    // Enroll a student in a course
    public function enrollStudentInCourse(int $studentId, int $courseId): bool
    {
        return $this->lecturerDao->enrollStudentInCourse($studentId, $courseId);
    }

    // Post a grade for a student
    public function postGrade(int $studentId, int $courseId, string $grade): bool
    {
        return $this->lecturerDao->postGrade($studentId, $courseId, $grade);
    }

    // List all students in a course
    public function listStudentsInCourse(int $courseId): array
    {
        return $this->lecturerDao->getStudentsInCourse($courseId);
    }

    // List all lecturers
    public function listAllLecturers(): array
    {
        return $this->lecturerDao->listAllLecturers();
    }

    // Delete a lecturer record
    public function deleteLecturer(int $userId): bool
    {
        return $this->lecturerDao->deleteLecturer($userId);
    }
}
?>
