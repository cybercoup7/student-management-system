<?php
require_once __DIR__ . '/../../controller/AuthController.php';

use SYS\CONTROLLER\AuthController;

$auth = new AuthController();

if(!$auth->isAuthenticated()){
    header('Location: http://127.0.0.1/student-management-system-main/tests/login.html');
    exit;
}


?>