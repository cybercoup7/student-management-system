<?php
namespace SYS\CONTROLLER;
use SYS\DAO\DAO;

class AuthController
{
    private const HASH_ALGORITHM = 'sha256';
    private string $hashKey;
    private DAO $authDao;

    public function __construct()
    {
        $this->hashKey = "nadjndakjnqiury319ou30eada";
        $this->authDao = new Dao();
    }

    private function generateToken(int $userId): string
    {
        $timestamp = time();
        $hash = hash_hmac(self::HASH_ALGORITHM, $userId . $timestamp, $this->hashKey);
        return $userId . '.' . $timestamp . '.' . $hash;
    }


    public function register(){
        // $user->password = password_hash($user->password, PASSWORD_DEFAULT);
        $user = $_POST;
        $this->authDao->insertUser($user);
    }

    public function login()
    {
        $userId = $_POST['user_id'];
        $password =$_POST['password'];

        $user = $this->authDao->fetchUser($userId);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            die("Invalid Credentials");
        }

        $token = $this->generateToken($user['user_id']);
        $this->authDao->setUserToken($user['user_id'], $token);

        setcookie('auth-token', $token, (time()+3600), null, null, false, false);

        header(
          "Location: tests/dashboard.php"  
        );
        exit;
        // Should redirect to some protected url
    }

    public function isAuthenticated(): bool
    {
        $token = $_COOKIE['auth-token'] ?? '';
        // Split token into parts
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;  // Invalid token format
        }
    
        [$userId, $timestamp, $hash] = $parts;
    
        $expectedHash = hash_hmac(self::HASH_ALGORITHM, $userId . $timestamp, $this->hashKey);
    
        if (!hash_equals($expectedHash, $hash)) {
            return false;  // Invalid token hash
        } 
        $user = $this->authDao->fetchUser((int) $userId);
    
        return $user !== null;  // Return true if user exists and token is valid
    }
    

    public function logout(): bool
    {
        $token = $_COOKIE['auth-token'] ?? '';
        $isSuccess = $this->authDao->deleteUserToken($token);
        if ($isSuccess) {
            setcookie('auth-token', '', (time()-3600), null, null, false, false);
            return true;
        }
        // ELSE SEND ALERT OF LOG OUT FAILURE AND EXIT
        return false;
    }
}

?>