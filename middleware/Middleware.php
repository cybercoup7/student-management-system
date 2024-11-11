<?php
namespace SYS\MIDDLEWARE;

class Middleware {
    // Define access permissions for each role
    private static $permissions = [
        'student' => [
            'scripts' => ['viewProfile','students','studentHome', 'index.php'],// Scripts students can access
            'controllers' => ['StudentController'],     // Controllers students can access
        ],
        'lecturer' => [
            'scripts' => ['manageCourses'],
            'controllers' => ['LecturerController'],
        ],
        'admin' => [
            'scripts' => ['dashboard'],
            'controllers' => ['AdminController'],
        ]
    ];

    // Check if the user has permission to access the requested client script
    public static function canAccessScript($role, $script) {
        return in_array($script, self::$permissions[$role]['scripts'] ?? []);
    }

    // Check if the user has permission to access the requested controller
    public static function canAccessController($role, $controller) {
        return in_array($controller, self::$permissions[$role]['controllers'] ?? []);
    }
}
