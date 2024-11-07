<?php
namespace SYS\AUTHS;
use Exception;

use SYS\DAO\DAO;

class AuthController
{
    private const HASH_ALGORITHM = 'sha256';
    private string $hashKey;
    private DAO $userDAO;

    public function __construct(string $hashKey, DAO $userDAO)
    {
        $this->hashKey = $hashKey;
        $this->userDAO = $userDAO;
    }

    private function generateToken(int $userId): string
    {
        $timestamp = time();
        $hash = hash_hmac(self::HASH_ALGORITHM, $userId . $timestamp, $this->hashKey);
        return $userId . '.' . $timestamp . '.' . $hash;
    }


    public function register($user){
        // $user->password = password_hash($user->password, PASSWORD_DEFAULT);
        $this->userDAO->insertUser($user);
    }

    public function login(int $userId, string $password): string
    {
        $user = $this->userDAO->fetchUser($userId);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new Exception('Invalid credentials');
        }

        $token = $this->generateToken($user['user_id']);
        $this->userDAO->setUserToken($user['user_id'], $token);

        return $token;
    }

    public function isAuthenticated(string $token): bool
    {
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
    
        $user = $this->userDAO->fetchUser((int) $userId);
    
        return $user !== null;  // Return true if user exists and token is valid
    }
    

    public function logout(string $token): bool
    {
        return $this->userDAO->deleteUserToken($token);
    }
}

?>