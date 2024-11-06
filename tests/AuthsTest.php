<?php
require_once __DIR__ . '/../dao/DAO.php';
require_once __DIR__ . '/../controller/AuthController.php';

use SYS\AUTHS\AuthController as Auths;
use SYS\DAO\DAO;

class AuthsTest
{
    private $dao;

    private $auth;
    private $testUser;

    private $hashKey = 'secretHashKey';

    public function __construct()
    {
        $this->dao = new DAO();
        $this->auth = new Auths($this->hashKey, $this->dao);
        $this->testUser = (object)[
            'user_id' => 1,
            'password' => 'password123',
            'f_name' => 'f_test',
            'l_name' => 'l_test'
        ];
    }

    public function testRegistration()
    {
        try {
            $this->auth->register($this->testUser);
            echo "Registration test passed.\n";
        } catch (Exception $e) {
            echo "Registration test failed: " . $e->getMessage() . "\n";
        }
    }

    public function testLogin()
    {
        try {
            $token = $this->auth->login($this->testUser->user_id, $this->testUser->password);
            if ($token) {
                echo "Login test passed.\n";
            } else {
                echo "Login test failed.\n";
            }
        } catch (Exception $e) {
            echo "Login test failed: " . $e->getMessage() . "\n";
        }
    }

    public function testTokenValidation()
    {
        try {
            $token = $this->auth->login($this->testUser->user_id, $this->testUser->password);
            if ($this->auth->isAuthenticated($token)) {
                echo "Token validation test passed.\n";
            } else {
                echo "Token validation test failed.\n";
            }
        } catch (Exception $e) {
            echo "Token validation test failed: " . $e->getMessage() . "\n";
        }
    }

    public function testLogout()
    {
        try {
            $token = $this->auth->login($this->testUser->user_id, $this->testUser->password);
            if ($this->auth->logout($token)) {
                echo "Logout test passed.\n";
            } else {
                echo "Logout test failed.\n";
            }
        } catch (Exception $e) {
            echo "Logout test failed: " . $e->getMessage() . "\n";
        }
    }

    public function testDuplicateRegistration()
    {
        try {
            $this->auth->register($this->testUser); // First registration
            $this->auth->register($this->testUser); // Duplicate registration
            echo "Duplicate registration test failed.\n";
        } catch (Exception $e) {
            echo "Duplicate registration test passed: " . $e->getMessage() . "\n";
        }
    }
    
    public function testInvalidLogin()
    {
        try {
            $invalidPassword = 'wrongpassword';
            $this->auth->login($this->testUser->user_id, $invalidPassword);
            echo "Invalid login test failed.\n";
        } catch (Exception $e) {
            echo "Invalid login test passed: " . $e->getMessage() . "\n";
        }
    }
    
    public function testInvalidTokenValidation()
    {
        try {
            $invalidToken = 'invalid.token.value';
            if (!$this->auth->isAuthenticated($invalidToken)) {
                echo "Invalid token validation test passed.\n";
            } else {
                echo "Invalid token validation test failed.\n";
            }
        } catch (Exception $e) {
            echo "Invalid token validation test failed: " . $e->getMessage() . "\n";
        }
    }
    
    public function testLogoutNonexistentToken()
    {
        try {
            $nonExistentToken = 'non.existent.token';
            if (!$this->auth->logout($nonExistentToken)) {
                echo "Logout nonexistent token test passed.\n";
            } else {
                echo "Logout nonexistent token test failed.\n";
            }
        } catch (Exception $e) {
            echo "Logout nonexistent token test failed: " . $e->getMessage() . "\n";
        }
    }
    
    public function runTests()
    {
        $this->testRegistration();
        $this->testLogin();
        $this->testTokenValidation();
        $this->testLogout();
        $this->testDuplicateRegistration();
        $this->testInvalidLogin();
        $this->testInvalidTokenValidation();
        $this->testLogoutNonexistentToken();
    }
    
}

$test = new AuthsTest();
$test->runTests();
?>