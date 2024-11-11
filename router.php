<?php
namespace SYS;
require_once __DIR__ . '/controller/AuthController.php'; // Use autoloading for multiple controllers in the future

use SYS\CONTROLLER\AuthController;

$baseNamespace = 'SYS\\CONTROLLER\\';
// Get controller and action from URL or set defaults
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'User';
$action = isset($_GET['action']) ? $_GET['action'] : 'showUserProfile';

// Create controller instance
// Build the fully qualified class name (e.g., SYS\Controllers\AuthController)
$controllerClass = $baseNamespace . $controller . 'Controller';
if (class_exists($controllerClass, false)) {
    $controllerInstance = new $controllerClass();
    if (method_exists($controllerInstance, $action)) {
        // Call the method with parameters if provided
        $controllerInstance->$action();
    } else {
        echo "Action $action not found!";
    }
} else {
    echo "Controller $controllerClass not found!";
}
