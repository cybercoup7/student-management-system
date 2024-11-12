<?php
namespace SYS;

use SYS\MIDDLEWARE\Middleware;

class Router {
    public function route() {
        session_start();
        $role = $_SESSION['role'] ?? 'student';
        $folder = $_GET['folder']?? Null;
        // Check if a client script is requested
        if (isset($_GET['script'])) {
            $this->runClientScript($role, $_GET['script'], $folder);
            return;
        }


        // Otherwise, proceed with controller-based routing
        $controller = isset($_GET['controller']) ? ucfirst($_GET['controller']) . 'Controller' : Null;
        $action = isset($_GET['action']) ? $_GET['action'] : Null;

        if(!isset($controller)){
            $this->runClientScript('student', 'studentHome', $folder);
            return;
        }


        if (!Middleware::canAccessController($role, $controller)) {
            http_response_code(403);
            echo "Access Denied: Unauthorized access for the role '$role'!";
            return;
        }

        $controllerClass = "SYS\\CONTROLLER\\" . $controller;

        if (class_exists($controllerClass)) {
            $controllerInstance = new $controllerClass();
            if (method_exists($controllerInstance, $action)) {
                $controllerInstance->$action();
            } else {
                echo "Action '$action' not found!";
            }
        } else {
            echo "Controller '$controllerClass' not found!";
        }
    }

    private function runClientScript($role, $scriptName, $folder) {
        //check if role can access the requested script
        if (Middleware::canAccessScript($role, $scriptName)) {
            $role_folder = $role. 's';
            $scriptFolder = $folder??$role_folder;
            $scriptPath = __DIR__."/client/$scriptFolder/$scriptName.php";
            if (file_exists($scriptPath)) {
                require $scriptPath;
            } else {
                echo "Script '$scriptName' not found in role folder '$role'! full path: $scriptPath";
            }
        } else {
            echo "Access denied to '$scriptName' for role '$role'.";
        }
    }
}
