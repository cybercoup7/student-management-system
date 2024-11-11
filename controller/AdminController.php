<?php
namespace SYS\CONTROLLER;
use SYS\DAO\AdminDAO;

class AdminController {
    private AdminDAO $adminDao;

    public function __construct()
    {
        $this->adminDao = new AdminDAO();
    }

    // Create a new admin
    public function createAdmin($adminData): bool
    {
        return $this->adminDao->createAdmin($adminData);
    }

    // Get details of a specific admin
    public function getAdmin(int $userId): array
    {
        return $this->adminDao->getAdmin($userId);
    }

    // Update an admin field
    public function updateAdminField(int $userId, string $field, $newValue): bool
    {
        return $this->adminDao->updateAdminField($userId, $field, $newValue);
    }

    // List all admins
    public function listAllAdmins(): array
    {
        return $this->adminDao->listAllAdmins();
    }

    // Delete an admin record
    public function deleteAdmin(int $userId): bool
    {
        return $this->adminDao->deleteAdmin($userId);
    }

    public function approveStudentApplication(int $id){
        return $this->adminDao->executeStudentApprovalTransaction($id);
    }

}
?>
