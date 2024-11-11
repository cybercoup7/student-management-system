<?php
namespace SYS\CONTROLLER;
require_once  __DIR__ . '/../dao/DAO.php';
use SYS\DAO\DAO;
use Exception;

class UserController
{
    private DAO $userDao;

    public function __construct()
    {
        $this->userDao = new DAO();
    }

    public function getUserDetails(int $userId): array
    {
        $user = $this->userDao->fetchUser($userId);
        
        if (!$user) {
            throw new Exception("User not found");
        }
        
        return $user;
    }

    // Additional user-related methods can be added here
}
